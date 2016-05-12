@extends('user.layouts.default')

@section('content')
    <div class="clearfix">
        <ol class="breadcrumb pull-left">
            <li><a href="{!! route('forum.index') !!}">Forums</a></li>
            <li><a href="{!! route('forum.category', $thread->category->id) !!}">{!! $thread->category->title !!}</a></li>
            <li class="active">{{$thread->title}}</li>
        </ol>
        @if(Auth::check() && Auth::user()->user->isAdmin())
        {!! Form::open(['method' => 'DELETE', 'route'=>['forum.delete.thread', $thread->id]]) !!}
        {!! Form::submit('Delete', ['class' => 'btn btn-danger pull-right']) !!}
        {!! Form::close() !!}
        @endif
    </div>

    <div class="well">
        <h1>{!! $thread->title !!}</h1>
        <h4>By: {!! $thread->user->name !!} on {!! $thread->created_at !!}</h4>
        <hr>
        <p>{!! nl2br($thread->body) !!}</p>
    </div>

    @foreach($comments as $comment)
        <div class="well">
            <h4>By: {!! $comment->user->getFullName() !!} on {!! $comment->created_at !!}</h4>
            <p>{!! nl2br($comment->body) !!}</p>
            @if(Auth::check() && Auth::user()->user->isAdmin())
                {!! Form::open(['method' => 'DELETE', 'route'=>['forum.delete.comment', $comment->id]]) !!}
                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            @endif
        </div>
    @endforeach

    {!! $comments->render() !!}

    {!! Form::open(array('method' => 'post', 'route' => ['forum.store.comment', $thread->id])) !!}
    <div class="form-group">
        {!! Form::label('body', 'Body:') !!}
        {!! Form::textarea('body', '', array('class' => 'form-control', 'id' => 'body')) !!}
    </div>

    {!! Form::token() !!}
    {!! Form::submit('Save comment', array('class' => 'btn btn-primary')) !!}
    {!! Form::close() !!}

@stop