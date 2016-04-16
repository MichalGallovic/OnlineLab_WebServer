<div id="dashboard_header">
    <div class="left">
        <h1 class="dashboard">
            <img class="heading_icon" src="{{ asset($module->iconPath()) }}"/>
            @section('heading')
            <span>{!! $module->localizedName() !!}</span>
            @show
        </h1>
    </div>
    <div class="right">
        <div id="notifications" class="left"  style="padding-top: 10px; margin-right: 20px;cursor: pointer;position: relative;" data-toggle="popover" data-placement="bottom">
            <span class="glyphicon glyphicon-inbox" style="font-size: 2em; color: cornflowerblue"></span>
            <span id="notifications_count" class="label label-danger label-as-badge" style="position: absolute; top:25px; left:15px;"></span>
        </div>
        <div id="languages">
            {!! Widget::get('languagePicker') !!}
        </div>
        <div id="dahsboard_user_info">
            <div class="left">
                <span>{{ trans('default.LOGGED_AS') }}</span>
                <span class="logged_user"> <a href="#" title="Profil užívateľa">{{ Auth::user()->user->name }}</a></span>
            </div>
            <div class="right">
                <a class="logout" href="{{ url('auth/logout') }}" title="{!! trans('auth.logout') !!}"></a>
            </div>
        </div>
    </div>
    <div class="breaker"></div>
</div>