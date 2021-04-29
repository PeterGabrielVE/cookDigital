<div class="card bg-none card-box">
    {{ Form::open(array('url' => 'expenses','enctype' => "multipart/form-data")) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-control-label']) }}
            {{ Form::select('category_id', $category,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-control-label']) }}
            {{ Form::number('amount', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-control-label']) }}
            {{ Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('project_id', __('Project'),['class'=>'form-control-label']) }}
            {{ Form::select('project_id', $projects,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('user_id', __('User'),['class'=>'form-control-label']) }}
            {{ Form::select('user_id', $users,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('attachment', __('Attachment'),['class'=>'form-control-label']) }}
            <div class="choose-file form-group">
                <label for="attachment" class="form-control-label">
                    <div>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="attachment" id="attachment" data-filename="attachment_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                </label>
                <p class="attachment_update"></p>
            </div>
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
