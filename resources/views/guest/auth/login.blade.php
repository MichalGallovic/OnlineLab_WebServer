@extends('guest.layouts.default')

@section('content')
        <!-- LOGIN FORM -->
<div id="login">
    <h1><a href="{!! trans('default.ROOT_PATH') !!}" title="ONLINE LABORATORY MANAGER">ONLINE LABORATORY MANAGER</a></h1>

    <!-- BEGIN DYNAMIC BLOCK: forgotpass_email_succes -->
    <div class="login_info">
        {!! trans('default.NEW_PASS_INFO_SUCCESS_TEXT') !!}
    </div>
    <!-- END DYNAMIC BLOCK: forgotpass_email_succes -->

    {!! trans('default.FORM') !!}


    <!-- BEGIN DYNAMIC BLOCK: forgotpass_block -->
    <div class="login_info">{!! trans('default.NEW_PASS_INFO_TEXT') !!}</div>

    <!-- BEGIN DYNAMIC BLOCK: forgotpass_email_empty -->
    <div class="login_error">
        {!! trans('default.FOROGT_EMAIL_MESSAGE') !!}
    </div>
    <!-- END DYNAMIC BLOCK: forgotpass_email_empty -->

    <!-- BEGIN DYNAMIC BLOCK: email_no_exist -->
    <div class="login_error">
        {!! trans('default.EMAIL_NOEXIST_MESSAGE') !!}
    </div>
    <!-- END DYNAMIC BLOCK: email_no_exist -->

    <form action="" method="post" enctype="multipart/form-data">
        <p>
            <label>
                Email
                <br />
                <input class="input" type="text" name="email" />
            </label>
        </p>
        <p style="margin-top:10px;" >
            <input type="submit" name="submit" id="" value="{!! trans('default.GET_NEW_PASSWORD_BTN') !!}">
            <input type="hidden" name="new-password-atempt" value="1">
            <a style="float:right;margin-top:5px;" href="{!! trans('default.ROOT_PATH') !!}" title="{!! trans('default.BACK_LOGIN_LINK') !!}">{!! trans('default.BACK_LOGIN_LINK') !!}</a>
        </p>
    </form>
    <!-- END DYNAMIC BLOCK: forgotpass_block -->

</div>
@endsection