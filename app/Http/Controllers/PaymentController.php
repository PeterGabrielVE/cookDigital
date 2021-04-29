<?php

namespace App\Http\Controllers;

use App\InvoicePayment;
use App\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage payment'))
        {
            $payments = Payment::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('payments.index')->with('payments', $payments);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create payment'))
        {
            return view('payments.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create payment'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('payments.index')->with('error', $messages->first());
            }

            $payment             = new Payment();
            $payment->name       = $request->name;
            $payment->created_by = \Auth::user()->creatorId();
            $payment->save();

            return redirect()->route('payments.index')->with('success', __('Payment successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(Payment $payment)
    {
        return redirect()->route('payments.index');
    }

    public function edit(Payment $payment)
    {
        if(\Auth::user()->can('edit payment'))
        {
            if($payment->created_by == \Auth::user()->creatorId())
            {
                return view('payments.edit', compact('payment'));
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


    public function update(Request $request, Payment $payment)
    {


        if(\Auth::user()->can('edit payment'))
        {
            if($payment->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('payments.index')->with('error', $messages->first());
                }
                $payment->name = $request->name;
                $payment->save();

                return redirect()->route('payments.index')->with('success', __('Payment successfully updated.'));
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

    public function destroy(Payment $payment)
    {
        if(\Auth::user()->can('delete payment'))
        {
            if($payment->created_by == \Auth::user()->creatorId())
            {
                InvoicePayment::where('payment_id', $payment->id)->update(array('payment_id' => 0));
                $payment->delete();

                return redirect()->route('payments.index')->with('success', __('Payment successfully deleted.'));
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
}
