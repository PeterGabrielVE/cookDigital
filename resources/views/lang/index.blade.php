@extends('layouts.admin')
@section('page-title')
    {{__('Manage Language')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('create language')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create New Language')}}" data-url="{{route('create.language')}}"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
            @if($currantLang != \App\Utility::settings()['default_language'])
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                    <a href="#" class="btn btn-xs btn-white btn-icon-only bg-red width-auto" data-toggle="tooltip" data-original-title="{{__('Delete This Language')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$currantLang}}').submit();"><i class="fas fa-trash"></i> {{__('Delete')}}</a>
                    {!! Form::open(['method' => 'DELETE', 'route' => ['destroy.language', $currantLang],'id'=>'delete-form-'.$currantLang]) !!}
                    {!! Form::close() !!}
                </div>
            @endif
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" role="tablist">
                        @foreach($languages as $lang)
                            <li class="nav-item">
                                <a href="{{route('manage.language',[$lang])}}" class="nav-link {{($currantLang == $lang)?'active':''}} text-sm font-weight-bold">
                                    {{Str::upper($lang)}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li>
                            <a class="active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ __('Labels')}}</a>
                        </li>
                        <li>
                            <a id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ __('Messages')}}</a>
                        </li>
                    </ul>
                    @can('create language')
                        <form method="post" action="{{route('store.language.data',[$currantLang])}}">
                            @csrf
                            @endcan
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="row">
                                        @foreach($arrLabel as $label => $value)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label text-dark">{{$label}} </label>
                                                    <input type="text" class="form-control" name="label[{{$label}}]" value="{{$value}}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        @foreach($arrMessage as $fileName => $fileValue)
                                            <div class="col-lg-12">
                                                <h4>{{ucfirst($fileName)}}</h4>
                                            </div>
                                            @foreach($fileValue as $label => $value)
                                                @if(is_array($value))
                                                    @foreach($value as $label2 => $value2)
                                                        @if(is_array($value2))
                                                            @foreach($value2 as $label3 => $value3)
                                                                @if(is_array($value3))
                                                                    @foreach($value3 as $label4 => $value4)
                                                                        @if(is_array($value4))
                                                                            @foreach($value4 as $label5 => $value5)
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group">
                                                                                        <label class="form-control-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}.{{$label5}}</label>
                                                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}][{{$label5}}]" value="{{$value5}}">
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <div class="col-lg-6">
                                                                                <div class="form-group">
                                                                                    <label class="form-control-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}</label>
                                                                                    <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}]" value="{{$value4}}">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group">
                                                                            <label class="form-control-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}</label>
                                                                            <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}]" value="{{$value3}}">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label class="form-control-label text-dark">{{$fileName}}.{{$label}}.{{$label2}}</label>
                                                                    <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}]" value="{{$value2}}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label text-dark">{{$fileName}}.{{$label}}</label>
                                                            <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}]" value="{{$value}}">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @can('create language')
                                <div class="form-group col-12 text-right">
                                    <input type="submit" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                </div>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

