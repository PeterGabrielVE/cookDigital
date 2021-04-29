@extends('layouts.admin')

@push('css-page')
    <meta name="route" content="{{ $route }}">
    <meta name="url" content="{{ url('').'/'.config('chatify.path') }}" data-user="{{ Auth::user()->id }}">

    {{-- scripts --}}
    <script src="{{ asset('js/chatify/autosize.js') }}"></script>
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

    {{-- styles --}}
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
    <link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet"/>

    @include('messenger.layouts.messengerColor')
@endpush

@section('page-title')
    {{__('Chats')}}
@endsection

@section('content')
    <div class="card">
        <div class="messenger rounded min-h-750 overflow-hidden mt-2">
            <div class="messenger-listView">
                <div class="m-header">
                    <nav>
                        <nav class="m-header-right">
                            <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                        </nav>
                    </nav>
                    <input type="text" class="messenger-search" placeholder="{{__('Search')}}"/>
                    <div class="messenger-listView-tabs">
                        <a href="#" @if($route == 'user') class="active-tab" @endif data-view="users">
                            <span class="fas fa-clock" title="{{__('Recent')}}"></span>
                        </a>
                        <a href="#" @if($route == 'group') class="active-tab" @endif data-view="groups">
                            <span class="fas fa-users" title="{{__('Members')}}"></span>
                        </a>
                    </div>
                </div>
                <div class="m-body">
                    <div class="@if($route == 'user') show @endif messenger-tab app-scroll" data-view="users">
                        <p class="messenger-title">{{__('Favorites')}}</p>
                        <div class="messenger-favorites app-scroll-thin"></div>
                        {!! view('messenger.layouts.listItem', ['get' => 'saved','id' => $id])->render() !!}
                        <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);"></div>
                    </div>
                    <div class="all_members @if($route == 'group') show @endif messenger-tab app-scroll" data-view="groups">
                        <p style="text-align: center;color:grey;">{{__('Soon will be available')}}</p>
                    </div>
                    <div class="messenger-tab app-scroll" data-view="search">
                        <p class="messenger-title">{{__('Search')}}</p>
                        <div class="search-records">
                            <p class="message-hint"><span>{{__('Type to search..')}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="messenger-messagingView">
                <div class="m-header m-header-messaging">
                    <nav>
                        <div style="display: inline-block;">
                            <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i> </a>
                            @if(!empty(Auth::user()->avatar))
                                <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;background-image: url('{{ asset('/storage/avatar/'.Auth::user()->avatar) }}');"></div>
                            @else
                                <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;background-image: url('{{ asset('/storage/avatar/avatar.png') }}');"></div>
                            @endif
                            <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                        </div>
                        <nav class="m-header-right">
                            <a href="#" class="add-to-favorite my-lg-1 my-xl-1 mx-lg-3 mx-xl-3"><i class="fas fa-star"></i></a>
                            <a href="#" class="show-infoSide my-lg-1 my-xl-1 mx-lg-3 mx-xl-3"><i class="fas fa-info-circle"></i></a>
                        </nav>
                    </nav>
                </div>
                <div class="internet-connection">
                    <span class="ic-connected">{{__('Connected')}}</span>
                    <span class="ic-connecting">{{__('Connecting...')}}</span>
                    <span class="ic-noInternet">{{__('No internet access')}}</span>
                </div>
                <div class="m-body app-scroll">
                    <div class="messages">
                        <p class="message-hint" style="margin-top: calc(30% - 126.2px);"><span>{{__('Please select a chat to start messaging')}}</span></p>
                    </div>
                    <div class="typing-indicator">
                        <div class="message-card typing">
                            <p>
                            <span class="typing-dots">
                                <span class="dot dot-1"></span>
                                <span class="dot dot-2"></span>
                                <span class="dot dot-3"></span>
                            </span>
                            </p>
                        </div>
                    </div>
                    @include('messenger.layouts.sendForm')
                </div>
            </div>
            <div class="messenger-infoView app-scroll text-center">
                <nav class="text-left">
                    <a href="#"><i class="fas fa-times"></i></a>
                </nav>
                {!! view('messenger.layouts.info')->render() !!}
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    @include('messenger.layouts.modals')
    @include('messenger.layouts.footerLinks')
@endpush
