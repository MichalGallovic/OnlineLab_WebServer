@extends('user.layouts.default')

@section('content')
    <h1>New thread</h1>

    {!! Form::open(array('method' => 'post', 'route' => ['forum.store.thread', $category_id])) !!}
    <div class="form-group">
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', '', array('class' => 'form-control', 'id' => 'title')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('body', 'Body:') !!}
        {!! Form::textarea('body', '', array('class' => 'form-control', 'id' => 'body')) !!}
    </div>

    {!! Form::token() !!}
    {!! Form::submit('Save thread', array('class' => 'btn btn-primary')) !!}
    {!! Form::close() !!}
@stop