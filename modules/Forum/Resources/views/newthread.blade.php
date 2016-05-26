@extends('user.layouts.default')

@section('content')
    <h1>{{trans("forum::default.FORUM_THREAD_NEW")}}</h1>

    {!! Form::open(array('method' => 'post', 'route' => ['forum.store.thread', $category_id])) !!}
    <div class="form-group">
        {!! Form::label('title', trans("forum::default.FORUM_THREAD_TITLE").':') !!}
        {!! Form::text('title', '', array('class' => 'form-control', 'id' => 'title')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('body', trans("forum::default.FORUM_THREAD_BODY").':') !!}
        {!! Form::textarea('body', '', array('class' => 'form-control', 'id' => 'body')) !!}
    </div>

    {!! Form::token() !!}
    {!! Form::submit(trans("forum::default.FORUM_THREAD_SAVE"), array('class' => 'btn btn-primary')) !!}
    {!! Form::close() !!}
@stop