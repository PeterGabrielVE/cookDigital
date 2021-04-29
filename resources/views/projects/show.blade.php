@extends('layouts.admin')
@push('css-page')
    <link rel="stylesheet" href="{{asset('assets/libs/dropzonejs/dropzone.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('assets/libs/dropzonejs/min/dropzone.min.js')}}"></script>
    <script>
        // Update Project Status
        $(document).on("change", "#submit_status select[name=status]", function () {
            $('#submit_status').submit();
        });
    </script>
@endpush
@section('page-title')
    {{__('Project Detail')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('manage bug report')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{ route('task.bug',$project->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fas fa-bug"></i> {{__('Bug Report')}}
                </a>
            </div>
        @endcan
        @can('manage task')
            @if(\Auth::user()->type!='client' || (\Auth::user()->type=='client' && in_array('show task',$perArr)))
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                    <a href="{{ route('project.taskboard',$project->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto">
                        <i class="fas fa-user-edit"></i> {{__('Task Kanban')}}
                    </a>
                </div>
            @endif
        @endcan
        @can('edit project')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('projects.edit',$project->id) }}" data-ajax-popup="true" data-title="{{__('Edit Project')}}">
                    <i class="fas fa-user-edit"></i> {{__('Edit')}}
                </a>
            </div>
        @endcan
        @can('delete task')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto bg-danger" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$project->id}}').submit();">
                    <i class="fas fa-trash"></i> {{__('Delete')}}
                </a>
                {!! Form::open(['method' => 'DELETE', 'route' => ['projects.destroy', $project->id],'id'=>'delete-form-'.$project->id]) !!}
                {!! Form::close() !!}
            </div>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $permissions=$project->client_project_permission();
        $perArr=(!empty($permissions)? explode(',',$permissions->permissions):[]);
        $project_last_stage = ($project->project_last_stage($project->id))? $project->project_last_stage($project->id)->id:'';

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

    <div class="card">
        <div class="detail-box">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-3 col-xl-3 col-md-4">
                    <h2 class="h5 font-weight-400 mb-0">{{$project->name}} <span class="badge badge-pill badge-danger">Uncompleted</span></h2>
                </div>
                <div class="col-lg-9 col-xl-9 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                    <div class="col-xl-4 col-lg-5 col-md-4 col-sm-4 col-12">
                        <div class="start-date full-width">
                            <label class="m-0">{{__('Progress')}}</label>
                            <div class="progress">
                                <div class="progress-bar yellow-bg" style="width:{{$percentage}}%">{{$percentage}}%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-4 col-7">
                        <div class="start-date mr-2">
                            <label class="m-0">{{__('Start Date')}}</label>
                            <div class="date-box">{{ \Auth::user()->dateFormat($project->start_date)}}</div>
                        </div>
                        <div class="start-date">
                            <label class="m-0">{{__('Due Date')}}</label>
                            <div class="date-box light-red">{{ \Auth::user()->dateFormat($project->due_date)}}</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-4 col-5">
                        {{ Form::model($project, array('route' => array('projects.update.status', $project->id), 'method' => 'POST','id'=>'submit_status')) }}
                        <div class="btn-box row d-flex justify-content-end">
                            <a href="#" class="monthly-btn">
                                <span class="monthly-text">{{__('Status')}}</span>
                                <select class="daily-text" name="status" id="status">
                                    @foreach($project_status as $key => $value)
                                        <option value="{{ $key }}" {{ ($project->status == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </a>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="detail-card-box">
            @php
                $datetime1 = new DateTime($project->due_date);
                $datetime2 = new DateTime(date('Y-m-d'));
                $interval = $datetime1->diff($datetime2);
                $days = $interval->format('%a')
            @endphp
            <div class="row">
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box"><i class="fas fa-university"></i></div>
                            <h4>{{__('Budget')}} <span>{{ \Auth::user()->priceFormat($project->price) }}</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box yellow-bg"><i class="fas fa-star"></i></div>
                            <h4>{{__('Expense')}} <span>{{ \Auth::user()->priceFormat($project->project_expenses()) }}</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box green-bg"><i class="fas fa-clipboard"></i></div>
                            <h4>{{__('Tasks')}} <span>6</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box black-bg"><i class="fas fa-database"></i></div>
                            <h4>{{__('Comments')}} <span>{{$project->countTaskComments()}}</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box red-bg"><i class="fas fa-users"></i></div>
                            <h4>{{__('Members')}} <span>{{$project->project_user()->count()}}</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6">
                    <div class="card card-box">
                        <div class="left-card">
                            <div class="icon-box blue-bg"><i class="fas fa-calendar-alt"></i></div>
                            <h4>{{__('Days Left')}} <span>{{$days}}</span></h4>
                        </div>
                        <img src="{{asset('assets/img/dot-icon.png')}}" class="dotted-icon">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{--Users--}}
        <div class="col-xl-9 col-lg-9 col-md-12">
            <div class="card height-480">
                <div class="row justify-content-between align-items-center small-detail">
                    <div class="col-md-4 col-sm-6 col-xl-5 col-lg-5 mb-3 mb-md-0">
                        <h5 class="h5 font-weight-400 mb-0">{{__('Staff')}}</h5>
                    </div>
                    @can('invite user project')
                        <div class="col-xl-7 col-lg-7 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box row d-flex justify-content-end">
                                <a href="#" class="btn btn-sm btn-white btn-icon-only width-auto" data-url="{{ route('project.invite',$project->id) }}" data-ajax-popup="true" data-title="{{__('Add User')}}">
                                    <i class="fas fa-plus"></i> {{__('Add')}}
                                </a>
                            </div>
                        </div>
                    @endcan
                </div>
                <div class="staff-box mx-450 top-5-scroll">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                            <div class="card profile-card">
                                @can('client permission project')
                                    <div class="edit-profile user-text">
                                        <a href="#" data-url="{{ route('client.permission',[$project->id,$project->client]) }}" data-ajax-popup="true" data-title="{{__('Client Permission')}}" data-toggle="tooltip" data-original-title="{{__('Client Permission')}}" class="edit-icon mr-3"><i class="fas fa-lock"></i></a>
                                    </div>
                                @endcan
                                <div class="avatar-parent-child">
                                    <img src="{{(!empty($project->client()->avatar)? asset(Storage::url('avatar/'.$project->client()->avatar)) : asset(Storage::url('avatar/avatar.png')))}}" class="avatar rounded-circle avatar-lg">
                                </div>
                                <h4 class="h4 mb-0 mt-2">{{(!empty($project->client())?$project->client()->name:'')}}</h4>
                                <h5 class="office-time my-2">{{(!empty($project->client())?$project->client()->email:'')}}</h5>
                                <div class="sal-right-card">
                                    <span class="badge badge-pill badge-blue">{{__('Client')}}</span>
                                </div>
                            </div>
                        </div>
                        @foreach($project->project_user() as $user)
                            @php $totalTask= $project->user_project_total_task($user->project_id,$user->user_id) @endphp
                            @php $completeTask= $project->user_project_comlete_task($user->project_id,$user->user_id,($project->project_last_stage())?$project->project_last_stage()->id:'' ) @endphp
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                                <div class="card profile-card">
                                    @can('invite user project')
                                        <div class="edit-profile user-text">
                                            <a href="#" data-toggle="tooltip" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user->user_id}}').submit();" data-original-title="{{__('Remove')}}" class="edit-icon bg-danger mr-3"><i class="fas fa-trash-alt"></i></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['project.remove.user',$project->id,$user->user_id],'id'=>'delete-form-'.$user->user_id]) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    @endcan
                                    <div class="avatar-parent-child">
                                        <img src="{{(!empty($user->avatar)? asset(Storage::url('avatar/'.$user->avatar)) : asset(Storage::url('avatar/avatar.png')))}}" class="avatar rounded-circle avatar-lg">
                                    </div>
                                    <h4 class="h4 mb-0 mt-2">{{$user->name}}</h4>
                                    <h5 class="office-time my-2">{{$user->email}}</h5>
                                    <div class="sal-right-card">
                                        <span class="badge badge-pill badge-blue">{{ucfirst($user->type)}}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{--Activity--}}
        <div class="col-lg-3 col-xl-3 col-md-6">
            <div class="card height-480">
                <div class="small-detail h5 font-weight-400 mb-0">{{__('Activity')}}</div>
                <ul class="activity-detail mx-450 top-5-scroll">
                    @foreach($project->activities as $activity)
                        <li>
                            <span class="blue-text">{{ $activity->log_type }}</span> - {!! $activity->getRemark() !!}
                            <br><span class="text-green">{{date('d M Y H:i', strtotime($activity->created_at))}}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{--Description--}}
        @if(!empty($project->description))
            <div class="col-lg-12 col-xl-12 col-md-12">
                <div class="card">
                    <div class="small-detail h5 font-weight-400 mb-0">{{__('Description')}}</div>
                    <div class="p-3">
                        <p class="text-sm">{{$project->description}}</p>
                    </div>
                </div>
            </div>
        @endif

        {{--Milestone--}}
        @if(\Auth::user()->type !='client' || (\Auth::user()->type=='client' && in_array('show milestone',$perArr)))
            <div class="col-lg-5 col-xl-5 col-md-6">
                <div class="card height-480">
                    <div class="row justify-content-between align-items-center small-detail">
                        <div class="col-md-4 col-sm-6 col-xl-5 col-lg-5 mb-3 mb-md-0">
                            <h5 class="h5 font-weight-400 mb-0">{{__('Milestones')}} ({{count($project->milestones)}})</h5>
                        </div>
                        @if(\Auth::user()->type!='client' || (\Auth::user()->type=='client' && in_array('create milestone',$perArr)))
                            <div class="col-xl-7 col-lg-7 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                                <div class="all-button-box row d-flex justify-content-end">
                                    <a href="#" class="btn btn-sm btn-white btn-icon-only width-auto" data-url="{{ route('project.milestone',$project->id) }}" data-ajax-popup="true" data-title="{{__('Create New Milestone')}}">
                                        <i class="fas fa-plus"></i> {{__('Create milestone')}}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="table-responsive mx-450 top-5-scroll">
                        <table class="table align-items-center">
                            <tbody class="list">
                            @foreach($project->milestones as $milestone)
                                <tr>
                                    <td class="Id"><a href="#" data-ajax-popup="true" data-title="{{ __('Milestones Details') }}" data-url="{{route('project.milestone.show',[$milestone->id])}}">{{$milestone->title}}</a></td>
                                    <td class="mile-text"><span>{{Auth::user()->priceFormat($milestone->cost)}}</span></td>
                                    <td class="mile-text">{{\Utility::dateFormat($milestone->created_at)}}</td>
                                    <td class="Due">
                                        <div class="date-box">{{ucfirst($milestone->status)}}</div>
                                    </td>
                                    <td class="Action-icon">
                                          <span>
                                              @if(\Auth::user()->type!='client' || (\Auth::user()->type=='client' && in_array('edit milestone',$perArr)))
                                                  <a href="#" class="edit-icon" data-url="{{ route('project.milestone.edit',$milestone->id) }}" data-ajax-popup="true" data-title="{{__('Edit Milestone')}}"><i class="fas fa-pencil-alt"></i></a>
                                              @endif
                                              @if(\Auth::user()->type!='client' || (\Auth::user()->type=='client' && in_array('delete milestone',$perArr)))
                                                  <a href="#" class="delete-icon" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$milestone->id}}').submit();"><i class="fas fa-trash"></i></a>
                                                  {!! Form::open(['method' => 'DELETE', 'route' => ['project.milestone.destroy', $milestone->id],'id'=>'delete-form-'.$milestone->id]) !!}
                                                  {!! Form::close() !!}
                                              @endif
                                          </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-xl-7 col-lg-7 col-md-6">
            <div class="card">
                <div class="small-detail h5 font-weight-400 mb-0">{{__('Upload File')}}</div>
                <div class="col-md-12 dropzone mx-428 min-428 browse-file" id="my-dropzone"></div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#my-dropzone", {
            maxFiles: 20,
            maxFilesize: 20,
            parallelUploads: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
            url: "{{route('project.file.upload',[$project->id])}}",
            success: function (file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('{{__("Error")}}', response.error, 'error');
                }
            },
            error: function (file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{__("Error")}}', response.error, 'error');
                } else {
                    show_toastr('{{__("Error")}}', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("project_id", {{$project->id}});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "badge badge-pill badge-blue mx-1");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "badge badge-pill badge-danger mx-1");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{__('Delete')}}");
            del.innerHTML = "<i class='fas fa-trash'></i>";

            del.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        success: function (response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            html.appendChild(download);
            html.appendChild(del);

            file.previewTemplate.appendChild(html);
        }

        @php
            $files = $project->files;

        @endphp
        @foreach($files as $file)
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('project_files/'.$file->file_path))}}};
        myDropzone.emit("addedfile", mockFile);
        {{--myDropzone.emit("thumbnail", mockFile, "{{asset('storage/project_files/'.$file->file_path)}}");--}}
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('project_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);
        dropzoneBtn(mockFile, {download: "{{route('projects.file.download',[$project->id,$file->id])}}", delete: "{{route('projects.file.delete',[$project->id,$file->id])}}"});
        @endforeach
    </script>
@endpush

