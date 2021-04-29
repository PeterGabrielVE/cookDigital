<div class="card bg-none card-box">
    {{ Form::open(array('url' => 'invoices')) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('project_id', __('Project'),['class'=>'form-control-label']) }}
            {{ Form::select('project_id', $projects,null, array('class' => 'form-control select2')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('issue_date', __('Issue Date'),['class'=>'form-control-label']) }}
            {{ Form::text('issue_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class'=>'form-control-label']) }}
            {{ Form::text('due_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('tax_id', __('Tax %'),['class'=>'form-control-label']) }}
            {{ Form::select('tax_id', $taxes,null, array('class' => 'form-control select2')) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('terms', __('Terms'),['class'=>'form-control-label']) }}
            {!! Form::textarea('terms', null, ['class'=>'form-control','rows'=>'2']) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
