@extends('layouts.admin')
@section('page-title')
    {{__('Manage Invoice')}}
@endsection

@section('action-button')
    @can('create invoice')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('invoices.create') }}" data-ajax-popup="true" data-title="{{__('Create Invoice')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
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
                                <th> {{__('Invoice')}}</th>
                                <th> {{__('Project')}}</th>
                                <th> {{__('Issue Date')}}</th>
                                <th> {{__('Due Date')}}</th>
                                <th> {{__('Value')}}</th>
                                <th> {{__('Status')}}</th>
                                @if(Gate::check('edit invoice') || Gate::check('delete invoice'))
                                    <th> {{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="Id">
                                        <a href="{{ route('invoices.show',$invoice->id) }}">{{ Utility::invoiceNumberFormat($invoice->id) }}</a>
                                    </td>
                                    <td>{{ (isset($invoice->project) && !empty($invoice->project)) ? $invoice->project->name : '-' }}</td>
                                    <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                    <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                    <td>{{ Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                    <td>
                                        @if($invoice->status == 0)
                                            <span class="label label-soft-primary">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span class="label label-soft-danger">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span class="label label-soft-warning">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 3)
                                            <span class="label label-soft-success">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 4)
                                            <span class="label label-soft-info">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                        @endif
                                    </td>
                                    @if(Gate::check('edit invoice') || Gate::check('delete invoice'))
                                        <td class="Action">
                                            <span>
                                            @can('show invoice')
                                                    <a href="{{ route('invoices.show',$invoice->id) }}" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="{{__('Detail')}}">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('edit invoice')
                                                    <a href="#" data-url="{{ route('invoices.edit',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Edit Invoice')}}" class="edit-icon" >
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan
                                                @can('delete invoice')
                                                    <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$invoice->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]) !!}
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
