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
            {!! Form::label('Id', 'Id:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('email', 'E-mail:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::email('email',$user->getEmail('local'),['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('name', 'Name:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('name',null,['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('surname', 'Surname:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::text('surname',null,['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('language_code', 'Language:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::select('language_code', array('sk' => 'Slovenský', 'en' => 'English'), null,  ['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('password', 'Password:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::password('password',['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('password_confirmation', 'Confirm password:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-11">
                {!! Form::password('password_confirmation',['class'=>'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            <h4 class="col-sm-offset-1 col-sm-6">I want to recieve notifications on theese e-mail addresses:</h4>
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

                {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}

        <hr>
    <div class="col-md-10 col-md-offset-2">
        @if ($user->hasAccount('ldap'))
            <h4>AIS STUBA account</h4>
            <br>
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('ismail', 'e-mail:', ['class' => 'col-md-1 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::email('ismail',$user->getEmail('ldap'),['class'=>'form-control', 'readonly']) !!}
                    </div>
                </div>
            </div>
            <hr>
        @else
            <div class="form-group">
                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#ldap_form">Connect account with IS STU</a>
            </div>

            <div class="modal fade" id="ldap_form" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true"&times;></span>
                                <span class="sr-only">Close</span>
                            </button>
                            <h4 class="modal-title">Connect profile with IS STU</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'profile.settings.ldap')) !!}
                            <div class="form-group {!! ($errors->has('ldap_login')) ? 'has-error' : '' !!}">
                                <label for="ldap_login">Login:</label>
                                <input type="text" id="ldap_login" name="ldap_login" class="form-control">
                                @if($errors->has('ldap_login'))
                                    <p>{!! $errors->first('ldap_login') !!}</p>
                                @endif
                            </div>
                            <div class="form-group {!! ($errors->has('ldap_password')) ? 'has-error' : '' !!}">
                                <label for="ldap_password">Password:</label>
                                <input type="password" id="ldap_password" name="ldap_password" class="form-control">
                                @if($errors->has('ldap_password'))
                                    <p>{!! $errors->first('ldap_password') !!}</p>
                                @endif
                            </div>
                            {!! Form::token() !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            {!! Form::submit('Save', array('form' => 'target_form', 'class' => 'btn btn-primary')) !!}
                                    <!--
						<button type="button" class="btn btn-primary" data-dismiss="modal" id="form_submit">Save</button>-->
                        </div>
                    </div>
                </div>
            </div>

        @endif
        @if (!$user->hasAccount('facebook'))
            <div class="form-group">
                <a class="btn btn-social btn-facebook" href="provider/facebook" role="button"><span class="fa fa-facebook"></span>Connect account with Facebook</a>
            </div>
        @else
            <h4>Facebook account</h4>
            <br>
            {!! Form::open(['method' => 'DELETE', 'route'=>['profile.destroy', $user->getAccount('facebook')->id], 'class' => 'form-horizontal']) !!}
            <div class="form-group">
                {!! Form::label('fbemail', 'e-mail:', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::email('fbemail',$user->getEmail('facebook'),['class'=>'form-control', 'readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                    {!! Form::submit('Delete account', ['class' => 'btn btn-danger']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            <hr>
        @endif
        @if (!$user->hasAccount('google'))
            <div class="form-group">
                <a class="btn btn-social btn-google" href="provider/google" role="button"><span class="fa fa-google"></span>Connect account with Google</a>
            </div>
        @else
            <h4>Google account</h4>
            <br>
            {!! Form::open(['method' => 'DELETE', 'route'=>['profile.destroy', $user->getAccount('google')->id], 'class' => 'form-horizontal']) !!}
            <div class="form-group">

                {!! Form::label('gmail', 'e-mail:', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-10">
                    {!! Form::email('gmail',$user->getEmail('google'),['class'=>'form-control', 'readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10">
                    {!! Form::submit('Delete account', ['class' => 'btn btn-danger']) !!}
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
