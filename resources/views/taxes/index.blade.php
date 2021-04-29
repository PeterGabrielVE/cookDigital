@extends('layouts.admin')
@section('page-title')
    {{__('Manage Tax Rate')}}
@endsection

@section('action-button')
    @can('create invoice')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('taxes.create') }}" data-ajax-popup="true" data-title="{{__('Create Tax Rate')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
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
                                <th>{{__('Tax Name')}}</th>
                                <th>{{__('Rate %')}}</th>
                                <th width="250px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($taxes as $taxe)
                                <tr>
                                    <td>{{ $taxe->name }}</td>
                                    <td>{{ $taxe->rate }}</td>
                                    <td class="Action">
                                        <span>
                                        @can('edit tax')
                                                <a href="#" class="edit-icon" data-url="{{ route('taxes.edit',$taxe->id) }}" data-ajax-popup="true" data-title="{{__('Edit Tax Rate')}}" >
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endcan
                                            @can('delete tax')
                                                <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$taxe->id}}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['taxes.destroy', $taxe->id],'id'=>'delete-form-'.$taxe->id]) !!}
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
    </div>
@endsection
