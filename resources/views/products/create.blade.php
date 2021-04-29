{{ Form::open(array('url' => 'products')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('name', __('Product Name')) }}
        {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        @error('name')
        <span class="invalid-name" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
    </span>
        @enderror
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('price', __('Product Price')) }}
        {{ Form::text('price', '', array('class' => 'form-control','required'=>'required')) }}
        @error('price')
        <span class="invalid-price" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
    </span>
        @enderror
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('unit', __('Product Unit')) }}
        {!! Form::select('unit', $productunits, null,array('class' => 'form-control ','required'=>'required')) !!}
        @error('unit')
        <span class="invalid-unit" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
        @error('description')
        <span class="invalid-description" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
    </span>
        @enderror
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Create'),array('class'=>'btn green'))}}
</div>

{{ Form::close() }}
