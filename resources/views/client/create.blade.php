<div class="card bg-none card-box">
    {{Form::open(array('url'=>'clients','method'=>'post'))}}
    <div class="row">
        <div class="col-md-6 form-group">
            {{Form::label('name',__('Name'),['class'=>'form-control-label']) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Client Name'),'required'=>'required'))}}
        </div>
        <div class="col-md-6 form-group">
            {{Form::label('email',__('Email'),['class'=>'form-control-label'])}}
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Client Email'),'required'=>'required'))}}
        </div>
        <div class="col-md-12 form-group">
            {{Form::label('password',__('Password'),['class'=>'form-control-label'])}}
            {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Client Password'),'minlength'=>"6",'required'=>'required'))}}
        </div>
        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{Form::close()}}
</div>
