@extends('layouts.admin')
@push('css-page')
@endpush
@push('script-page')
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{__('Permission')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('Permission')}}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between w-100">
                            <h4>{{__('Manage Permission')}}</h4>
                            @can('create permission')
                                <a href="#" data-url="{{ route('permissions.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Permission')}}" class="btn btn-sm btn-warning">
                                    <i class="fa fa-plus"></i> &nbsp;&nbsp;{{__('Create')}}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="dataTable">
                                <thead>
                                <tr>
                                    <th> {{__('Permissions')}}</th>
                                    <th class="text-right" width="200px"> {{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td class="action">
                                            @can('create permission')
                                                <a href="#" data-url="{{ route('permissions.edit',$permission->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Update permission')}}" class="btn btn-outline btn-sm ">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            @endcan
                                            @can('create permission')
                                                <a href="#" class="btn btn-outline btn-sm red"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$permission->id}}').submit();">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id],'id'=>'delete-form-'.$permission->id]) !!}
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
    </section>
@endsection
