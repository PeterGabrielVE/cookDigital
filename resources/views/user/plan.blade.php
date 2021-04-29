<div class="card bg-none card-box">
    <div class="table-responsive">
        <table class="table table-striped text-center">
            <tr>
                <th>{{__('Name / Price')}}</th>
                <th>{{__('Users')}}</th>
                <th>{{__('Clients')}}</th>
                <th>{{__('Projects')}}</th>
                <th>{{__('Action')}}</th>
            </tr>
            @foreach($plans as $plan)
                <tr>
                    <td>{{$plan->name}} <br> <small>{{env('CURRENCY_SYMBOL').$plan->price}} {{' / '. $plan->duration}}</small></td>
                    <td>{{$plan->max_users}}</td>
                    <td>{{$plan->max_clients}}</td>
                    <td>{{$plan->max_projects}}</td>
                    <td>
                        @if($user->plan == $plan->id)
                            <div class="btn btn-success btn-xs rounded-pill my-auto w-100">{{__('Active')}}</div>
                        @else
                            <a href="{{route('plan.active',[$user->id,$plan->id])}}" class="btn btn-primary btn-xs rounded-pill my-auto w-100" title="{{__('Click to Upgrade Plan')}}"><i class="fas fa-cart-plus"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
