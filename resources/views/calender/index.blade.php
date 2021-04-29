@extends('layouts.admin')
@push('css-page')
    <link rel="stylesheet" href="{{asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')}}">
@endpush
@push('script-page')
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            var e, t, a = $('[data-toggle="event_calendar"]');
            a.length && (t = {
                header: {right: "", center: "", left: "",},
                buttonIcons: {prev: "calendar--prev", next: "calendar--next"},
                theme: !1,
                selectable: !0,
                selectHelper: !0,
                editable: false,
                events: {!! ($calenderData) !!},
                eventStartEditable: !1,
                locale: '{{basename(App::getLocale())}}',
                viewRender: function (t) {
                    e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
                },
            }, (e = a).fullCalendar(t),
                $("body").on("click", "[data-calendar-view]", function (t) {
                    t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                    var a = $(this).attr("data-calendar-view");
                    e.fullCalendar("changeView", a)
                }), $("body").on("click", ".fullcalendar-btn-next", function (t) {
                t.preventDefault(), e.fullCalendar("next")
            }), $("body").on("click", ".fullcalendar-btn-prev", function (t) {
                t.preventDefault(), e.fullCalendar("prev")
            }), $("body").on("click", ".fc-today-button", function (t) {
                t.preventDefault(), e.fullCalendar("today")
            }));

            $(document).on('click', '.fc-day-grid-event', function (e) {
                if ($(this).attr('href') != undefined) {
                    if (!$(this).hasClass('deal')) {
                        e.preventDefault();
                        var event = $(this);
                        var title = $(this).find('.fc-content .fc-title').html();
                        var size = 'md';
                        var url = $(this).attr('href');
                        var parts = url.split("/");
                        var last_part = parts[parts.length - 2];

                        if (last_part == 'invoices') {
                            window.location.href = url;
                        } else {
                            $("#commonModal .modal-title").html(title);
                            $("#commonModal .modal-dialog").addClass('modal-' + size);
                            $.ajax({
                                url: url,
                                success: function (data) {
                                    $('#commonModal .modal-body').html(data);
                                    $("#commonModal").modal('show');
                                },
                                error: function (data) {
                                    data = data.responseJSON;
                                    toastr('Error', data.error, 'error')
                                }
                            });
                        }
                    }
                }
            });
            $(document).on('click', '#form-comment button', function (e) {
                var comment = $.trim($("#form-comment textarea[name='comment']").val());
                var name = '{{\Auth::user()->name}}';
                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {comment: comment, "_token": $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);

                            var html = "<li class='media mb-20'>" +
                                "                    <div class='media-body'>" +
                                "                    <div class='d-flex justify-content-between align-items-end'><div>" +
                                "                        <h5 class='mt-0'>" + name + "</h5>" +
                                "                        <p class='mb-0 text-xs'>" + data.comment + "</p></div>" +
                                "                           <div class='comment-trash' style=\"float: right\">" +
                                "                               <a href='#' class='btn btn-outline btn-sm text-danger delete-comment' data-url='" + data.deleteUrl + "' >" +
                                "                                   <i class='fas fa-trash'></i>" +
                                "                               </a>" +
                                "                           </div>" +
                                "                    </div>" +
                                "                    </div>" +
                                "                </li>";

                            $("#comments").prepend(html);
                            $("#form-comment textarea[name='comment']").val('');
                            show_toastr('{{__("Success")}}', '{{ __("Comment Added Successfully!")}}', 'success');
                        },
                        error: function (data) {
                            show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    });
                } else {
                    show_toastr('{{__("Error")}}', '{{ __("Please write comment!")}}', 'error');
                }
            });
            $(document).on("click", ".delete-comment", function () {
                if (confirm('Are You Sure ?')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        dataType: 'JSON',
                        success: function (data) {
                            show_toastr('{{__("Success")}}', '{{ __("Comment Deleted Successfully!")}}', 'success');
                            btn.closest('.media').remove();
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{__("Error")}}', data.message, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                            }
                        }
                    });
                }
            });
            $(document).on('submit', '#form-file', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $("#form-file").data('url'),
                    type: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        show_toastr('{{__("Success")}}', '{{ __("Comment Added Successfully!")}}', 'success');
                        var delLink = '';

                        if (data.deleteUrl.length > 0) {
                            delLink = "<a href='#' class='btn btn-outline btn-sm text-danger delete-comment-file m-0 px-2' data-id='" + data.id + "' data-url='" + data.deleteUrl + "'>" +
                                "                                        <i class='fas fa-trash'></i>" +
                                "                                    </a>";
                        }

                        var html = '<div class="col-8 mb-2 file-' + data.id + '">' +
                            '                                <h5 class="mt-0 mb-1 font-weight-bold text-sm">' + data.name + '</h5>' +
                            '                                <p class="m-0 text-xs">' + data.file_size + '</p>' +
                            '                            </div>' +
                            '                            <div class="col-4 mb-2 file-' + data.id + '">' +
                            '                                <div class="comment-trash" style="float: right">' +
                            '                                    <a download href="{{asset(Storage::url('tasks'))}}/' + data.file + '" class="btn btn-outline btn-sm text-primary m-0 px-2">' +
                            '                                        <i class="fa fa-download"></i>' +
                            '                                    </a>' + delLink +
                            '                                </div>' +
                            '                            </div>';

                        $("#comments-file").prepend(html);
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{__("Error")}}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-comment-file", function () {
                if (confirm('Are You Sure ?')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        dataType: 'JSON',
                        success: function (data) {
                            show_toastr('{{__("Success")}}', '{{ __("File Deleted Successfully!")}}', 'success');
                            $('.file-' + btn.attr('data-id')).remove();
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{__("Error")}}', data.message, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                            }
                        }
                    });
                }
            });
            $(document).on('click', '.submit-checklist', function () {
                $('#form-checklist').submit();
            });
            $(document).on('submit', '#form-checklist', function (e) {
                e.preventDefault();
                if ($('.checklist-name').val() != '') {
                    $.ajax({
                        url: $("#form-checklist").data('action'),
                        type: 'POST',
                        data: new FormData(this),
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (data) {
                            show_toastr('{{__("Success")}}', '{{ __("Checklist Added Successfully!")}}', 'success');

                            var html = '<li class="media">' +
                                '<div class="media-body">' +
                                '<h5 class="mt-0 mb-1 font-weight-bold"> </h5> ' +
                                '<div class=" custom-control custom-checkbox checklist-checkbox"> ' +
                                '<input type="checkbox" id="checklist-' + data.id + '" class="custom-control-input"  data-url="' + data.updateUrl + '">' +
                                '<label for="checklist-' + data.id + '" class="custom-control-label"></label> ' +
                                '' + data.name + ' </div>' +
                                '<div class="comment-trash" style="float: right"> ' +
                                '<a href="#" class="btn btn-outline btn-sm red text-muted delete-checklist" data-url="' + data.deleteUrl + '">\n' +
                                '                                                            <i class="fas fa-trash"></i>' +
                                '</a>' +
                                '</div>' +
                                '</div>' +
                                ' </li>';

                            var html = '<li class="media">' +
                                '<div class="media-body">' +
                                '<h5 class="mt-0 mb-1 font-weight-bold"> </h5> ' +
                                '<div class="row"> ' +
                                '<div class="col-8"> ' +
                                '<div class="custom-control custom-checkbox checklist-checkbox"> ' +
                                '<input type="checkbox" id="checklist-' + data.id + '" class="custom-control-input"  data-url="' + data.updateUrl + '">' +
                                '<label for="checklist-' + data.id + '" class="custom-control-label">' + data.name + '</label> ' +
                                ' </div>' +
                                '</div> ' +
                                '<div class="col-4"> ' +
                                '<div class="comment-trash text-right"> ' +
                                '<a href="#" class="btn btn-outline btn-sm text-danger delete-checklist" data-url="' + data.deleteUrl + '">' +
                                '<i class="fas fa-trash"></i>' +
                                '</a>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                ' </li>';

                            $("#check-list").prepend(html);
                            $("#form-checklist input[name=name]").val('');
                            $("#form-checklist").collapse('toggle');
                        },
                    });
                } else {
                    show_toastr('{{__("Error")}}', '{{ __("Checklist name is required")}}', 'error');
                }
            });
            $(document).on("click", ".delete-checklist", function () {
                if (confirm('Are You Sure ?')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        dataType: 'JSON',
                        success: function (data) {
                            show_toastr('{{__("Success")}}', '{{ __("Checklist Deleted Successfully!")}}', 'success');
                            btn.closest('.media').remove();
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{__("Error")}}', data.message, 'error');
                            } else {
                                show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                            }
                        }
                    });
                }
            });

            var checked = 0;
            var count = 0;
            var percentage = 0;

            $(document).on("change", "#check-list input[type=checkbox]", function () {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('{{__("Error")}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                });
                taskCheckbox();
            });
        });
    </script>
@endpush
@section('page-title')
    {{__('Calendar')}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card author-box card-primary">
                <div class="card-header">
                    <div class="row justify-content-between align-items-center full-calender">
                        <div class="col d-flex align-items-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </div>
                            <h5 class="fullcalendar-title h4 d-inline-block font-weight-400 mb-0"></h5>
                        </div>
                        <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button class="fc-today-button btn btn-sm btn-neutral" type="button">{{__('Today')}}</button>
                            </div>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="#" class="btn btn-sm btn-neutral active" data-calendar-view="month">{{__('Month')}}</a>
                                <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicWeek">{{__('Week')}}</a>
                                <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicDay">{{__('Day')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id='calendar-container'>
                        <div id='calendar' data-toggle="event_calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
