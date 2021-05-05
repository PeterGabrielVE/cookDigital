<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\Invoice;
use App\InvoicePayment;
use App\InvoiceProduct;
use App\Mail\CustomInvoiceSend;
use App\Mail\InvoiceSend;
use App\Mail\PaymentReminder;
use App\Milestone;
use App\Payment;
use App\Products;
use App\Task;
use App\Tax;
use App\User;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Stripe;

class InvoiceController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage invoice') || Auth::user()->type == 'client')
        {
            if(Auth::user()->type == 'client')
            {
                $invoices = Invoice::select(['invoices.*'])->join('projects', 'projects.id', '=', 'invoices.project_id')->where('projects.client', '=', Auth::user()->id)->where('invoices.created_by', '=', Auth::user()->creatorId())->get();
            }
            else
            {
                $invoices = Invoice::where('created_by', '=', Auth::user()->creatorId())->get();
            }

            return view('invoices.index')->with('invoices', $invoices);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {

        if(Auth::user()->can('create invoice'))
        {
            $taxes    = Tax::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $projects = Auth::user()->projects->pluck('name', 'id');

            return view('invoices.create', compact('projects', 'taxes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('create invoice'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   // 'project_id' => 'required',
                                   'issue_date' => 'required',
                                   'due_date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('invoices.index')->with('error', $messages->first());
            }

            $invoice             = new Invoice();
            $invoice->invoice_id = $this->invoiceNumber();
            $invoice->status     = 0;
            $invoice->issue_date = $request->issue_date;
            $invoice->due_date   = $request->due_date;
            $invoice->discount   = 0;
            $invoice->terms      = $request->terms;

            if(isset($request->project_id) && !empty($request->project_id))
            {
                $invoice->project_id = $request->project_id;
            }
            if(isset($request->tax_id) && !empty($request->tax_id))
            {
                $invoice->tax_id = $request->tax_id;
            }
            $invoice->created_by = Auth::user()->creatorId();
            $invoice->save();

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->creatorId(),
                    'project_id' => (isset($request->project_id) && !empty($request->project_id)) ? $request->project_id : 0,
                    'log_type' => 'Create Invoice',
                    'remark' => sprintf(__('%s Create new invoice "%s"'), Auth::user()->name, Auth::user()->invoiceNumberFormat($invoice->invoice_id)),
                ]
            );

            return redirect()->route('invoices.show', $invoice->id)->with('success', __('Invoice successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function show(Invoice $invoice)
    {
        if(Auth::user()->can('show invoice') || Auth::user()->type == 'client')
        {
            if($invoice->created_by == Auth::user()->creatorId())
            {
                if(Auth::user()->type == 'client' && $invoice->project->client == Auth::user()->id)
                {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }

                $settings = Utility::settings();
                $client   = $invoice->project->client;
                if($client != 0)
                {
                    $user = User::where('id', $client)->first();
                }
                else
                {
                    $user = '';
                }

                return view('invoices.view', compact('invoice', 'settings', 'user'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Invoice $invoice)
    {
        if(Auth::user()->can('edit invoice'))
        {
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $taxes    = Tax::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $projects = Auth::user()->projects->pluck('name', 'id');

                return view('invoices.edit', compact('invoice', 'projects', 'taxes'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
        if(Auth::user()->can('edit invoice'))
        {

            if($invoice->created_by == Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'project_id' => 'required',
                                       'issue_date' => 'required',
                                       'due_date' => 'required',
                                       'discount' => 'required|min:0',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.index')->with('error', $messages->first());
                }

                $invoice->project_id = $request->project_id;
                $invoice->issue_date = $request->issue_date;
                $invoice->due_date   = $request->due_date;
                $invoice->tax_id     = $request->tax_id;
                $invoice->terms      = $request->terms;
                $invoice->discount   = $request->discount;
                $invoice->save();

                return redirect()->back()->with('success', __('Invoice successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Invoice $invoice)
    {

        if(Auth::user()->can('delete invoice'))
        {
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $invoice->delete();
                InvoicePayment::where('invoice_id', '=', $invoice->id)->delete();
                InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();

                return redirect()->route('invoices.index')->with('success', __('Invoice successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productAdd($id)
    {
        if(Auth::user()->can('create invoice product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $products   = Products::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $milestones = Milestone::where('project_id', $invoice->project_id)->get();
                $tasks      = Task::where('project_id', $invoice->project_id)->get();

                return view('invoices.product', compact('invoice', 'milestones', 'tasks'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function productStore($id, Request $request)
    {
        if(Auth::user()->can('create invoice product'))
        {
            $invoice = Invoice::find($id);

            if($invoice->getTotal() == 0.0)
            {
                Invoice::change_status($invoice->id, 1);
            }

            if($invoice->created_by == Auth::user()->creatorId())
            {
                if($request->type == 'milestone')
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'milestone_id' => 'required',
                                       ]
                    );
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('invoices.show', $invoice->id)->with('error', __('Please select milestone or task.'));

                    }

                    $task      = Task::find($request->task_id);
                    $milestone = Milestone::find($request->milestone_id);
                    $item      = (!empty($task->title) ? $task->title : '') . '-' . (!empty($milestone->title) ? $milestone->title : '');
                    $price     = $request->price;
                }
                else
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'title' => 'required',
                                           'price' => 'required',
                                       ]
                    );
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('invoices.show', $invoice->id)->with('error', __('title and price filed are required'));

                    }

                    $item  = $request->title;
                    $price = $request->price;
                }


                InvoiceProduct::create(
                    [
                        'invoice_id' => $invoice->id,
                        'iteam' => $item,
                        'price' => $price,
                        'type' => $request->type,
                    ]
                );

                if($invoice->getTotal() > 0.0 || $invoice->getDue() < 0.0)
                {
                    Invoice::change_status($invoice->id, 2);
                }

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully added.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productEdit($id, $product_id)
    {
        if(Auth::user()->can('edit invoice product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $product  = InvoiceProduct::find($product_id);
                $products = Products::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('invoices.product', compact('invoice', 'products', 'product'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function productUpdate($id, $product_id, Request $request)
    {


        if(Auth::user()->can('edit invoice product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'product_id' => 'required',
                                       'quantity' => 'required|numeric|min:1',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                $product                     = Products::find($request->product_id);
                $invoiceProduct              = InvoiceProduct::find($product_id);
                $invoiceProduct->product_id  = $product->id;
                $invoiceProduct->price       = $product->price;
                $invoiceProduct->quantity    = $request->quantity;
                $invoiceProduct->description = $request->description;
                $invoiceProduct->save();

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productDelete($id, $product_id)
    {


        if(Auth::user()->can('delete invoice product'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $invoiceProduct = InvoiceProduct::find($product_id);
                $invoiceProduct->delete();
                if($invoice->getDue() <= 0.0)
                {
                    Invoice::change_status($invoice->id, 3);
                }

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Product successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function milestoneTask(Request $request)
    {
        if(!empty($request->milestone_id))
        {
            $tasks = Task::where('milestone_id', $request->milestone_id)->get();

            return $tasks;
        }
        else
        {
            $tasks = Task::where('project_id', $request->project_id)->get();

            return $tasks;
        }

    }

    public function paymentAdd($id)
    {
        if(Auth::user()->can('create invoice payment'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $payment_methods = Payment::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('invoices.payment', compact('invoice', 'payment_methods'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function paymentStore($id, Request $request)
    {
        if(Auth::user()->can('create invoice payment'))
        {
            $invoice = Invoice::find($id);
            if($invoice->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'amount' => 'required|numeric|min:1',
                                       'date' => 'required',
                                       'payment_id' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                InvoicePayment::create(
                    [
                        'transaction_id' => $this->transactionNumber(),
                        'invoice_id' => $invoice->id,
                        'amount' => $request->amount,
                        'date' => $request->date,
                        'payment_id' => $request->payment_id,
                        'notes' => $request->notes,
                    ]
                );
                if($invoice->getDue() == 0.0)
                {
                    Invoice::change_status($invoice->id, 3);
                }
                else
                {
                    Invoice::change_status($invoice->id, 2);
                }

                return redirect()->route('invoices.show', $invoice->id)->with('success', __('Payment successfully added.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addPayment($id, Request $request)
    {

        $objUser = Auth::user();
        $invoice = Invoice::find($id);

        $settings = Utility::settings();

        if($invoice)
        {
            if($request->amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else
            {
                try
                {
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $price   = $request->amount;
                    Stripe\Stripe::setApiKey($settings['site_stripe_secret']);
                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => $settings['site_currency'],
                            "source" => $request->stripeToken,
                            "description" => $settings['company_name'] . " - " . Utility::invoiceNumberFormat($invoice->invoice_id),
                            "metadata" => ["order_id" => $orderID],
                        ]
                    );

                    if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                    {
                        InvoicePayment::create(
                            [
                                'transaction_id' => $this->transactionNumber(),
                                'invoice_id' => $invoice->id,
                                'amount' => $price,
                                'date' => date('Y-m-d'),
                                'payment_id' => 0,
                                'payment_type' => __('STRIPE'),
                                'client_id' => $objUser->id,
                                'notes' => '',
                            ]
                        );

                        if(($invoice->getDue() - $request->amount) == 0)
                        {
                            $invoice->status = 'paid';
                            $invoice->save();
                        }

                        return redirect()->back()->with('success', __(' Payment added Successfully'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Transaction has been failed!'));
                    }

                }
                catch(\Exception $e)
                {
                    return redirect()->route('invoices.show', $invoice->id)->with('error', __($e->getMessage()));
                }
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function transactionNumber()
    {
        $latest = InvoicePayment::select('invoice_payments.*')->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', Auth::user()->creatorId())->latest()->first();
        if($latest)
        {
            return $latest->transaction_id + 1;
        }

        return 1;
    }

    public function payments()
    {
        if(Auth::user()->can('manage invoice payment') || Auth::user()->type == 'client')
        {
            if(Auth::user()->type == 'client')
            {
                $payments = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->join('projects', 'invoices.project_id', '=', 'projects.id')->where('projects.client', '=', Auth::user()->id)->where(
                    'invoices.created_by', '=', Auth::user()->creatorId()
                )->get();
            }
            else
            {
                $payments = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', Auth::user()->creatorId())->get();
            }

            return view('invoices.all-payments', compact('payments'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function printInvoice($id)
    {
        if(Auth::user()->can('manage invoice'))
        {
            $invoiceId = Crypt::decrypt($id);
            $invoice   = Invoice::findOrFail($invoiceId);
            $settings  = Utility::settings();

            //Set your logo
            $logo         = asset(\Storage::url('logo/'));
            $company_logo = Utility::getValByName('company_logo');
            $invoice_logo = Utility::getValByName('invoice_logo');
            if(isset($invoice_logo) && !empty($invoice_logo))
            {
                $img = asset(\Storage::url('invoice_logo/') . $invoice_logo);
            }
            else
            {
                $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
            }

            if(empty($settings))
            {
                $settings = Utility::settings();
            }

            if(!empty($invoice->project))
            {
                $client = $invoice->project->client;
                if($client != 0)
                {
                    $user = User::where('id', $client)->first();
                }
                else
                {
                    $user = '';
                }
            }
            else
            {
                $user = '';
            }

            if($invoice)
            {
                $color      = '#' . $settings['invoice_color'];
                $font_color = Utility::getFontColor($color);

                return view('invoices.templates.' . $settings['invoice_template'], compact('invoice', 'color', 'settings', 'user', 'img', 'font_color'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function previewInvoice($template, $color)
    {
        $settings   = Utility::settings();
        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $invoice       = new Invoice();
        $user          = new \stdClass();
        $tax           = new \stdClass();
        $project       = new \stdClass();
        $user->name    = 'Client';
        $user->email   = 'client@example.com';
        $tax->name     = 'GST';
        $tax->rate     = 10;
        $project->name = 'Test Project';

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item        = new \stdClass();
            $item->iteam = 'Item ' . $i;;
            $item->price = 100;
            $items[]     = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->discount   = 50;
        $invoice->items      = $items;
        $invoice->tax        = $tax;
        $invoice->project    = $project;


        //Set your logo
        $logo         = asset(\Storage::url('logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = asset(\Storage::url('invoice_logo/') . $invoice_logo);
        }
        else
        {
            $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        }

        return view('invoices.templates.' . $template, compact('invoice', 'preview', 'color', 'settings', 'user', 'img', 'font_color'));
    }

    public function paymentReminder($invoice_id)
    {
        if(Auth::user()->can('payment reminder invoice'))
        {
            $invoice            = Invoice::find($invoice_id);
            $client             = !empty($invoice->project) ? $invoice->project->client() : '';
            $invoice->dueAmount = Auth:: user()->priceFormat($invoice->getDue());
            $invoice->name      = !empty($client) ? $client->name : 'Dear';
            $email              = !empty($client) ? $client->email : '';
            $invoice->date      = Auth::user()->dateFormat($invoice->issue_date);
            $invoice->invoice   = Auth::user()->invoiceNumberFormat($invoice->invoice_id);

            try
            {
                Mail::to($email)->send(new PaymentReminder($invoice));
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function sent($id)
    {
        /*if(Auth::user()->can('send invoice'))
        {*/
            $invoice          = Invoice::where('invoice_id', $id)->first();
            $client           = !empty($invoice->project) ? $invoice->project->client() : '';
            $invoice->name    = !empty($client) ? $client->name : 'Dear';
            $email            = !empty($client) ? $client->email : '';
            $invoice->invoice = Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $invoiceId        = Crypt::encrypt($invoice->invoice_id);
            $invoice->url     = route('get.invoice', $invoiceId);
            try
            {
                Mail::to($email)->send(new InvoiceSend($invoice));
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
       /* }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }*/
    }

    public function customMail($invoice_id)
    {
        if(Auth::user()->can('custom mail send invoice'))
        {
            return view('invoices.invoice_send', compact('invoice_id'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customMailSend(Request $request, $invoice_id)
    {
        if(Auth::user()->can('custom mail send invoice'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'email' => 'required|email',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $email            = $request->email;
            $invoice          = Invoice::where('invoice_id', $invoice_id)->first();
            $invoice->name    = Auth::user()->name;
            $invoice->invoice = Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $invoiceId        = Crypt::encrypt($invoice->invoice_id);
            $invoice->url     = route('get.invoice', $invoiceId);
            try
            {
                Mail::to($email)->send(new CustomInvoiceSend($invoice));
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
