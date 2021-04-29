@extends('layouts.admin')
@push('script-page')
    <script src="{{asset('assets/libs/dragula/dist/dragula.min.js')}}"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var stage_id = $(target).attr('data-id');

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '{{route('leads.order')}}',
                            type: 'POST',
                            data: {lead_id: id, stage_id: stage_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('{{__("Error")}}', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
@endpush
@section('page-title')
    {{__('Manage Lead')}}
@endsection

@section('action-button')
    @can('create lead')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('leads.create') }}" data-ajax-popup="true" data-title="{{__('Create New Lead')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @php
                $json = [];
                foreach ($stages as $stage){
                    $json[] = 'lead-list-'.$stage->id;
                }
            @endphp
            <div class="board" data-plugin="dragula" data-containers='{!! json_encode($json) !!}'>
                @foreach($stages as $stage)
                    @if(\Auth::user()->type == 'company')
                        @php($leads = $stage->leads)
                    @else
                        @php($leads = $stage->user_leads())
                    @endif
                    <div class="tasks">
                        <h5 class="mt-0 mb-0 task-header">{{$stage->name}} (<span class="count">{{count($leads)}}</span>)</h5>
                        <div id="lead-list-{{$stage->id}}" data-id="{{$stage->id}}" class="task-list-items for-leads mb-2">
                            @foreach($leads as $lead)
                                <div class="card mb-2 mt-0 pb-1" data-id="{{$lead->id}}">
                                    <div class="card-body p-0">
                                        @if(Gate::check('edit lead') || Gate::check('delete lead'))
                                            <div class="float-right">
                                                @if(!$lead->is_active)
                                                    <div class="dropdown global-icon lead-dropdown pr-1">
                                                        <a href="#" class="action-item" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @can('edit lead')
                                                                <a class="dropdown-item" data-url="{{ URL::to('leads/'.$lead->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Lead')}}" href="#">{{__('Edit')}}</a>
                                                            @endcan
                                                            @can('delete lead')
                                                                <a class="dropdown-item" href="#" data-title="{{__('Delete Lead')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$lead->id}}').submit();">{{__('Delete')}}</a>
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                {!! Form::close() !!}
                                                            @endcan
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="pl-2 pt-0 pr-2 pb-2">
                                            <h5 class="my-2">
                                                <a href="#" class="text-body">{{$lead->name}}</a>
                                            </h5>
                                            <p class="mb-0">
                                                <span class="text-nowrap mb-2 d-inline-block text-xs">{{$lead->notes}}</span>
                                            </p>
                                            <div class="row">
                                                <div class="col-6 text-xs">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ \Auth::user()->dateFormat($lead->created_at) }}</span>
                                                </div>
                                                <div class="col-6 text-right text-xs font-weight-bold">
                                                    <span>{{ \Auth::user()->priceFormat($lead->price) }}</span>
                                                </div>
                                                <div class="col-12 pt-2">
                                                    <p class="mb-0">
                                                        @if(\Auth::user()->type=='company')
                                                            <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                                <img alt="image" data-toggle="tooltip" data-original-title="{{(!empty($lead->user())?$lead->user()->name:'')}}" src="{{(!empty($lead->user()->avatar))? asset(Storage::url('avatar/'.$lead->user()->avatar)) : asset(Storage::url("avatar/avatar.png"))}}" class="rounded-circle " width="25" height="25">
                                                            </a>
                                                        @else
                                                            <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                                <img alt="image" data-toggle="tooltip" data-original-title="{{(!empty($lead->client())?$lead->client()->name:'')}}" src="{{(!empty($lead->user()->avatar))? asset(Storage::url('avatar/'.$lead->user()->avatar)) : asset(Storage::url("avatar/avatar.png"))}}" class="rounded-circle " width="25" height="25">
                                                            </a>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
