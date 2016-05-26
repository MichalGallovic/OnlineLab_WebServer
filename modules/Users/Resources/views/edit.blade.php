@extends('user.layouts.default')

@section('content')
    {!! Form::model($user,['method' => 'PATCH','route'=>['users.update',$user->id], 'class' => 'form-horizontal']) !!}
    <div class="form-group">
        {!! Form::label('Id', trans("users::default.USR_ID").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('name', trans("users::default.USR_NAME").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('name',null,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('surname', trans("users::default.USR_SURNAME").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('surname',null,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('language_code', trans("users::default.USR_LANGUAGE").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select('language_code', array('sk' => 'Slovenský', 'en' => 'English'), null,  ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('role', trans("users::default.USR_ROLE").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select('role', ['user' => trans("users::default.USR_USER") , 'admin' => trans("users::default.USR_ADMIN")], null,  ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <a href="{{ url('users')}}" class="btn btn-default">{{trans("users::default.USR_BACK")}}</a>
            {!! Form::submit(trans("users::default.USR_UPDATE"), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop