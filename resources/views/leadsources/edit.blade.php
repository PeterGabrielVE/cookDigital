<div class="card bg-none card-box">
    {{ Form::model($leadsources, array('route' => array('leadsources.update', $leadsources->id), 'method' => 'PUT')) }}
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Lead Source Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
