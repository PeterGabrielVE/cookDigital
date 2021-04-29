@extends('layouts.admin')
@section('page-title')
    {{__('Manage Client')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('create client')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('clients.create') }}" data-ajax-popup="true" data-title="{{__('Create New Client')}}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fa fa-plus"></i> {{__('Add New')}}
                </a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        @foreach($clients as $client)
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card profile-card">
                    @if(Gate::check('edit user') || Gate::check('delete user'))
                        <div class="edit-profile user-text">
                            @if($client->is_active == 1)
                                @if((Gate::check('edit user') || Gate::check('delete user')))
                                    <div class="dropdown action-item">
                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @can('edit user')
                                                <a href="#" class="dropdown-item" data-url="{{ route('clients.edit',$client->id) }}" data-ajax-popup="true" data-title="{{__('Edit Client')}}">{{__('Edit')}}</a>
                                            @endcan
                                            @can('delete user')
                                                <a class="dropdown-item" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$client['id']}}').submit();">
                                                    @if($client->delete_status == 1){{__('Delete')}} @else {{__('Restore')}}@endif
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['clients.destroy', $client['id']],'id'=>'delete-form-'.$client['id']]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>
                                @endif
                            @else
                                <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                            @endif
                        </div>
                    @endif
                    <div class="avatar-parent-child">
                        <img src="{{(!empty($client->avatar))? asset(Storage::url("avatar/".$client->avatar)): asset(Storage::url("avatar/avatar.png"))}}" class="avatar rounded-circle avatar-xl">
                    </div>
                    <h4 class="h4 mb-0 mt-2">{{$client->name}}</h4>
                    <h5 class="office-time mb-0">{{$client->email}}</h5>
                    <div class="sal-right-card">
                        <span class="badge badge-pill badge-blue">{{ucfirst($client->type)}}</span>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number"><i class="fas fa-briefcase"></i>{{$client->client_project()}}</div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i>{{\Auth::user()->priceFormat($client ->client_project_budget())}}</div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number border-none"><i class="fas fa-tasks"></i>{{$client->client_lead()}}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
