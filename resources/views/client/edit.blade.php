<div class="card bg-none card-box">
    {{Form::model($client,array('route' => array('clients.update', $client->id), 'method' => 'PUT')) }}
    <div class="row">
        <div class="col-md-6 form-group">
            {{Form::label('name',__('Name'),['class'=>'form-control-label']) }}
            {{Form::text('name',null,array('class'=>'form-control ','placeholder'=>__('Enter Client Name')))}}
        </div>
        <div class="col-md-6 form-group">
            {{Form::label('email',__('Email'),['class'=>'form-control-label']) }}
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Client Email')))}}
        </div>
        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{Form::close()}}
</div>
