
<div class="left_navig_title {MENU_CLASSIC_SHOW}" >{!! trans('default.OPTIONS') !!}</div>

<ul class="menu {MENU_CLASSIC_SHOW}">
    <!-- BEGIN DYNAMIC BLOCK: section_row -->
    @foreach(Module::getOrdered() as $module)
        <li ><a href="{{ $module->mainRoute() }}" style="{SECT_BORDER_BOTTOM}" class="{SECT_SELECETED}" title="{SECT_TITLE}">{{ $module->name }}</a></li>
    @endforeach

    <!-- BEGIN DYNAMIC BLOCK: sub_section_block -->
    <ul class="level2">
        <!-- BEGIN DYNAMIC BLOCK: sub_section_row -->
        <li >
            <a href="{ROOT_PATH}dashboard.php?section_id={SUB_SECT_ID}" class="{SUB_SECT_SELECETED}" title="{SUB_SECT_TITLE}">{SUB_SECT_TITLE}</a>
        </li>
        <!-- END DYNAMIC BLOCK: sub_section_row -->
        <!--<li ><a href="{ROOT_PATH}{SUB_SECT_ID}/{SUB_SECT_ACCES_KEY}/" class="{SUB_SECT_SELECETED}" title="{SUB_SECT_TITLE}">Defaultne zobrazenie plochy</a></li>-->

    </ul>
    <!-- END DYNAMIC BLOCK: sub_section_block -->
    <!-- END DYNAMIC BLOCK: section_row -->
</ul>

<ul class="collpase-menu {MENU_COLLPASE_SHOW}">

    <!-- BEGIN DYNAMIC BLOCK: section_row2 -->
    @foreach(Module::getOrdered() as $module)
        <li ><div><a href="{{ $module->mainRoute() }}" title="{SECT_TITLE}" class="{SEC_NODUL} {SECT_SELECETED}"><img src="{{ asset($module->iconPath()) }}" width="24" style="" /></a></div></li>
    @endforeach

    <!-- END DYNAMIC BLOCK: section_row2 -->

</ul>

<a href="#" class="collapse {MENU_CLASSIC_SHOW}" title="{!! trans('default.MENU_COLLAPSE') !!}">{!! trans('default.MENU_COLLAPSE') !!}</a>
<a href="#" class="uncollapse {MENU_COLLPASE_SHOW}" title="Vysunúť menu"></a>




<div id="console"></div>
<div id="console2"></div>