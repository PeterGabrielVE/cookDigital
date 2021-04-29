@extends('layouts.admin')
@section('page-title')
    {{__('Manage Bug Status')}}
@endsection
@push('script-page')
    <script src="{{ asset('assets/libs/jquery-ui/jquery-ui.js') }}"></script>
    <script>
        $(function () {
            $(".sortable").sortable();
            $(".sortable").disableSelection();
            $(".sortable").sortable({
                stop: function () {
                    var order = [];
                    $(this).find('li').each(function (index, data) {
                        order[index] = $(data).attr('data-id');
                    });
                    $.ajax({
                        url: "{{route('bugstatus.order')}}",
                        data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        success: function (data) {
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{__("Error")}}', data.error, 'error')
                        }
                    })
                }
            });
        });
    </script>
@endpush

@section('action-button')
    @can('create bug status')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('bugstatus.create') }}" data-ajax-popup="true" data-title="{{__('Create Bug Status')}}"><i class="fas fa-plus"></i>{{__('Create')}}</a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group sortable">
                        @foreach ($bugStatus as $bug)
                            <li class="list-group-item" data-id="{{$bug->id}}">
                                <div class="row">
                                    <div class="col-6 text-xs text-dark">{{$bug->title}}</div>
                                    <div class="col-4 text-xs text-dark">{{$bug->created_at}}</div>
                                    <div class="col-2">
                                        @can('edit bug status')
                                            <a href="#" data-url="{{ URL::to('bugstatus/'.$bug->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Bug Status')}}" class="edit-icon">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('delete bug status')
                                            <a href="#" class="delete-icon" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$bug->id}}').submit();"><i class="fas fa-trash"></i></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['bugstatus.destroy', $bug->id],'id'=>'delete-form-'.$bug->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
