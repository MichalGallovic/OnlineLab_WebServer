<link href="{!! trans('ROOT_PATH ') !!}includes/modules/users/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/users/js/default.js"></script>

<!--  Pager-->
<div class="ok_warning"></div>

<div id="pager_holder"></div>

<!-- Users listing -->
<div id="users">

    <div id="users_list">
        
        <table class="users" cellspacing="0">
            <thead>
                <tr>
                    <th class="first">Id.</th>
                    <th class="user_login">{!! trans('USR_LOGIN ') !!}</th>
                    <th class="user_name">{!! trans('USR_NAME ') !!}</th>
                    <th class="user_mail">{!! trans('USR_MAIL ') !!}</th>
                    <th class="user_role">{!! trans('USR_ROLE ') !!}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot>
                <tr>
                    <th class="first">Id.</th>
                    <th>{!! trans('USR_LOGIN ') !!}</th>
                    <th>{!! trans('USR_NAME ') !!}</th>
                    <th>{!! trans('USR_MAIL ') !!}</th>
                    <th>{!! trans('USR_ROLE ') !!}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
</div>