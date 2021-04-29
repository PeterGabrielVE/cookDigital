@extends('layouts.admin')
@section('page-title')
    {{__('Manage TimeSheet')}}
@endsection

@section('action-button')
    @can('manage timesheet')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('task.timesheet') }}" data-ajax-popup="true" data-title="{{__('Create Time Sheet')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
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
                                <th> {{__('Task')}}</th>
                                @if(\Auth::user()->type == 'company')
                                    <th> {{__('User')}}</th>
                                @endif
                                <th> {{__('Project')}}</th>
                                <th> {{__('Date')}}</th>
                                <th> {{__('Hours')}}</th>
                                <th> {{__('Remark')}}</th>
                                @if(\Auth::user()->type!='client')
                                    <th> {{__('Action')}}</th>
                                @else
                                    <th>{{__('User')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($timeSheets as $timeSheet)
                                <tr>
                                    <td class="">{{ !empty($timeSheet->task())? $timeSheet->task()->title : ''}}</td>
                                    @if(\Auth::user()->type == 'company')
                                        <td class="">{{ !empty($timeSheet->user())? $timeSheet->user()->name : ''}}</td>
                                    @endif
                                    <td class="">{{ !empty($timeSheet->project)? $timeSheet->project->name : ''}}</td>
                                    <td>{{ Auth::user()->dateFormat($timeSheet->date) }}</td>
                                    <td>{{ $timeSheet->hours }}</td>
                                    <td class="">{{ $timeSheet->remark }}</td>
                                    @if(\Auth::user()->type!='client')
                                        <td class="Action">
                                            <a href="#" class="edit-icon" data-url="{{ route('task.timesheet.edit',[$timeSheet->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Time Sheet')}}" >
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$timeSheet->id}}').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['task.timesheet.destroy', $timeSheet->id],'id'=>'delete-form-'.$timeSheet->id]) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    @else
                                        <td>{{!empty($timeSheet->user())?$timeSheet->user()->name:''}}</td>
                                    @endif
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
