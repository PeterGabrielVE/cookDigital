<div class="card bg-none card-box">
    {{ Form::open(array('url' => 'taxes')) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Tax Rate Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('rate', __('Tax Rate %'),['class'=>'form-control-label']) }}
            {{ Form::number('rate', '', array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
