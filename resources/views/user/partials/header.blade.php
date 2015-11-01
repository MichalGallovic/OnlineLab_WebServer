<div id="dashboard_header">
    <div class="left">
        <h1 class="dashboard {SECTION_TITLE_CLASS}">{SECTION_TITLE}</h1>
    </div>
    <div class="right">
        <div id="languages">
            {!! Widget::get('languagePicker') !!}
        </div>
        <div id="dahsboard_user_info">
            <div class="left">
                <span>{{ trans('default.LOGGED_AS') }}</span>
                <span class="logged_user"> <a href="#" title="Profil užívateľa">{{ Auth::user()->name }}</a></span>
            </div>
            <div class="right">
                <a class="logout" href="{{ url('auth/logout') }}" title="{{ trans('auth.logout') }}"></a>
            </div>
        </div>
    </div>
    <div class="breaker"></div>
</div>