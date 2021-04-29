@extends('layouts.admin')
@section('page-title')
    {{__('Manage Project')}}
@endsection

@section('action-button')
    @can('create project')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('projects.create') }}" data-ajax-popup="true" data-title="{{__('Create New Project')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        @foreach ($projects as $project)
            @php
                $permissions=$project->client_project_permission();
                $perArr=(!empty($permissions)? explode(',',$permissions->permissions):[]);

                $project_last_stage = ($project->project_last_stage($project->id)? $project->project_last_stage($project->id)->id:'');

                $total_task = $project->project_total_task($project->id);
                $completed_task=$project->project_complete_task($project->id,$project_last_stage);
                $percentage=0;
                if($total_task!=0){
                    $percentage = intval(($completed_task / $total_task) * 100);
                }

                $label='';
                if($percentage<=15){
                    $label='bg-danger';
                }else if ($percentage > 15 && $percentage <= 33) {
                    $label='bg-warning';
                } else if ($percentage > 33 && $percentage <= 70) {
                    $label='bg-primary';
                } else {
                    $label='bg-success';
                }

            @endphp

            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-6 project-card">
                <div class="card">
                    <div class="edit-profile user-text">
                        @if($project->is_active == 1)
                            @if((Gate::check('edit project') || Gate::check('delete project')))
                                <div class="dropdown action-item">
                                    <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('edit project')
                                            <a href="#" class="dropdown-item" data-url="{{ route('projects.edit',$project->id) }}" data-ajax-popup="true" data-title="{{__('Edit Project')}}">{{__('Edit')}}</a>
                                        @endcan
                                        @can('delete project')
                                            <a href="#" class="dropdown-item" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$project->id}}').submit();">{{__('Delete')}}</a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['projects.destroy', $project->id],'id'=>'delete-form-'.$project->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </div>
                                </div>
                            @endif
                        @else
                            <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                        @endif
                    </div>
                    <div class="project-title">
                        @if($project->is_active==1)
                            <h3 class="h3 font-weight-400 mb-0"><a href="{{route('projects.show',$project->id)}}">{{ $project->name }}</a></h3>
                        @else
                            <h3 class="h3 font-weight-400 mb-0">{{ $project->name }}</h3>
                        @endif
                        @foreach($project_status as $key => $status)
                            @if($key== $project->status)
                                @if($status=='Completed')
                                    @php $status_color ='badge-success' @endphp
                                @elseif($status=='On Going')
                                    @php $status_color ='badge-primary' @endphp
                                @else
                                    @php $status_color ='badge-warning' @endphp
                                @endif
                                <span class="badge badge-pill {{$status_color}}">{{__($status)}}</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="project-detail">
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0">{{__('Start Date')}}</label>
                                    <div class="date-box">{{ \Auth::user()->dateFormat($project->start_date) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0">{{__('Due Date')}}</label>
                                    <div class="date-box light-red">{{ \Auth::user()->dateFormat($project->due_date) }}</div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date full-width">
                                    <label class="m-0">{{__('Progress')}}</label>
                                    <div class="progress">
                                        <div class="progress-bar yellow-bg" style="width:{{$percentage}}%">{{$percentage}}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0">{{__('Budget')}}<span>{{ \Auth::user()->priceFormat($project->price) }}</span></label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0">{{__('Expense')}}<span>{{ \Auth::user()->priceFormat($project->project_expenses()) }}</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4 col-4">
                                @php
                                    $client=(!empty($project->client())?$project->client()->avatar:'')
                                @endphp
                                <div class="start-date">
                                    <label class="m-0">{{__('Client')}}</label>
                                    <ul class="project-img">
                                        <li><img src="{{(!empty($project->client()->avatar)? asset(Storage::url('avatar/'.$client)) : asset(Storage::url('avatar/avatar.png')))}}" data-toggle="tooltip" data-original-title="{{$project->client()->name}}"></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0">{{__('Members')}}</label>
                                    <ul class="project-img">
                                        @foreach($project->project_user() as $project_user)
                                            <li><img src="{{(!empty($project_user->avatar)? asset(Storage::url('avatar/'.$project_user->avatar)) : asset(Storage::url('avatar/avatar.png')))}}" data-toggle="tooltip" data-original-title="{{$project_user->name}}"></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="project-detail">
                        <div class="row">
                            <div class="col-lg-3 col-xl-3 col-md-4 col-sm-3 col-3">
                                <div class="project-number">
                                    @if($project->is_active==1)
                                        <a href="{{ route('project.taskboard',$project->id) }}">
                                            <i class="fas fa-tasks"></i> {{$project->countTask()}} {{__('Tasks')}}
                                        </a>
                                    @else
                                        <i class="fas fa-tasks"></i> {{$project->countTask()}} {{__('Tasks')}}
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-5 col-md-4 col-sm-5 col-5">
                                <div class="project-number">
                                    @if($project->is_active==1)
                                        <a href="{{ route('project.taskboard',$project->id) }}">
                                            <i class="fas fa-comments"></i> {{$project->countTaskComments()}} {{__('Comments')}}
                                        </a>
                                    @else
                                        <i class="fas fa-comments"></i> {{$project->countTaskComments()}} {{__('Comments')}}
                                    @endif
                                </div>
                            </div>
                            @if($project->is_active==1)
                                <div class="col-lg-5 col-xl-4 col-md-4 col-sm-4 col-4">
                                    <a href="{{ route('projects.show',$project->id) }}" class="btn btn-sm btn-white btn-icon-only width-auto">
                                        {{__('Detail')}}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
