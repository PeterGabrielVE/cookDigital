@extends('layouts.admin')
@section('page-title')
    {{__('Manage Payment')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable">
                            <thead>
                            <tr>
                                <th> {{__('Transaction ID')}}</th>
                                <th> {{__('Invoice')}}</th>
                                <th> {{__('Payment Date')}}</th>
                                <th> {{__('Payment Method')}}</th>
                                <th> {{__('Payment Type')}}</th>
                                <th> {{__('Note')}}</th>
                                <th> {{__('Amount')}}</th>
                                @if(Gate::check('show invoice') || \Auth::user()->type=='client')
                                    <th>{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{sprintf("%05d", $payment->transaction_id)}}</td>
                                    <td>{{ Utility::invoiceNumberFormat($payment->invoice->invoice_id) }}</td>
                                    <td>{{ Auth::user()->dateFormat($payment->date) }}</td>
                                    <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td>
                                    <td>{{$payment->payment_type}}</td>
                                    <td>{{$payment->notes}}</td>
                                    <td>{{Auth::user()->priceFormat($payment->amount)}}</td>
                                    @if(Gate::check('show invoice') || \Auth::user()->type=='client')
                                        <td class="Action">
                                            <span>
                                                <a href="{{ route('invoices.show',$payment->invoice->id) }}" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="{{__('Invoice Detail')}}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
