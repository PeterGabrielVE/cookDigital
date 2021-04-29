<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $logo=asset(Storage::url('logo/'));
    $favicon=Utility::getValByName('company_favicon');
@endphp
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('page-title') &dash; {{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'WorkGo')}}</title>
    <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')}}" type="image">

    @stack('css-page')

    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body class="application application-offset">
<div class="container-fluid container-application">
    @include('partials.admin.menu')
    <div class="main-content position-relative">
        @include('partials.admin.header')
        <div class="page-content">
            <div class="page-title">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                        <div class="d-inline-block">
                            <h5 class="h4 d-inline-block font-weight-400 mb-0 ">@yield('page-title')</h5>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                        @yield('action-button')
                    </div>
                </div>
            </div>
            @yield('content')
        </div>
        <footer class="main-footer bottom-0 px-4 py-4">
            <div class="footer-left">
                {{__('Copyright')}} &copy; {{ (Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'WorkGo') }} {{date('Y')}}
            </div>
            <div class="footer-right">
            </div>
        </footer>
    </div>
</div>

<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div>
                <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal" aria-label="Close">{{__('Close')}}</a>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="omnisearch" class="omnisearch">
    <div class="container">
        <div class="omnisearch-form">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control search_keyword" placeholder="{{__('Type and search By Project & Tasks.')}}">
                </div>
            </div>
        </div>
        <div class="omnisearch-suggestions">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-unstyled mb-0 search-output text-sm">
                        <li>
                            <a class="list-link pl-4" href="#">
                                <i class="fas fa-search"></i>
                                <span>{{__('Type and search By Project & Tasks.')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- General JS Scripts -->
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/js/site.core.js') }}"></script>
<script src="{{ asset('assets/libs/progressbar.js/dist/progressbar.min.js') }}"></script>
<script src="{{ asset('assets/libs/chart/Chart.min.js') }}"></script>
<script src="{{ asset('assets/libs/chart/Chart.extension.js') }}"></script>
<script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/site.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>


{{-- Pusher JS--}}
@if(\Auth::user()->type != 'super admin')
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script>
        $(document).ready(function () {
            pushNotification('{{ Auth::id() }}');
        });

        function pushNotification(id) {

            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = false;

            var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
                cluster: '{{env('PUSHER_APP_CLUSTER')}}',
                forceTLS: true
            });

            var channel = pusher.subscribe('send_notification');
            channel.bind('notification', function (data) {
                if (id == data.user_id) {
                    $(".notification-toggle").addClass('beep');
                    $(".notification-dropdown #notification-list").prepend(data.html);
                }
            });

            // Pusher Message
            var msgChannel = pusher.subscribe('my-channel');
            msgChannel.bind('my-chat', function (data) {
                console.log(data);
                if (id == data.to) {
                    getChat();
                }
            });
        }

        // Mark As Read Notification
        $(document).on("click", ".mark_all_as_read", function () {
            $.ajax({
                url: '{{route('notification.seen',\Auth::user()->id)}}',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.notification-dropdown #notification-list').html('');
                    $(".notification-toggle").removeClass('beep');
                }
            })
        });

        // Get chat for top ox
        function getChat() {
            $.ajax({
                url: '{{route('message.data')}}',
                type: "get",
                cache: false,
                success: function (data) {
                    if (data.length != 0) {
                        $(".message-toggle-msg").addClass('beep');
                        $(".dropdown-list-message-msg").html(data);
                    }
                }
            })
        }

        getChat();

        $(document).on("click", ".mark_all_as_read_message", function () {
            $.ajax({
                url: '{{route('message.seen')}}',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.dropdown-list-message-msg').html('');
                    $(".message-toggle-msg").removeClass('beep');
                }
            })
        });
    </script>
@endif

<script>
    var date_picker_locale = {
        format: 'YYYY-MM-DD',
        daysOfWeek: [
            "{{__('Sun')}}",
            "{{__('Mon')}}",
            "{{__('Tue')}}",
            "{{__('Wed')}}",
            "{{__('Thu')}}",
            "{{__('Fri')}}",
            "{{__('Sat')}}"
        ],
        monthNames: [
            "{{__('January')}}",
            "{{__('February')}}",
            "{{__('March')}}",
            "{{__('April')}}",
            "{{__('May')}}",
            "{{__('June')}}",
            "{{__('July')}}",
            "{{__('August')}}",
            "{{__('September')}}",
            "{{__('October')}}",
            "{{__('November')}}",
            "{{__('December')}}"
        ],
    };

    $(document).ready(function () {
        if ($('.dataTable').length > 0) {
            $(".dataTable").dataTable({
                language: {
                    "lengthMenu": "{{__('Display')}} _MENU_ {{__('records per page')}}",
                    "zeroRecords": "{{__('No data available in table')}}",
                    "info": "{{__('Showing page')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No page available')}}",
                    "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total records')}})",
                    "paginate": {
                        "previous": "{{__('Previous')}}",
                        "next": "{{__('Next')}}",
                        "last": "{{__('Last')}}"
                    }
                },
            })
        }

        @if(Auth::user()->type != 'super admin')
        $(document).on('keyup', '.search_keyword', function () {
            search_data($(this).val());
        });
        @endif
    })

    @if(Auth::user()->type != 'super admin')
    // Common main search
    var currentRequest = null;

    function search_data(keyword = '') {
        currentRequest = $.ajax({
            url: '{{ route('search.json') }}',
            data: {keyword: keyword},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                $('.search-output').html(data);
            }
        });
    }
    @endif
</script>

<script src="{{asset('assets/js/custom.js')}}"></script>

@stack('script-page')

@if ($message = Session::get('success'))
    <script>show_toastr('{{__("Success")}}', '{!! $message !!}', 'success')</script>
@endif

@if ($message = Session::get('error'))
    <script>show_toastr('Error', '{!! $message !!}', 'error')</script>
@endif

@if ($message = Session::get('info'))
    <script>show_toastr('{{__("Info")}}', '{!! $message !!}', 'info')</script>
@endif
</body>
</html>
