@extends('layouts.admin')
@php
    $dir= asset(Storage::url('plan'));
@endphp
@section('page-title')
    {{__('Manage Note')}}
@endsection

@section('action-button')
    @can('create lead source')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('notes.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create Note')}}"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="staff-wrap">
                <div class="row">
                    @foreach($notes as $note)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card profile-card pt-0">
                                <div class="row">
                                    <div class="col-10">
                                        <div class="custom-control custom-radio mb-3 {{$note->color}} font-weight-bold">
                                            <label class="custom-control-label "></label>
                                            {{$note->title}}
                                        </div>
                                    </div>
                                    <div class="col-2 text-right">
                                        <div class="dropdown action-item pt-0">
                                            <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(-166px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                @can('edit note')
                                                    <a href="#" class="dropdown-item" data-url="{{ route('notes.edit',$note->id) }}" data-ajax-popup="true" data-title="{{__('Edit Note')}}">{{__('Edit')}}</a>
                                                @endcan
                                                @can('delete note')
                                                    <a class="dropdown-item" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$note->id}}').submit();">{{__('Delete')}}</a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['notes.destroy', $note->id],'id'=>'delete-form-'.$note->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-justify text-sm">
                                        {{$note->text}}
                                        <br><br>
                                        <b>{{\Auth::user()->dateFormat($note->created_at)}}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
