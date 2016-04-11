@extends('user.layouts.default')

@section('content')

    <div class="clearfix">
        <ol class="breadcrumb pull-left">
            <li><a href="{!! route('forum.index') !!}">Forums</a></li>
            <li class="active">{{$category->title}}</li>
        </ol>
    </div>


    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="clearfix">
                <h3 class="panel-title pull-left">{!! $category->title !!}</h3>
                {!! Form::open(['method' => 'DELETE', 'route'=>['forum.delete.category', $category->id]]) !!}
                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs pull-right']) !!}
                {!! Form::close() !!}
                <a href="{!! URL::route('forum.new.thread', $category->id) !!}" class="btn btn-success btn-xs pull-right">New Thread</a>
            </div>
        </div>
        <div class="panel-body breaker">
            <div class="list-group breaker">
                @foreach($category->threads as $thread)
                    <a href="{!! route('forum.thread', $thread->id) !!}" class="list-group-item">{!! $thread->title !!}</a>
                @endforeach
            </div>
        </div>
    </div>


    @if(Auth::check() && Auth::user()->user->isAdmin())
        <div class="modal fade" id="group_form" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"&times;></span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">New Group</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'forum.store.group')) !!}
                        <div class="form-group {!! ($errors->has('group_name')) ? 'has-error' : '' !!}">
                            <label for="group_name">Group Name:</label>
                            <input type="text" id="group_name" name="group_name" class="form-control">
                            @if($errors->has('group_name'))
                                <p>{!! $errors->first('group_name') !!}</p>
                            @endif
                        </div>
                        {!! Form::token() !!}
                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="form_submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop

@section('page_js')
    @parent
@stop