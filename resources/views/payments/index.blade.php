@extends('layouts.admin')
@section('page-title')
    {{__('Manage Payment Method')}}
@endsection
@section('action-button')
    @can('create payment')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('payments.create') }}" data-ajax-popup="true" data-title="{{__('Create Payment Method')}}"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped dataTable">
                        <thead class="">
                        <tr>
                            <th>{{__('Payment Method')}}</th>
                            <th width="250px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        @foreach ($payments as $payment)
                            <tr data-id="{{$payment->id}}">
                                <td>
                                    <a>{{$payment->name}}</a>
                                </td>
                                <td class="Action">
                                    <span>
                                    @can('edit payment')
                                            <a href="#" class="edit-icon" data-url="{{ route('payments.edit',$payment->id) }}" data-ajax-popup="true" data-title="{{__('Edit Payment Method')}}" class="table-action" ><i class="fas fa-pencil-alt"></i></a>
                                        @endcan
                                        @can('delete payment')
                                            <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$payment->id}}').submit();"><i class="fas fa-trash"></i></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['payments.destroy', $payment->id],'id'=>'delete-form-'.$payment->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
