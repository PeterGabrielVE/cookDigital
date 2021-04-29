<div class="card bg-none card-box">
    @if(isset($product))
        {{ Form::model($product, array('route' => array('estimations.products.update', $estimation->id,$product->id), 'method' => 'POST')) }}
    @else
        {{ Form::model($estimation, array('route' => array('estimations.products.store', $estimation->id), 'method' => 'POST')) }}
    @endif
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Please enter your service or product name'))) }}
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 form-group">
            {{ Form::label('price', __('Price'),['class'=>'form-control-label']) }}
            {{ Form::number('price', isset($product)?null:1, array('class' => 'form-control','required'=>'required','min'=>'1')) }}
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 form-group">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-control-label']) }}
            {{ Form::number('quantity', isset($product)?null:1, array('class' => 'form-control','required'=>'required','min'=>'1')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
            {{ Form::textarea('description', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-12 text-right">
            @if(isset($product))
                <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            @else
                <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            @endif
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
