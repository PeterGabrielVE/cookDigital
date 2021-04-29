@extends('layouts.admin')
@section('page-title')
    {{ __('Estimation Detail') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('edit estimation')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ URL::to('estimations/'.$estimation->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Estimation')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-pencil-alt"></i> {{__('Edit')}} </a>
            </div>
        @endcan
        @can('view estimation')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 col-12">
                <a href="{{ route('get.estimation',$estimation->id) }}" class="btn btn-xs btn-white bg-warning btn-icon-only width-auto" title="{{__('Print Estimation')}}" target="_blanks"><i class="fa fa-print"></i> {{__('Print')}}</a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="invoice-title">{{ \App\Utility::estimateNumberFormat($estimation->estimation_id) }}</div>
        <div class="invoice-detail">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail">
                        <strong>{{__('From')}}:</strong>
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
                        <div class="invoice-number">{{$client->name}} </div>
                        <div class="invoice-number">{{$client->email}}</div>
                    </div>
                </div>
            </div>
            <div class="status-section">
                <div class="row">
                    <div class="col-6">
                        <div class="text-status"><strong>{{__('Status')}} : </strong>
                            @if($estimation->status == 0)
                                <span class="badge badge-pill badge-primary">{{ __(\App\Estimation::$statues[$estimation->status]) }}</span>
                            @elseif($estimation->status == 1)
                                <span class="badge badge-pill badge-danger">{{ __(\App\Estimation::$statues[$estimation->status]) }}</span>
                            @elseif($estimation->status == 2)
                                <span class="badge badge-pill badge-warning">{{ __(\App\Estimation::$statues[$estimation->status]) }}</span>
                            @elseif($estimation->status == 3)
                                <span class="badge badge-pill badge-success">{{ __(\App\Estimation::$statues[$estimation->status]) }}</span>
                            @elseif($estimation->status == 4)
                                <span class="badge badge-pill badge-info">{{ __(\App\Estimation::$statues[$estimation->status]) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-status text-right">{{__('Issue Date')}}:<strong>{{ Auth::user()->dateFormat($estimation->issue_date) }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="justify-content-between align-items-center d-flex">
                        <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                        @can('estimation add product')
                            <a href="#" class="btn btn-sm btn-white float-right add-small" data-url="{{ route('estimations.products.add',$estimation->id) }}" data-ajax-popup="true" data-title="{{__('Add Item')}}">
                                <i class="fas fa-plus"></i> {{__('Add Item')}}
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
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Quantity')}}</th>
                                    <th class="text-right">{{__('Totals')}}</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @php
                                    $i=0;
                                @endphp
                                @foreach($estimation->getProducts as $product)
                                    <tr>
                                        <td class="Action">
                                            <span>
                                                @can('estimation edit product')
                                                    <a href="#" data-url="{{ route('estimations.products.edit',[$estimation->id,$product->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Estimation Item')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                                @endcan
                                                @can('estimation delete product')
                                                    <a href="#" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product->id}}').submit();" class="delete-icon"><i class="fas fa-trash"></i></a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['estimations.products.delete', $estimation->id,$product->id],'id'=>'delete-form-'.$product->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </span>
                                        </td>
                                        <td>{{++$i}}</td>
                                        <td>{{$product->name}}</td>
                                        <td>{{Auth::user()->priceFormat($product->price)}}</td>
                                        <td>{{$product->quantity}}</td>
                                        @php
                                            $price = $product->price * $product->quantity;
                                        @endphp
                                        <td class="text-right">{{Auth::user()->priceFormat($price)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    @php
                        $subTotal = $estimation->getSubTotal();
                    @endphp
                    <div class="text-status"><strong>{{__('Subtotal')}} : </strong>{{Auth::user()->priceFormat($subTotal)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Discount')}} : </strong>{{Auth::user()->priceFormat($estimation->discount)}}</div>
                </div>
                <div class="col-md-3">
                    @php
                        $tax = $estimation->getTax();
                    @endphp
                    <div class="text-status"><strong>{{$estimation->tax->name}} ({{$estimation->tax->rate}} %) : </strong>{{Auth::user()->priceFormat($tax)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status text-right"><strong>{{__('Total')}} : </strong>{{Auth::user()->priceFormat($subTotal-$estimation->discount+$tax)}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
