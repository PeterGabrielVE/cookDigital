<div class="card bg-none card-box">
    {{ Form::model($lead, array('route' => array('leads.update', $lead->id), 'method' => 'PUT')) }}
    <div class="row ">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('price', __('Price'),['class'=>'form-control-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('stage', __('Stage'),['class'=>'form-control-label']) }}
            {{ Form::select('stage', $stages,null, array('class' => 'form-control  select2','required'=>'required')) }}
        </div>
        @if(\Auth::user()->type=='company')
            <div class="form-group  col-md-6">
                {{ Form::label('owner', __('Lead User'),['class'=>'form-control-label']) }}
                {!! Form::select('owner', $owners, null,array('class' => 'form-control  select2','required'=>'required')) !!}
            </div>
        @endif

        <div class="form-group col-md-6">
            {{ Form::label('client', __('Client'),['class'=>'form-control-label']) }}
            {!! Form::select('client', $clients, null,array('class' => 'form-control  select2','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('source', __('Source'),['class'=>'form-control-label']) }}
            {!! Form::select('source', $sources, null,array('class' => 'form-control  select2','required'=>'required')) !!}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('notes', __('Notes'),['class'=>'form-control-label']) }}
            {!! Form::textarea('notes', null,array('class' => 'form-control ','rows'=>'3')) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
