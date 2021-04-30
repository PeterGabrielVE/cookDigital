@extends('layouts.admin')
@section('page-title')
    {{__('Projects')}}
@endsection

@section('action-button')

        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="#" data-ajax-popup="true" data-title="{{__('Create Time Sheet')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
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
                                <th> {{__('Client')}}</th>
                                <th> {{__('Price')}}</th>
                                <th> {{__('Start Date')}}</th>
                                <th> {{__('Due Date')}}</th>
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
