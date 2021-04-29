<div class="card">
    <div class="card-body text-sm">
        <div class="p-2">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div>
                        <div class="font-weight-bold">{{ __('Milestone Title')}} :</div>
                        <p class="mt-1 lab-val">{{$milestone->title}}</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div>
                        <div class="font-weight-bold">{{ __('Milestone Description')}} :</div>
                        <p class="mt-1 lab-val">{{$milestone->description}}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="font-weight-bold">{{ __('Status')}} :</div>
                        <p class="mt-1 lab-val">
                            @if($milestone->status == 'incomplete')
                                <label class="label label-soft-warning">{{__('Incomplete')}}</label>
                            @endif
                            @if($milestone->status == 'complete')
                                <label class="label label-soft-success">{{__('Complete')}}</label>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="font-weight-bold">{{ __('Milestone Cost')}} :</div>
                        <p class="mt-1 lab-val">{{\App\Utility::getValByName('site_currency_symbol') .' '. number_format($milestone->cost)}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
