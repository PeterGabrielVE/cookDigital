@extends('layouts.admin')

@section('page-title')
    {{__('Invoice Detail')}}
@endsection
@push('script-page')
    @php
        $settings = \App\Utility::settings();
        $dir_payment = asset(Storage::url('payments'));
    @endphp
    <script>
        function getTask(obj, project_id) {
            $('#task_id').empty();
            var milestone_id = obj.value;
            $.ajax({
                url: '{!! route('invoices.milestone.task') !!}',
                data: {
                    "milestone_id": milestone_id,
                    "project_id": project_id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                success: function (data) {
                    $('#task_id').empty();
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value=' + data[i].id + '>' + data[i].title + '</option>';
                    }
                    $('#task_id').append(html);
                    $('#task_id').select2('refresh');
                },
                error: function (data) {
                    data = data.responseJSON;
                    show_toastr('{{__("Error")}}', data.error, 'error')
                }
            });
        }

        function hide_show(obj) {
            if (obj.value == 'milestone') {
                document.getElementById('milestone').style.display = 'block';
                document.getElementById('other').style.display = 'none';
            } else {
                document.getElementById('other').style.display = 'block';
                document.getElementById('milestone').style.display = 'none';
            }
        }
    </script>
    @if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && $settings['site_enable_stripe'] == 'on')
        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript">
            var stripe = Stripe('{{ $settings['site_stripe_key'] }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        toastr('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        </script>
    @endif
@endpush

@push('css-page')
    <style>
        #card-element {
            border: 1px solid #e4e6fc;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
@endpush

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('create invoice payment')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoices.payments.create',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Add Payment')}}"><i class="fas fa-plus"></i> {{__('Add Payment')}}</a>
            </div>
        @endcan
        @if(\Auth::user()->type == 'client' && $invoice->getDue() > 0 && (($settings['site_enable_stripe'] == 'on' && !empty($settings['site_stripe_key']) && !empty($settings['site_stripe_secret'])) || ($settings['site_enable_paypal'] == 'on' && !empty($settings['site_paypal_client_id']) && !empty($settings['site_paypal_secret_key']))))
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-toggle="modal" data-target="#paymentModal"><i class="fas fa-plus"></i> {{__('Pay Now')}}</a>
            </div>
        @endif
        @can('edit invoice')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoices.edit',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Edit Invoice')}}" data-original-title="{{__('Edit')}}"><i class="fas fa-pencil-alt"></i> {{__('Edit')}}</a>
            </div>
        @endcan

            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{ route('invoice.sent',$invoice->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-reply"></i> {{__('Send Invoice Mail')}}</a>
            </div>


            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                <a href="{{ route('invoice.payment.reminder',$invoice->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-money-check"></i> {{__('Payment Reminder')}}</a>
            </div>

        <!-- can('custom mail send invoice') -->
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoice.custom.send',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Send Invoice')}}" title="{{__('send Invoice')}}"><i class="fas fa-pencil-alt"></i> {{__('Send Invoice Mail')}}</a>
            </div>
        <!-- endcan -->
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 col-12">
            <a href="{{ route('get.invoice',Crypt::encrypt($invoice->id)) }}" class="btn btn-xs bg-warning btn-white btn-icon-only width-auto" title="{{__('Print Invoice')}}" target="_blanks"><i class="fas fa-print"></i> {{__('Print')}}</a>
        </div>

    </div>
@endsection

@section('content')
    <div class="card">
        <div class="invoice-title">{{ Utility::invoiceNumberFormat($invoice->id) }}</div>
        <div class="invoice-detail">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail">
                        <strong>{{__('From')}} : </strong><br>
                        {{$settings['company_name']}}<br>
                        {{$settings['company_address']}}<br>
                        {{$settings['company_city']}}
                        @if(isset($settings['company_city']) && !empty($settings['company_city'])), @endif
                        {{$settings['company_state']}}
                        @if(isset($settings['company_zipcode']) && !empty($settings['company_zipcode']))-@endif {{$settings['company_zipcode']}}<br>
                        {{$settings['company_country']}}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail text-right float-right">
                        <strong>{{__('To')}}:</strong>
                        <div class="invoice-number">{{(!empty($user))?$user->name:''}}</div>
                        <div class="invoice-number">{{(!empty($user))?$user->email:''}}</div>
                    </div>
                </div>
            </div>
            <div class="status-section">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status"><strong>{{__('Status')}}:</strong>
                            <div class="font-weight-bold">
                                @if($invoice->status == 0)
                                    <span class="badge badge-pill badge-primary">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 1)
                                    <span class="badge badge-pill badge-danger">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 2)
                                    <span class="badge badge-pill badge-warning">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 3)
                                    <span class="badge badge-pill badge-success">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 4)
                                    <span class="badge badge-pill badge-info">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(!empty($invoice->project))
                        <div class="col-md-3 col-sm-6 col-6">
                            <div class="text-status text-right">{{__('Project')}}:
                                <strong>{{ (!empty($invoice->project)?$invoice->project->name:'') }}</strong>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right">{{__('Issue Date')}}:
                            <strong>{{ Auth::user()->dateFormat($invoice->issue_date) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right">{{__('Due Date')}}:
                            <strong>{{ Auth::user()->dateFormat($invoice->due_date) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="justify-content-between align-items-center d-flex">
                        <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                        @can('create invoice product')
                            <a href="#" class="btn btn-sm btn-white float-right add-small" data-url="{{ route('invoices.products.add',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Add Item')}}">
                                <i class="fas fa-plus"></i> {{__('Add item')}}
                            </a>
                        @endcan
                    </div>
                    <div class="card">
                        <div class="table-responsive order-table">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th>{{__('Action')}}</th>
                                    <th>#</th>
                                    <th>{{__('Item')}}</th>
                                    <th class="text-right">{{__('Price')}}</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @php $i=0; @endphp
                                @foreach($invoice->items as $items)
                                    <tr>
                                        <td class="Action">
                                            <span>
                                                @can('delete invoice product')
                                                    <a href="#" class="delete-icon" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$items->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.products.delete', $invoice->id,$items->id],'id'=>'delete-form-'.$items->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </span>
                                        </td>
                                        <td>
                                            {{++$i}}
                                        </td>
                                        <td>
                                            {{$items->iteam}}
                                        </td>
                                        <td class="text-right">
                                            {{\Auth::user()->priceFormat($items->price)}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row order-price">
                @php
                    $subTotal = $invoice->getSubTotal();
                    $tax = $invoice->getTax();
                @endphp
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Subtotal')}} :</strong> {{Auth::user()->priceFormat($subTotal)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Discount')}} :</strong> {{Auth::user()->priceFormat($invoice->discount)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{(!empty($invoice->tax)?$invoice->tax->name:'Tax')}} ({{(!empty($invoice->tax)?$invoice->tax->rate:'0')}} %) :</strong> {{\Auth::user()->priceFormat($tax)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Total')}} :</strong> {{Auth::user()->priceFormat($subTotal-$invoice->discount+$tax)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status text-right"><strong>{{__('Due Amount')}} :</strong> {{Auth::user()->priceFormat($invoice->getDue())}}</div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h4 class="h4 font-weight-400 float-left">{{__('Payment History')}}</h4>
    </div>
    <div class="card">
        <div class="table-responsive order-table">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th>{{__('Transaction ID')}}</th>
                    <th>{{__('Payment Date')}}</th>
                    <th>{{__('Payment Method')}}</th>
                    <th>{{__('Payment Type')}}</th>
                    <th>{{__('Note')}}</th>
                    <th class="text-right">{{__('Amount')}}</th>
                </tr>
                </thead>
                <tbody class="list">
                @php $i=0; @endphp
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{sprintf("%05d", $payment->transaction_id)}}</td>
                        <td>{{ Auth::user()->dateFormat($payment->date) }}</td>
                        <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td>
                        <td>{{$payment->payment_type}}</td>
                        <td>{{!empty($payment->notes) ? $payment->notes : '-'}}</td>
                        <td class="text-right">{{\Auth::user()->priceFormat($payment->amount)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(\Auth::user()->type == 'client')
        @if($invoice->getDue() > 0)
            <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card bg-none card-box">
                                <div class="row w-100">
                                    <div class="col-12">
                                        @if($settings['site_enable_stripe'] == 'on' && $settings['site_enable_paypal'] == 'on')
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li>
                                                    <a class="active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                                                </li>
                                                <li>
                                                    <a data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <div class="tab-content">
                                            @if($settings['site_enable_stripe'] == 'on')
                                                <div class="tab-pane fade {{ (($settings['site_enable_stripe'] == 'on' && $settings['site_enable_paypal'] == 'on') || $settings['site_enable_stripe'] == 'on') ? "show active" : "" }}" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                                    <form method="post" action="{{ route('client.invoice.payment',[$invoice->id]) }}" class="require-validation" id="payment-form">
                                                        @csrf
                                                        <div class="py-3 stripe-payment-div">
                                                            <div class="row">
                                                                <div class="col-sm-8">
                                                                    <div class="custom-radio">
                                                                        <label class="font-16 font-weight-bold">{{__('Credit / Debit Card')}}</label>
                                                                    </div>
                                                                    <p class="text-sm">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}</p>
                                                                </div>
                                                                <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                                                    <img src="{{$dir_payment.'/master.png'}}" height="24" alt="master-card-img">
                                                                    <img src="{{$dir_payment.'/discover.png'}}" height="24" alt="discover-card-img">
                                                                    <img src="{{$dir_payment.'/visa.png'}}" height="24" alt="visa-card-img">
                                                                    <img src="{{$dir_payment.'/american express.png'}}" height="24" alt="american-express-card-img">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="card-name-on" class="form-control-label">{{__('Name on card')}}</label>
                                                                        <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="{{\Auth::user()->name}}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div id="card-element"></div>
                                                                    <div id="card-errors" role="alert"></div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <br>
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="error" style="display: none;">
                                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-sm-12">
                                                                    <div class="text-sm-right">
                                                                        <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                                            <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Make Payment')}}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            @if($settings['site_enable_paypal'] == 'on')
                                                <div class="tab-pane fade {{ ($settings['site_enable_stripe'] == 'off' && $settings['site_enable_paypal'] == 'on') ? "show active" : "" }}" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                                    <form class="require-validation" method="POST" id="payment-form" action="{{ route('client.pay.with.paypal', $invoice->id) }}">
                                                        @csrf
                                                        <div class="py-3 stripe-payment-div">
                                                            <div class="row">
                                                                <div class="col-md-12 form-group">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                    @error('amount')
                                                                    <span class="invalid-amount text-sm text-danger" role="alert">{{ $message }}</span>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-sm-12">
                                                                    <div class="text-sm-right">
                                                                        <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                                            <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Make Payment')}}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection
