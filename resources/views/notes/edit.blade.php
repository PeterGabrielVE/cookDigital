<div class="card bg-none card-box">
    {{Form::model($note, array('route' => array('notes.update', $note->id), 'method' => 'PUT')) }}
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('title',__('Title'),['class'=>'form-control-label'])}}
            {{Form::text('title',null,array('class'=>'form-control ','required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('text', __('Description'),['class'=>'form-control-label']) }}
            {!! Form::textarea('text', null, ['class'=>'form-control','rows'=>'4','required'=>'required']) !!}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Color'),['class'=>'form-control-label']) }}
            <div class="bg-color-label">
                @foreach($colors as $k=>$color)
                    <div class="custom-control custom-radio  mb-3 {{$color}}">
                        <input class="custom-control-input" name="color" id="customCheck_{{$k}}" type="radio" value="{{$color}}" @if($note->color == $color) checked @endif >
                        <label class="custom-control-label " for="customCheck_{{$k}}"></label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
