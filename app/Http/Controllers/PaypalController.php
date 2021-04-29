<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\InvoicePayment;
use App\Utility;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PaypalController extends Controller
{
    private $_api_context;

    public function setApiContext()
    {
        $user = Auth::user();

        $settings = Utility::settings();

        $paypal_conf = config('paypal');

        if($user->type == 'client')
        {
            $paypal_conf['settings']['mode'] = $settings['site_paypal_mode'];
            $paypal_conf['client_id']        = $settings['site_paypal_client_id'];
            $paypal_conf['secret_key']       = $settings['site_paypal_secret_key'];
        }

        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'], $paypal_conf['secret_key']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function clientPayWithPaypal(Request $request, $invoice_id)
    {
        $settings = Utility::settings();

        $get_amount = $request->amount;

        $request->validate(['amount' => 'required|numeric|min:0']);

        $invoice = Invoice::find($invoice_id);

        if($invoice)
        {
            if($get_amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else
            {
                $this->setApiContext();

                $name = $settings['company_name'] . " - " . Utility::invoiceNumberFormat($invoice->invoice_id);

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item_1 = new Item();
                $item_1->setName($name)->setCurrency($settings['site_currency'])->setQuantity(1)->setPrice($get_amount);

                $item_list = new ItemList();
                $item_list->setItems([$item_1]);

                $amount = new Amount();
                $amount->setCurrency($settings['site_currency'])->setTotal($get_amount);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(route('client.get.payment.status', $invoice->id))->setCancelUrl(route('client.get.payment.status', $invoice->id));

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if(\Config::get('app.debug'))
                    {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Connection timeout'));
                    }
                    else
                    {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }
                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Unknown error occurred'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function clientGetPaymentStatus(Request $request, $invoice_id)
    {
        $user = Auth::user();

        $invoice = Invoice::find($invoice_id);

        if($invoice)
        {
            $this->setApiContext();

            $payment_id = Session::get('paypal_payment_id');

            Session::forget('paypal_payment_id');

            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Payment failed'));
            }

            $payment   = Payment::get($payment_id, $this->_api_context);

            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);

            try
            {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $status = ucwords(str_replace('_', ' ', $result['state']));

                if($result['state'] == 'approved')
                {
                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->transaction_id =  app('App\Http\Controllers\InvoiceController')->transactionNumber();
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->amount = $result['transactions'][0]['amount']['total'];
                    $invoice_payment->date = date('Y-m-d');
                    $invoice_payment->payment_id = 0;
                    $invoice_payment->payment_type = __('PAYPAL');
                    $invoice_payment->client_id = $user->id;
                    $invoice_payment->notes = '';
                    $invoice_payment->save();

                    if(($invoice->getDue() - $invoice_payment->amount) == 0) {
                        $invoice->status = 'paid';
                        $invoice->save();
                    }

                    return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                }
                else
                {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been ' . $status));
                }

            } catch(\Exception $e) {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been failed!'));
            }
        } else {
            return redirect()->back()->with('error',__('Permission denied.'));
        }
    }
}
