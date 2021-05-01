@extends('layouts.admin')
@section('page-title')
    {{__('Projects')}}
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
