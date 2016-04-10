@extends('user.layouts.default')

@section('content')
    <form class="form-horizontal">
        <div class="form-group">
            <label for="user_id" class="col-sm-2 control-label">Id:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="user_id" placeholder="{{$user->id}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">Email:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="email" placeholder="{{$user->email}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Full name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" placeholder="{{$user->name}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="lang" class="col-sm-2 control-label">Language</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="lang" placeholder="{{$user->language_code}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="role" class="col-sm-2 control-label">Role</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="role" placeholder="{{$user->role}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="date" class="col-sm-2 control-label">Registration date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="date" placeholder="{{$user->created_at}}" readonly>
            </div>
        </div>
        <div class="form-group">
            <label for="mod" class="col-sm-2 control-label">Last edited</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="mod" placeholder="{{$user->updated_at}}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="mod" class="col-sm-2 control-label">Last login</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="mod" placeholder="{{$user->getLastLoginTime()}}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="thread" class="col-sm-2 control-label">Number of threads</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="thread" placeholder="{{count($user->threads)}}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="comment" class="col-sm-2 control-label">Number of comments</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="comment" placeholder="{{count($user->comments)}}" readonly>
            </div>
        </div>

        @if($user->hasAccount('facebook'))
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    <h4>Registered with Facebook</h4>
                </div>
            </div>
        @endif

        @if($user->hasAccount('google'))
            <div class="form-group">

                <div class="col-sm-3 col-sm-offset-2">
                    <h4>Registered with Google</h4>
                </div>
            </div>
        @endif

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a href="{{ url('users')}}" class="btn btn-default">Back</a>
            </div>
        </div>
    </form>
@stop