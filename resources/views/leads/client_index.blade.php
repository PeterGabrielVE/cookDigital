@extends('layouts.admin')

@section('page-title')
    {{__('Manage Lead')}}
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
                                <th> {{__('Price')}}</th>
                                <th>{{__('User Assign')}} </th>
                                <th>{{__('Stage')}} </th>
                                <th>{{__('Notes')}} </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($leads as $lead)
                                <tr>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->price }}</td>
                                    <td>{{ (!empty($lead->user())?$lead->user()->name:'') }}</td>
                                    <td>{{ $lead->stage_name }}</td>
                                    <td>{{ $lead->notes }}</td>
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
