<div class="card bg-none card-box">
    {{ Form::model($invoice, array('route' => array('invoices.payments.store', $invoice->id), 'method' => 'POST')) }}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('amount', __('Amount'),['class'=>'form-control-label']) }}
                {{ Form::number('amount', $invoice->getDue(), array('class' => 'form-control','required'=>'required','min'=>'0',"step"=>"0.01")) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Payment Date'),['class'=>'form-control-label']) }}
                {{ Form::text('date', null, array('class' => 'form-control datepicker','required'=>'required')) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('payment_id', __('Payment Method'),['class'=>'form-control-label']) }}
                {{ Form::select('payment_id', $payment_methods,null, array('class' => 'form-control  select2','required'=>'required')) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'),['class'=>'form-control-label']) }}
                {{ Form::textarea('notes', null, array('class' => 'form-control','rows'=>'2')) }}
            </div>
        </div>
        <div class="col-md-12 text-right">
            <input type="submit" value="{{__('Add')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
