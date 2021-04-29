<div class="card bg-none card-box">
    {{ Form::model($invoice, array('route' => array('invoices.products.store', $invoice->id), 'method' => 'POST')) }}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <input type="text" class="form-control " value="{{(!empty($invoice->project)?$invoice->project->name:'')}}" readonly>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="customRadio5" name="type" value="milestone" checked="checked" onclick="hide_show(this)">
                    <label class="custom-control-label text-dark" for="customRadio5">{{__('Milestone & Task')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="customRadio6" name="type" value="other" onclick="hide_show(this)">
                    <label class="custom-control-label text-dark" for="customRadio6">{{__('Other')}}</label>
                </div>
            </div>
        </div>
    </div>
    <div id="milestone">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="milestone_id" class="form-control-label">{{__('Milestone')}}</label>
                    <select class="form-control select2" onchange="getTask(this,{{$invoice->project_id}})" id="milestone_id" name="milestone_id">
                        <option value="" selected="selected">{{__('Select Milestone')}}</option>
                        @foreach($milestones as  $milestone)
                            <option value="{{$milestone->id}}">{{$milestone->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="task_id" class="form-control-label">{{__('Task')}}</label>
                    <select class="form-control select2" id="task_id" name="task_id">
                        <option value="" selected="selected">{{__('Select Task')}}</option>
                        @foreach($tasks as  $task)
                            <option value="{{$task->id}}">{{$task->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div id="other" style="display: none">
        <div id="milestone">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title" class="form-control-label">{{__('Title')}}</label>
                        <input type="text" class="form-control " name="title">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="price" class="form-control-label">{{__('Price')}}</label>
                <input type="number" class="form-control " name="price" step="0.01" required>
            </div>
        </div>
        <div class="col-12 text-right">
            @if(isset($invoice))
                <input type="submit" value="{{__('Add')}}" class="btn-create badge-blue">
            @else
                <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            @endif
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>

        {{ Form::close() }}
    </div>
</div>
