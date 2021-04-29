@extends('layouts.admin')
@section('page-title')
    {{__('Manage Expense')}}
@endsection

@section('action-button')
    @can('create lead source')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('expenses.create') }}" data-ajax-popup="true" data-title="{{__('Create Expense')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
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
                                <th>{{__('Category')}}</th>
                                <th width="40%"> {{__('Description')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Project')}}</th>
                                <th>{{__('User')}}</th>
                                <th>{{__('Attachment')}}</th>
                                @if(Gate::check('edit expense') || Gate::check('delete expense'))
                                    <th> {{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{  (!empty($expense->category)?$expense->category->name:'')}}</td>
                                    <td>{{ (!empty($expense->description) ? $expense->description : '-') }}</td>
                                    <td>{{ Auth::user()->priceFormat($expense->amount) }} </td>
                                    <td>{{ Auth::user()->dateFormat($expense->date) }}</td>
                                    <td>{{ (!empty($expense->projects)?$expense->projects->name:'')  }}</td>
                                    <td>{{ (!empty($expense->user)?$expense->user->name:'') }}</td>
                                    <td class="Action">
                                        @if($expense->attachment)
                                            <span>
                                                <a href="{{asset(Storage::url('attachment/'. $expense->attachment))}}" download="" class="edit-icon bg-info" data-toggle="tooltip" data-original-title="{{__('Download')}}">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                        </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if(Gate::check('edit expense') || Gate::check('delete expense'))
                                        <td class="Action">
                                            <span>
                                            @can('edit expense')
                                                    <a href="#" class="edit-icon" data-url="{{ route('expenses.edit',$expense->id) }}" data-ajax-popup="true" data-title="{{__('Edit Expense')}}" >
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan
                                                @can('delete expense')
                                                    <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$expense->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['expenses.destroy', $expense->id],'id'=>'delete-form-'.$expense->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </span>
                                        </td>
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
