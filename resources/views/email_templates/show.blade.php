@extends('layouts.admin')
@section('page-title')
    {{ $emailTemplate->name }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('assets/libs/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('assets/libs/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ route('email_template.index') }}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-arrow-left"></i> {{__('Back')}} </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    {{Form::model($emailTemplate, array('route' => array('email_template.update', $emailTemplate->id), 'method' => 'PUT')) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{Form::label('name',__('Name'),['class'=>'form-control-label text-dark'])}}
                            {{Form::text('name',null,array('class'=>'form-control ','disabled'=>'disabled'))}}
                        </div>
                        <div class="form-group col-md-12">
                            {{Form::label('from',__('From'),['class'=>'form-control-label text-dark'])}}
                            {{Form::text('from',null,array('class'=>'form-control ','required'=>'required'))}}
                        </div>
                        {{Form::hidden('lang',$currEmailTempLang->lang,array('class'=>''))}}
                        @can('edit email template')
                            <div class="col-12 text-right">
                                <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                            </div>
                        @endcan
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row text-xs">
                        <div class="col-6">
                            <h6 class="font-weight-bold">{{__('Project')}}</h6>
                            <p class="mb-1">{{__('Project Name')}} : <span class="pull-right text-primary">{project_name}</span></p>
                            <p class="mb-1">{{__('Project Label')}} : <span class="pull-right text-primary">{project_label}</span></p>
                            <p class="mb-1">{{__('Project Status')}} : <span class="pull-right text-primary">{project_status}</span></p>
                        </div>
                        <div class="col-6">
                            <h6 class="font-weight-bold">{{__('Task')}}</h6>
                            <p class="mb-1">{{__('Task Name')}} : <span class="pull-right text-primary">{task_name}</span></p>
                            <p class="mb-1">{{__('Task Priority')}} : <span class="pull-right text-primary">{task_priority}</span></p>
                            <p class="mb-1">{{__('Task Status')}} : <span class="pull-right text-primary">{task_status}</span></p>
                            <p class="mb-1">{{__('Task Old Stage')}} : <span class="pull-right text-primary">{task_old_stage}</span></p>
                            <p class="mb-1">{{__('Task New Stage')}} : <span class="pull-right text-primary">{task_new_stage}</span></p>
                        </div>
                        @if($emailTemplate->name == 'Assign Estimation')
                            <div class="col-6">
                                <h6 class="font-weight-bold">{{__('Estimation')}}</h6>
                                <p class="mb-1">{{__('Estimation Id')}} : <span class="pull-right text-primary">{estimation_name}</span></p>
                                <p class="mb-1">{{__('Estimation Client')}} : <span class="pull-right text-primary">{estimation_client}</span></p>
                                <p class="mb-1">{{__('Estimation Status')}} : <span class="pull-right text-primary">{estimation_status}</span></p>
                            </div>
                        @endif
                        <div class="col-6">
                            <h6 class="font-weight-bold">{{__('Other')}}</h6>
                            <p class="mb-1">{{__('App Name')}} : <span class="pull-right text-primary">{app_name}</span></p>
                            <p class="mb-1">{{__('Company Name')}} : <span class="pull-right text-primary">{company_name}</span></p>
                            <p class="mb-1">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                            <p class="mb-1">{{__('Email')}} : <span class="pull-right text-primary">{email}</span></p>
                            <p class="mb-1">{{__('Password')}} : <span class="pull-right text-primary">{password}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="language-wrap">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 language-list-wrap">
                                <div class="language-list">
                                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                        @foreach($languages as $lang)
                                            <li class="text-sm font-weight-bold">
                                                <a href="{{route('manage.email.language',[$emailTemplate->id,$lang])}}" class="nav-link {{($currEmailTempLang->lang == $lang)?'active':''}}">{{Str::upper($lang)}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12 language-form-wrap">
                                {{Form::model($currEmailTempLang, array('route' => array('store.email.language',$currEmailTempLang->parent_id), 'method' => 'POST')) }}
                                <div class="row">
                                    <div class="form-group col-12">
                                        {{Form::label('subject',__('Subject'),['class'=>'form-control-label text-dark'])}}
                                        {{Form::text('subject',null,array('class'=>'form-control ','required'=>'required'))}}
                                    </div>
                                    <div class="form-group col-12">
                                        {{Form::label('content',__('Email Message'),['class'=>'form-control-label text-dark'])}}
                                        {{Form::textarea('content',$currEmailTempLang->content,array('class'=>'summernote-simple','required'=>'required'))}}
                                    </div>
                                    @can('edit email template lang')
                                        <div class="col-md-12 text-right">
                                            {{Form::hidden('lang',null)}}
                                            <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                        </div>
                                    @endcan
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

