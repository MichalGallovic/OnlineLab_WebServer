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
        <div class="media">
            <div class="media-left text-center">
                {!! Html::image('images/thumb/' . $thread->user->id, 'Generic placeholder image', ['class' => 'media-object, img-rounded','style' => 'max-width: 100px', 'style' => 'margin-bottom:10px;']) !!}
                <strong class="text-success" >{!! $thread->user->getFullName() !!}</strong>
            </div>
            <div class="media-body">
                <span class="text-muted pull-right">{{$thread->created_at}}</span>

                <h3 class="media-heading">{!! $thread->title !!}</h3>

                <p>{!! nl2br($thread->body) !!}</p>
            </div>
        </div>
    </div>

    <hr>
    <ul class="media-list">
    @foreach($comments as $comment)

        <div class="well">
            <li class="media">
                <div class="media-left">
                    {!! Html::image('images/thumb/' . $comment->user->id, 'Generic placeholder image', ['class' => 'media-object, img-circle','style' => 'max-width: 50px']) !!}
                </div>
                <div class="media-body">
                    <span class="text-muted pull-right">{!! $comment->created_at !!}</span>
                    <strong class="text-success" >{!! $comment->user->getFullName() !!}</strong>
                    <p style="margin-top: 10px">{!! nl2br($comment->body) !!}</p>
                    @if(Auth::check() && Auth::user()->user->isAdmin() || Auth::user()->user->id==$comment->user->id)
                        {!! Form::open(['method' => 'DELETE', 'route'=>['forum.delete.comment', $comment->id], 'class' => 'pull-right']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endif
                </div>

            </li>

        </div>
    @endforeach
    </ul>

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