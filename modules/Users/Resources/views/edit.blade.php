@extends('user.layouts.default')

@section('content')
    {!! Form::model($user,['method' => 'PATCH','route'=>['users.update',$user->id], 'class' => 'form-horizontal']) !!}
    <div class="form-group">
        {!! Form::label('Id', 'Id:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('email', 'E-mail:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('email',null,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('name', 'Meno:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('name',null,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('surname', 'Priezvisko:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('surname',null,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('language_code', 'Jazyk:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select('language_code', array('sk' => 'Slovenský', 'en' => 'English'), null,  ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('role', 'Rola:', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select('role', ['user' => 'Užívateľ' , 'admin' => 'Administrátor'], null,  ['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <a href="{{ url('users')}}" class="btn btn-default">Back</a>
            {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop