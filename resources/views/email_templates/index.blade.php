@extends('layouts.admin')

@section('page-title')
    {{__('Manage Email Templates')}}
@endsection

@push('script-page')
    <script type="text/javascript">
        @can('on-off email template')
        $(document).on("click", ".email-template-checkbox", function () {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'POST',
                success: function (response) {
                    if (response.is_success) {
                        show_toastr('{{__("Success")}}', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('{{__("Error")}}', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('{{__("Error")}}', response.error, 'error');
                    } else {
                        show_toastr('{{__("Error")}}', response, 'error');
                    }
                }
            })
        });
        @endcan
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped dataTable">
                        <thead>
                        <tr>
                            <th class="w-75">{{__('Name')}}</th>
                            <th class="text-center">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($EmailTemplates as $EmailTemplate)
                            <tr>
                                <td>{{ $EmailTemplate->name }}</td>
                                <td class="Action">
                                    <div class="row">
                                        @can('on-off email template')
                                            <div class="form-group col-6 text-right">
                                                <label class="switch ml-3">
                                                    <input type="checkbox" class="email-template-checkbox" name="site_enable_stripe" id="email_tempalte_{{$EmailTemplate->template->id}}" @if($EmailTemplate->template->is_active == 1) checked="checked" @endcan type="checkbox" value="{{$EmailTemplate->template->is_active}}" data-url="{{route('status.email.language',[$EmailTemplate->template->id])}}">
                                                    <span class="slider1 round"></span>
                                                </label>
                                            </div>
                                        @endcan
                                        @can('edit email template lang')
                                            <div class="col-6">
                                                <a href="{{ route('manage.email.language',[$EmailTemplate->id,\Auth::user()->currentLanguage()]) }}" class="edit-icon">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
