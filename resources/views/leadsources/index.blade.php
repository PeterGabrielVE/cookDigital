@extends('layouts.admin')
@section('page-title')
    {{__('Manage Lead Source')}}
@endsection

@section('action-button')
    @can('create lead source')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('leadsources.create') }}" data-ajax-popup="true" data-title="{{__('Create Lead Source')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
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
                                <th>{{__('Source')}}</th>
                                <th width="250px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($leadsources as $leadsource)
                                <tr>
                                    <td>
                                        <a>{{$leadsource->name}}</a>
                                    </td>
                                    <td class="Action">
                                            <span>
                                            @can('edit lead source')
                                                    <a href="#" class="edit-icon" data-url="{{ route('leadsources.edit',$leadsource->id) }}" data-ajax-popup="true" data-title="{{__('Edit Lead Source')}}" class="table-action" ><i class="fas fa-pencil-alt"></i></a>
                                                @endcan
                                                @can('delete lead source')
                                                    <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$leadsource->id}}').submit();"><i class="fas fa-trash"></i></a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leadsources.destroy', $leadsource->id],'id'=>'delete-form-'.$leadsource->id]) !!}
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
