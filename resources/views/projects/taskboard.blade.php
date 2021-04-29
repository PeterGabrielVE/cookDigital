@extends('layouts.admin')
@push('script-page')
    <script src="{{asset('assets/libs/dragula/dist/dragula.min.js')}}"></script>
    <script>
        @can('move task')
            @if(\Auth::user()->type!='client' || (\Auth::user()->type=='client' && in_array('move task',$perArr)))
            !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var old_status = $("#" + source.id).attr('data-status');
                        var new_status = $("#" + target.id).attr('data-status');
                        var stage_id = $(target).attr('data-id');

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '{{route('taskboard.order')}}',
                            type: 'POST',
                            data: {task_id: id, stage_id: stage_id, order: order, old_status: old_status, new_status: new_status, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('{{__("Error")}}', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";
            a.Dragula.init()
        }(window.jQuery);
        @endif
        @endcan
    </script>
    <script>
        $(document).on('click', '#form-comment button', function (e) {
            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            var name = '{{\Auth::user()->name}}';
            if (comment != '') {
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {comment: comment, "_token": $('meta[name="csrf-token"]').attr('content')},
                    type: 'POST',
                    success: function (data) {
                        data = JSON.parse(data);
                        var html = "<li class='media mb-20'>" +
                            "                    <div class='media-body'>" +
                            "                    <div class='d-flex justify-content-between align-items-end'><div>" +
                            "                        <h5 class='mt-0'>" + name + "</h5>" +
                            "                        <p class='mb-0 text-xs'>" + data.comment + "</p></div>" +
                            "                           <div class='comment-trash' style=\"float: right\">" +
                            "                               <a href='#' class='btn btn-outline btn-sm text-danger delete-comment' data-url='" + data.deleteUrl + "' >" +
                            "                                   <i class='fas fa-trash'></i>" +
                            "                               </a>" +
                            "                           </div>" +
                            "                    </div>" +
                            "                    </div>" +
                            "                </li>";

                        $("#comments").prepend(html);
                        $("#form-comment textarea[name='comment']").val('');
                        show_toastr('{{__("Success")}}', '{{ __("Comment Added Successfully!")}}', 'success');
                    },
                    error: function (data) {
                        show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                });
            } else {
                show_toastr('{{__("Error")}}', '{{ __("Please write comment!")}}', 'error');
            }
        });

        $(document).on("click", ".delete-comment", function () {
            if (confirm('Are You Sure ?')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'JSON',
                    success: function (data) {
                        show_toastr('{{__("Success")}}', '{{ __("Comment Deleted Successfully!")}}', 'success');
                        btn.closest('.media').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__("Error")}}', data.message, 'error');
                        } else {
                            show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            }
        });

        $(document).on('submit', '#form-file', function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#form-file").data('url'),
                type: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    show_toastr('{{__("Success")}}', '{{ __("File Added Successfully!")}}', 'success');
                    $('.file_update').html('');
                    $('#file-error').html('');
                    var delLink = '';
                    if (data.deleteUrl.length > 0) {
                        delLink = "<a href='#' class='btn btn-outline btn-sm text-danger delete-comment-file m-0 px-2' data-id='" + data.id + "' data-url='" + data.deleteUrl + "'>" +
                            "                                        <i class='fas fa-trash'></i>" +
                            "                                    </a>";
                    }

                    var html = '<div class="col-8 mb-2 file-' + data.id + '">' +
                        '                                <h5 class="mt-0 mb-1 font-weight-bold text-sm">' + data.name + '</h5>' +
                        '                                <p class="m-0 text-xs">' + data.file_size + '</p>' +
                        '                            </div>' +
                        '                            <div class="col-4 mb-2 file-' + data.id + '">' +
                        '                                <div class="comment-trash" style="float: right">' +
                        '                                    <a download href="{{asset(Storage::url('tasks'))}}/' + data.file + '" class="btn btn-outline btn-sm text-primary m-0 px-2">' +
                        '                                        <i class="fa fa-download"></i>' +
                        '                                    </a>' + delLink +
                        '                                </div>' +
                        '                            </div>';

                    $("#comments-file").prepend(html);
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        $('#file-error').text(data.errors.file[0]).show();
                    } else {
                        show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                }
            });
        });

        $(document).on("click", ".delete-comment-file", function () {
            if (confirm('Are You Sure ?')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'JSON',
                    success: function (data) {
                        show_toastr('{{__("Success")}}', '{{ __("File Deleted Successfully!")}}', 'success');
                        $('.file-' + btn.attr('data-id')).remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__("Error")}}', data.message, 'error');
                        } else {
                            show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.submit-checklist', function () {
            $('#form-checklist').submit();
        });
        $(document).on('submit', '#form-checklist', function (e) {
            e.preventDefault();
            if ($('.checklist-name').val() != '') {
                $.ajax({
                    url: $("#form-checklist").data('action'),
                    type: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        show_toastr('{{__("Success")}}', '{{ __("Checklist Added Successfully!")}}', 'success');
                        var html = '<li class="media">' +
                            '<div class="media-body">' +
                            '<h5 class="mt-0 mb-1 font-weight-bold"> </h5> ' +
                            '<div class="row"> ' +
                            '<div class="col-8"> ' +
                            '<div class="custom-control custom-checkbox checklist-checkbox"> ' +
                            '<input type="checkbox" id="checklist-' + data.id + '" class="custom-control-input"  data-url="' + data.updateUrl + '">' +
                            '<label for="checklist-' + data.id + '" class="custom-control-label">' + data.name + '</label> ' +
                            ' </div>' +
                            '</div> ' +
                            '<div class="col-4"> ' +
                            '<div class="comment-trash text-right"> ' +
                            '<a href="#" class="btn btn-outline btn-sm text-danger delete-checklist" data-url="' + data.deleteUrl + '">' +
                            '<i class="fas fa-trash"></i>' +
                            '</a>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            ' </li>';


                        $("#check-list").prepend(html);
                        $("#form-checklist input[name=name]").val('');
                        $("#form-checklist").collapse('toggle');
                    },
                });
            } else {
                show_toastr('{{__("Error")}}', '{{ __("Checklist name is required")}}', 'error');
            }
        });
        $(document).on("click", ".delete-checklist", function () {
            if (confirm('Are You Sure ?')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'JSON',
                    success: function (data) {
                        show_toastr('{{__("Success")}}', '{{ __("Checklist Deleted Successfully!")}}', 'success');
                        btn.closest('.media').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__("Error")}}', data.message, 'error');
                        } else {
                            show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            }
        });

        var checked = 0;
        var count = 0;
        var percentage = 0;

        $(document).on("change", "#check-list input[type=checkbox]", function () {
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'POST',
                data: {_token: $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {
                },
                error: function (data) {
                    data = data.responseJSON;
                    show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                }
            });
            taskCheckbox();
        });
    </script>
@endpush

@section('page-title')
    {{__('Manage Task')}}
@endsection

@section('action-button')
    @can('create task')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('task.create',$project->id) }}" data-ajax-popup="true" data-title="{{__('Add New Task')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @php
                $json = [];
                foreach ($stages as $stage){
                    $json[] = 'lead-list-'.$stage->id;
                }
            @endphp
            <div class="board" data-plugin="dragula" data-containers='{!! json_encode($json) !!}'>
                @foreach($stages as $stage)
                    @if(\Auth::user()->type =='client' || \Auth::user()->type =='company')
                        @php $tasks =$stage->tasks($project->id) @endphp
                    @else
                        @php $tasks =$stage->tasks($project->id)     @endphp
                    @endif
                    <div class="tasks">
                        <h5 class="mt-0 mb-0 task-header">{{$stage->name}} (<span class="count">{{count($tasks)}}</span>)</h5>
                        <div id="lead-list-{{$stage->id}}" data-status="{{$stage->name}}" data-id="{{$stage->id}}" class="task-list-items for-tasks mb-2">
                            @foreach($tasks as $task)
                                <div class="card mb-2 mt-0 pb-1" data-id="{{$task->id}}">
                                    <div class="card-body p-0">
                                        @if(Gate::check('edit task') || Gate::check('delete task'))
                                            <div class="float-right">
                                                <div class="dropdown global-icon lead-dropdown pr-1">
                                                    <a href="#" class="action-item" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @can('edit task')
                                                            <a href="#" data-url="{{ route('task.edit',$task->id) }}" data-ajax-popup="true" data-title="{{__('Edit Task')}}" data-original-title="{{__('Edit Task')}}" class="dropdown-item">
                                                                {{__('Edit')}}
                                                            </a>
                                                        @endcan
                                                        @can('delete task')
                                                            <a class="dropdown-item" href="#" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$task->id}}').submit();">{{__('Delete')}}</a>
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id],'id'=>'delete-form-'.$task->id]) !!}
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="pl-2 pt-0 pr-2 pb-2">
                                            <div class="my-2">
                                                <span>
                                                    <a href="#" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Task Board')}}" class="text-body h6">{{$task->title}}</a>
                                                </span>
                                                @if($task->priority =='low')
                                                    <span class="font-weight-600 badge badge-xs badge-success">{{ ucfirst($task->priority) }}</span>
                                                @elseif($task->priority =='medium')
                                                    <span class="font-weight-600 badge badge-xs badge-warning">{{ ucfirst($task->priority) }}</span>
                                                @elseif($task->priority =='high')
                                                    <span class="font-weight-600 badge badge-xs badge-danger">{{ ucfirst($task->priority) }}</span>
                                                @endif
                                            </div>
                                            <p class="mb-0">
                                                <span class="text-nowrap mb-2 d-inline-block text-xs">{{(!empty($task->description)) ? $task->description : '-'}}</span>
                                            </p>
                                            <div class="row">
                                                <div class="col-6 text-xs @if($task->taskCompleteCheckListCount()==$task->taskTotalCheckListCount() && $task->taskCompleteCheckListCount()!=0) text-success @else text-warning @endif">
                                                    <span>{{$task->taskCompleteCheckListCount()}}/{{$task->taskTotalCheckListCount()}}</span>
                                                </div>
                                                <div class="col-6 text-right text-xs font-weight-bold">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ \Auth::user()->dateFormat($task->start_date) }}</span>
                                                </div>
                                                <div class="col-12 pt-2">
                                                    <p class="mb-0">
                                                        <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                            <img data-toggle="tooltip" data-original-title="{{(!empty($task->task_user)?$task->task_user->name:'')}}" src="{{(!empty($task->task_user->avatar)? asset(Storage::url('avatar/'.$task->task_user->avatar)) : asset(Storage::url('avatar/avatar.png')))}}" class="rounded-circle " width="25" height="25">
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
