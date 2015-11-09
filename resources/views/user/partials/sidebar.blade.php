
<div class="left_navig_title {MENU_CLASSIC_SHOW}" >{!! trans('default.OPTIONS') !!}</div>

<ul class="menu">
    <li>
        <a class="{{ Active::pattern('dashboard*',"selected") }}" href="{{ route('user::dashboard.home') }}">{!! trans('default.DASHBOARD') !!}</a>
    </li>
    @if(Active::pattern('dashboard*'))
        <ul class="level2">
            <li >
                <a href="{{ route('user::dashboard.home') }}" class="{{ Active::route('user::dashboard.home',"selected") }}" title="{!! trans('default.DASHBOARD_HOME') !!}">{!! trans('default.DASHBOARD_HOME') !!}</a>
            </li>
            <li >
                <a href="{{ route('user::dashboard.settings') }}" class="{{ Active::route('user::dashboard.settings',"selected") }}" title="{!! trans('default.DASHBOARD_LAYOUT') !!}">{!! trans('default.DASHBOARD_LAYOUT') !!}</a>
            </li>
        </ul>
    @endif
    @foreach(Module::getOrdered() as $module)
        @if($module->isVisible())
        <li ><a href="{{ $module->mainRoute() }}" style="{SECT_BORDER_BOTTOM}" class="{{ $module->isActive() }}" title="{{ $module->localizedName() }}">{{ $module->localizedName() }}</a></li>
        @endif
    @endforeach
</ul>

<ul class="collpase-menu nodisplay">
    @foreach(Module::getOrdered() as $module)
        @if($module->isVisible())
        <li ><div><a href="{{ $module->mainRoute() }}" title="{SECT_TITLE}" class="{SEC_NODUL} {SECT_SELECETED}"><img src="{{ asset($module->iconPath()) }}" width="24" style="" /></a></div></li>
        @endif
    @endforeach
</ul>

<a href="#" class="collapse" title="{!! trans('default.MENU_COLLAPSE') !!}" style="display:block;">{!! trans('default.MENU_COLLAPSE') !!}</a>
<a href="#" class="uncollapse nodisplay" title="Vysunúť menu"></a>


<div id="console"></div>
<div id="contsole2"></div>