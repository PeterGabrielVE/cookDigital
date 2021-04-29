@extends('layouts.admin')
@section('page-title')
    {{__('Manage Label')}}
@endsection

@section('action-button')
    @can('create label')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('labels.create') }}" data-ajax-popup="true" data-title="{{__('Create New Label')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
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
                        <thead>
                        <tr>
                            <th>{{__('Label')}}</th>
                            <th width="250px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels as $k => $label)
                            <tr data-id="{{$label->id}}">
                                <td>
                                    <div class="custom-control custom-radio mb-3 {{$label->color}}">
                                        <label class="custom-control-label ">{{$label->name}}</label>
                                    </div>
                                </td>
                                <td class="Action">
                                    <span>
                                    @can('edit label')
                                            <a href="#" data-url="{{ route('labels.edit',$label->id) }}" data-ajax-popup="true" data-title="{{__('Edit Label')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                        @endcan
                                        @can('delete label')
                                            <a href="#" class="delete-icon" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$label->id}}').submit();"><i class="fas fa-trash"></i></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['labels.destroy', $label->id],'id'=>'delete-form-'.$label->id]) !!}
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
