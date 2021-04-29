<div class="card bg-none card-box">
    {{Form::open(array('url'=>'users','method'=>'post'))}}
    <div class="row">
        <div class="col-md-6 form-group">
            {{Form::label('name',__('Name'),['class'=>'form-control-label']) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
        </div>
        <div class="col-md-6 form-group">
            {{Form::label('email',__('Email'),['class'=>'form-control-label'])}}
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required'=>'required'))}}
        </div>
        <div class="col-md-6 form-group">
            {{Form::label('password',__('Password'),['class'=>'form-control-label'])}}
            {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter User Password'),'required'=>'required','minlength'=>"6"))}}
        </div>
        @if(\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('User Role'),['class'=>'form-control-label']) }}
                {!! Form::select('role', $roles, null,array('class' => 'form-control select2','required'=>'required')) !!}
            </div>
        @endif
        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{Form::close()}}
</div>
