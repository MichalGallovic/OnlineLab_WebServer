
<div class="left_navig_title {MENU_CLASSIC_SHOW}" >{!! trans('default.OPTIONS') !!}</div>

<ul class="menu">
    <li>
        <a href="{{ route('user::dashboard') }}">{!! trans('default.DASHBOARD') !!}</a>
    </li>
    @foreach(Module::getOrdered() as $module)
        @if($module->isVisible() && (!$module->admin || $module->admin && Auth::user()->user->isAdmin()))
        <li ><a href="{{ $module->mainRoute() }}" style="{SECT_BORDER_BOTTOM}" class="{{ $module->isActive() }}" title="{{ $module->localizedName() }}">{{ $module->localizedName() }}</a></li>
        @endif
    @endforeach
</ul>

{{-- @TODO <ul class="collpase-menu {MENU_COLLPASE_SHOW}">--}}
<ul class="collpase-menu nodisplay">
    <li ><div><a href="/dashboard" ><img src="/modules/dashboard/images/icon/default.png" width="24"/></a></div></li>
    @foreach(Module::getOrdered() as $module)
        @if($module->isVisible())
        <li ><div><a href="{{ $module->mainRoute() }}"><img src="{{ asset($module->iconPath()) }}" width="24" style="" /></a></div></li>
        @endif
    @endforeach
</ul>

<a href="#" class="collapse" title="{!! trans('default.MENU_COLLAPSE') !!}" style="display:block;">{!! trans('default.MENU_COLLAPSE') !!}</a>
<a href="#" class="uncollapse nodisplay" title="Vysunúť menu"></a>


<div id="console"></div>
<div id="contsole2"></div>