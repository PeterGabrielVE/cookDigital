@extends('layouts.admin')
@section('page-title')
    {{__('Tasks')}}
@endsection
@section('action-button')

@if(isset($project))
<div class="all-button-box row d-flex justify-content-end">
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-4">
        <a href="{{ route('get.tasksList',$project->id) }}" title="{{__('View All Tasks')}}" class="btn btn-xs btn-white btn-icon-only btn-success width-auto" style="background-color: green"><i class="fas fa-tasks"></i></a>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-4">
        <a href="#" data-url="{{ route('task.create',$project->id) }}" data-ajax-popup="true" data-title="{{__('Add New Task')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
    </div>
</div>
@endif
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
                                <th> {{__('Title')}}</th>
                                <th> {{__('Project')}}</th>
                                <th> {{__('Milestone')}}</th>
                                <th> {{__('Due Date')}}</th>
                                <th> {{__('Assigned to')}}</th>
                                <th> {{__('Status')}}</th>
                                <th> {{__('Priority')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ !empty($task->title)? $task->title : ''}}</td>
                                    <td>{{ !empty($task->project->name)? $task->project->name : ''}}</td>
                                    <td>{{ !empty($task->milestone->title)? $task->milestone->title : ''}}</td>
                                    <td>{{ !empty($task->due_date)? $task->due_date : ''}}</td>
                                    <td>{{ !empty($task->task_user->name)? $task->task_user->name : ''}}</td>
                                    <td>{{ !empty($task->status)? $task->status : ''}}</td>
                                    <td>{{ !empty($task->priority)? $task->priority : ''}}</td>
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
