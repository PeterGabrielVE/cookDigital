@extends('layouts.admin')
@push('css-page')
    <link href="{{ asset('assets/default/render/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@push('script-page')
    <script src="{{ asset('assets/default/render/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/components-bootstrap-select.min.js') }}" type="text/javascript"></script>
@endpush
@section('page-title')
    {{__('Product')}}
@endsection
@section('breadcrumb')
    <ul class="page-breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="{{ route('dashboard') }}">{{__('Home')}}</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <span>{{__('Products')}}</span>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit portlet-datatable ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tasks font-green"></i>
                        <span class="caption-subject font-green sbold uppercase">{{__('Manage Products')}}</span>
                    </div>
                    @can('create product')
                        <span class="create-btn">
                        <a href="#" data-url="{{ route('products.create') }}" data-ajax-popup="true" data-title="{{__('Create New Product')}}" class="btn btn-circle btn-outline btn-sm ">
                        <i class="fa fa-plus"></i>  {{__('Create')}}
                    </a>
                     </span>
                    @endcan
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="dataTable">
                            <thead>
                            <tr>
                                <th> {{__('Name')}}</th>
                                <th> {{__('Price')}}</th>
                                <th>{{__('Unit')}} </th>
                                <th>{{__('Description')}} </th>
                                <th class="text-right" width="200px"> {{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody class="">
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ (!empty($product->unit()))?$product->unit()->name:'' }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td class="action">
                                        @can('edit project')
                                            <a href="#" data-url="{{ route('products.edit',$product->id) }}" data-ajax-popup="true" data-title="{{__('Edit Product')}}" class="btn btn-outline btn-sm " >
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('delete project')
                                            <a href="#" class="btn btn-outline btn-sm red"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product->id}}').submit();">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['products.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </td>
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


