<div class="card bg-none card-box">
    <form class="pl-3 pr-3" method="post" action="{{ route('test.email.send') }}">
        @csrf
        <div class="row">
            <div class="col-12 form-group">
                <label for="email" class="form-control-label">{{ __('E-Mail Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" required/>
            </div>
            <div class="col-12 form-group text-right">
                <input type="submit" value="{{__('Send Test Mail')}}" class="btn-create badge-blue">
                <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
