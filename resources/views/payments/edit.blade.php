<div class="card bg-none card-box">
    {{ Form::model($payment, array('route' => array('payments.update', $payment->id), 'method' => 'PUT','enctype' => "multipart/form-data")) }}
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Payment Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
