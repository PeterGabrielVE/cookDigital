<div class="card bg-none card-box">
    {{ Form::open(array('route' => array('invoice.custom.mail',$invoice_id))) }}
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('email', __('Email'),['class'=>'form-control-label']) }}
            {{ Form::text('email', '', array('class' => 'form-control','required'=>'required')) }}
            @error('email')
            <span class="invalid-email" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-md-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
