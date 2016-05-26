@extends('user.layouts.default')

@section('content')
    {!! Form::model($user,['method' => 'PATCH','route'=>'profile.update', 'class' => 'form-horizontal', 'files' => true]) !!}
    <div class="col-md-2">
        <div class="profile-picture-frame" style=" padding: 15px; min-height: 200px">

                {!! Html::image('images/profile/' . $user->id, 'Generic placeholder image', ['class' => 'center-block']) !!}

        </div>
        <div class="form-group">
            <div class="col-sm-12">
                {!! Form::file('avatar') !!}
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="form-group">
            {!! Form::label('id', trans("profile::default.PROFILE_ID").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('email', trans("profile::default.PROFILE_EMAIL").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::email('email',$user->getEmail('local'),['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('name', trans("profile::default.PROFILE_NAME").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('name',null,['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('surname', trans("profile::default.PROFILE_SURNAME").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('surname',null,['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('language_code', trans("profile::default.PROFILE_LANGUAGE").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::select('language_code', array('sk' => 'Slovenský', 'en' => 'English'), null,  ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('password', trans("profile::default.PROFILE_PASSWORD").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::password('password',['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('password_confirmation', trans("profile::default.PROFILE_PASSWORD_CONFIRM").':', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::password('password_confirmation',['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            <h4 class="col-sm-offset-1 col-sm-6">{{trans("profile::default.PROFILE_NOTIFICATIONS")}}:</h4>
            @foreach($user->accounts as $account)
                <div class="checkbox col-sm-offset-1 col-sm-6">
                    <label>
                        {!! Form::checkbox('account[]', $account->id, $account->notify, ['id' => 'checkbox-'.$account->id]) !!}
                        {{$account->email}}
                    </label>
                </div>

            @endforeach
        </div>


        <div class="form-group">
            <div class="col-sm-10">

                {!! Form::submit(trans("profile::default.PROFILE_UPDATE"), ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}

        <hr>
    <div class="col-md-10 col-md-offset-2">
        @if ($user->hasAccount('ldap'))
            <h4>{{trans("profile::default.PROFILE_AIS")}}</h4>
            <br>
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('ismail', trans("profile::default.PROFILE_EMAIL").':', ['class' => 'col-md-1 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::email('ismail',$user->getEmail('ldap'),['class'=>'form-control', 'readonly']) !!}
                    </div>
                </div>
            </div>
            <hr>
        @else
            <div class="form-group">
                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#ldap_form">{{trans("profile::default.PROFILE_AIS_CONNECT")}}</a>
            </div>

            <div class="modal fade" id="ldap_form" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true"&times;></span>
                                <span class="sr-only">Close</span>
                            </button>
                            <h4 class="modal-title">{{trans("profile::default.PROFILE_AIS_CONNECT")}}</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'profile.settings.ldap')) !!}
                            <div class="form-group {!! ($errors->has('ldap_login')) ? 'has-error' : '' !!}">
                                <label for="ldap_login">{{trans("profile::default.PROFILE_LOGIN")}}:</label>
                                <input type="text" id="ldap_login" name="ldap_login" class="form-control">
                                @if($errors->has('ldap_login'))
                                    <p>{!! $errors->first('ldap_login') !!}</p>
                                @endif
                            </div>
                            <div class="form-group {!! ($errors->has('ldap_password')) ? 'has-error' : '' !!}">
                                <label for="ldap_password">{{trans("profile::default.PROFILE_PASSWORD")}}:</label>
                                <input type="password" id="ldap_password" name="ldap_password" class="form-control">
                                @if($errors->has('ldap_password'))
                                    <p>{!! $errors->first('ldap_password') !!}</p>
                                @endif
                            </div>
                            {!! Form::token() !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{trans("profile::default.PROFILE_CLOSE")}}</button>
                            {!! Form::submit(trans("profile::default.PROFILE_SAVE"), array('form' => 'target_form', 'class' => 'btn btn-primary')) !!}
                                    <!--
						<button type="button" class="btn btn-primary" data-dismiss="modal" id="form_submit">Save</button>-->
                        </div>
                    </div>
                </div>
            </div>

        @endif
        @if (!$user->hasAccount('facebook'))
            <div class="form-group">
                <a class="btn btn-social btn-facebook" href="provider/facebook" role="button"><span class="fa fa-facebook"></span>{{trans("profile::default.PROFILE_FACEBOOK_CONNECT")}}</a>
            </div>
        @else
            <h4>{{trans("profile::default.PROFILE_FACEBOOK")}}</h4>
            <br>
            {!! Form::open(['method' => 'DELETE', 'route'=>['profile.destroy', $user->getAccount('facebook')->id], 'class' => 'form-horizontal']) !!}
            <div class="form-group">
                {!! Form::label('fbemail', trans("profile::default.PROFILE_EMAIL").':', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::email('fbemail',$user->getEmail('facebook'),['class'=>'form-control', 'readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                    {!! Form::submit(trans("profile::default.PROFILE_ACCOUNT_DELETE"), ['class' => 'btn btn-danger']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            <hr>
        @endif
        @if (!$user->hasAccount('google'))
            <div class="form-group">
                <a class="btn btn-social btn-google" href="provider/google" role="button"><span class="fa fa-google"></span>{{trans("profile::default.PROFILE_GOOGLE_CONNECT")}}</a>
            </div>
        @else
            <h4>{{trans("profile::default.PROFILE_GOOGLE")}}</h4>
            <br>
            {!! Form::open(['method' => 'DELETE', 'route'=>['profile.destroy', $user->getAccount('google')->id], 'class' => 'form-horizontal']) !!}
            <div class="form-group">

                {!! Form::label('gmail', trans("profile::default.PROFILE_EMAIL").':', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::email('gmail',$user->getEmail('google'),['class'=>'form-control', 'readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                    {!! Form::submit(trans("profile::default.PROFILE_ACCOUNT_DELETE"), ['class' => 'btn btn-danger']) !!}
                </div>
            </div>

            {!! Form::close() !!}
            <hr>
        @endif
    </div>

@endsection

@section('page_js')
    @parent

        <script type="text/javascript">
            @if(Session::has('modal'))
                $("{!! Session::get('modal') !!}").modal('show');
            @endif
                $(document).ready(function(){


                });
        </script>

@stop
