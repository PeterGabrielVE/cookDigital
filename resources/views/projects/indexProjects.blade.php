@extends('layouts.admin')
@section('page-title')
    {{__('Projects')}}
@endsection
@section('action-button')

        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-4">
                <a {{ (Request::segment(1) == 'projects')?'active open':''}}" href="{{ route('projects.index') }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-tasks"></i></a>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-4">
                <a href="{{ route('get.all.projects') }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only btn-success width-auto" style="background-color: green"><i class="fas fa-tasks"></i></a>
            </div>
            @can('create project')
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-4">
                    <a href="#" data-url="{{ route('projects.create') }}" data-ajax-popup="true" data-title="{{__('Create New Project')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
                </div>
            @endcan
        </div>

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
                                <th> {{__('Name')}}</th>

                                @if(\Auth::user()->type!='client')
                                    <th> {{__('Action')}}</th>
                                @else
                                    <th>{{__('User')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($projects as $proj)
                                <tr>
                                    <td class="">{{ !empty($proj->name)? $proj->name : ''}}</td>
                                    <td> <a href="{{ route('projects.show',$proj->id) }}" class="btn btn-sm btn-white btn-icon-only width-auto">
                                        {{__('Detail')}}
                                    </a></td>
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
