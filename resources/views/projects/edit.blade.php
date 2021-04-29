<div class="card bg-none card-box">
    {{ Form::model($project, array('route' => array('projects.update', $project->id), 'method' => 'PUT')) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Projects Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('price', __('Projects Price'),['class'=>'form-control-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Due Date'),['class'=>'form-control-label']) }}
            {{ Form::text('date', $project->due_date, array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('client', __('Client'),['class'=>'form-control-label']) }}
            {!! Form::select('client', $clients, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('lead', __('Lead'),['class'=>'form-control-label']) }}
            {!! Form::select('lead', $leads, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('label', __('Label'),['class'=>'form-control-label']) }}
            <div class="bg-color-label">
                @foreach($labels as $k=>$label)
                    <div class="custom-control custom-radio {{$label->color}} mb-3">
                        <input class="custom-control-input" name="label" id="customCheck_{{$k}}" type="radio" value="{{$label->id}}" {{($label->id==$project->label)?'checked':''}}>
                        <label class="custom-control-label" for="customCheck_{{$k}}"></label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control ','rows'=>'2']) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
