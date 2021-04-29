<div class="card bg-none card-box">
    {{ Form::model($timeSheet, array('route' => array('task.timesheet.update', $timeSheet->id), 'method' => 'POST')) }}
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('project_id', __('Project'),['class'=>'form-control-label']) }}
            {!! Form::select('project_id', $projects, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('task_id', __('Task'),['class'=>'form-control-label']) }}
            <select name="task_id" id="task_id" class="form-control select2" required>
                <option value="">{{__('Select Task')}}</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Task Date'),['class'=>'form-control-label']) }}
            {{ Form::text('date', null, array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('hours', __('Task Hours'),['class'=>'form-control-label']) }}
            {{ Form::number('hours', null, array('class' => 'form-control','required'=>'required','min'=>0)) }}
        </div>
    </div>
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('remark', __('Remark'),['class'=>'form-control-label']) }}
            {!! Form::textarea('remark', null, ['class'=>'form-control','rows'=>'2']) !!}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>

<script>
    var task_id = '{{$timeSheet->task_id}}';
    $(document).ready(function () {
        $("#commonModal select[name=project_id]").trigger('change');
    });

    $(document).on("change", "#commonModal select[name=project_id]", function () {
        $.ajax({
            url: '{{route('timesheet.project.task')}}',
            data: {project_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $("#task_id").empty();
                $("#task_id").html('<option value="" selected="selected">{{__('Select Task')}}</option>');
                $.each(data, function (key, data) {
                    var select = '';
                    if (key == '{{ $timeSheet->task_id }}') {
                        select = 'selected';
                    }
                    $("#task_id").append('<option value="' + key + '" ' + select + '>' + data + '</option>');
                });

                $("#task_id").select2();
                $("#task_id").val(task_id);
            }
        })
    });
</script>
