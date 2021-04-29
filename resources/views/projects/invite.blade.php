<div class="card bg-none card-box">
    {{ Form::open(array('route' => array('invite',$project_id))) }}
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('user', __('User'),['class'=>'form-control-label']) }}
            {!! Form::select('user[]', $employee, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Add')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
