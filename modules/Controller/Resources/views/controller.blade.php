<link href="{!! trans('ROOT_PATH ') !!}includes/modules/controller/css/default.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/controller/js/paginator.js"></script>
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/controller/js/default.js"></script>

<script type="text/javascript">
    var rows_per_page = {!! trans('ROWS_PER_PAGE ') !!};
    var numberOfPagesDisplay = {!! trans('NUMBER_OF_PAGE_DISPLAY ') !!};
</script>

<!--  Pager-->
<div class="ok_warning"></div>

<input type="hidden" name="page_count" id="page_count" />

<div id="pager_holder"></div>


<!-- Controllers listing -->
<div id="controllers">

    <div id="controller_list">
        <!-- Tlacitko na pridanie noveho -->
        <div class="buttons">
            <a id="create-new-ctrl" href="javascript:void();" title="" class="default-btn"><span>{!! trans('NEW_CONTROLLER_TITLE ') !!}</span></a>
        </div>

        <table class="controllers" cellspacing="0">
            <thead>
                <tr>
                    <th class="first">Id.</th>
                    <th class="reg">{!! trans('CTRL_NAME ') !!}</th>
                    <th class="equip">{!! trans('CTRL_EQUIPMENT ') !!}</th>
                    <th class="author">{!! trans('CTRL_AUTHOR ') !!}</th>
                    <th class="access">{!! trans('CTRL_ACCESSIBILITY ') !!}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>

            <tfoot>
                <tr>
                    <th class="first">Id.</th>
                    <th>{!! trans('CTRL_NAME ') !!}</th>
                    <th>{!! trans('CTRL_EQUIPMENT ') !!}</th>
                    <th>{!! trans('CTRL_AUTHOR ') !!}</th>
                    <th >{!! trans('CTRL_ACCESSIBILITY ') !!}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="reg-preview">
        <div class="buttons">
            <a id="back-to-ctrl" href="javascript:void();" title="{!! trans('BACK_TO_CONTROLLERS ') !!}" class="default-btn"><span>{!! trans('BACK_TO_CONTROLLERS ') !!}</span></a>
            <a id="change-cltr-settings" href="javascript:void();" title="{!! trans('CHANGE_CONTROLLER_SETTINGS ') !!}" class="default-btn"><span>{!! trans('CHANGE_CONTROLLER_SETTINGS ') !!}</span></a>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" value="" name="ctrl_id" id="ctrl_id_input">
        </form>

        <div id="ctrl_properties" class="default-box" >
            <div class="header"><span>Nastavenie regulátora</span></div>
            <div class="box-content" style="padding:20px;">
                <div class="column1">
                    <label>Id regulátora</label>
                    <div class="value"><span id="ctrl_id"></span></div>
                    <label>{!! trans('LABEL_NAME_REGULATOR ') !!}</label>
                    <div class="value"><span id="reg_name"></span></div>
                    <label>Vytvoril</label>
                    <div class="value"><span id="reg_author"></span></div>
                    <label>Dátum vytvorenia</label>
                    <div class="value"><span id="reg_date"></span></div>
                    <label>Systém</label>
                    <div class="value"><span id="reg_equipment_name"></span></div>
                    <label>Zdieľaný</label>
                    <div class="value"><span id="reg_permissions"></span></div>
                </div>
                <div class="column2">
                    <label>{!! trans('LABEL_BODY_REGULATOR ') !!}</label>
                    <div id="reg_body">
                        <textarea disabled="disabled"></textarea>
                        <div style="font-style:italic;font-size:11px;color:#21759B;">
                            y1 = f(u1)<br />
                            y1 = výstup z regulátora<br />
                            u1 = vstup do regulátora<br />
                        </div>
                    </div>
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
    </div>

    <div id="reg-settings">
        <div class="buttons">
            <a id="back-to-ctrl2" href="javascript:void();" title="" class="default-btn"><span>{!! trans('BACK_TO_CONTROLLERS ') !!}</span></a>
            <a id="back-to-selected-reg" href="javascript:void();" title="" class="default-btn"><span>{!! trans('PREVIEW_TITLE ') !!}</span></a>
        </div>
        
        <div class="default-box" id="reg-settings-box">
            <div class="header"><span>{!! trans('CHANGE_CONTROLLER_SETTINGS ') !!}</span></div>
            <div class="box-content" style="padding:20px;">
                <form id="reg-settings-form" action="" method="post" enctype="multipart/form-data" onsubmit="save_reg_settings(); return false;">
                    <input type="hidden" name="ctrl_id" value="" />
                    <input type="hidden" name="ctr_change_settings" value="1">
                    <div class="column1">
                        <label>Id regulátora</label>
                        <div class="value"><span id="settings_ctrl_id"></span></div>

                        <label>{!! trans('LABEL_NAME_REGULATOR ') !!}</label>
                        <div class="value">
                            <input type="text" value="" name="settings_reg_name" id="settings_reg_name">
                        </div>

                        <label>{!! trans('LABEL_BODY_REGULATOR ') !!}</label>
                        <div class="value">
                            <textarea name="settings_reg_body"  id="settings_reg_body"></textarea>
                            <div style="font-style:italic;font-size:11px;color:#21759B;">
                                y1 = f(u1)<br />
                                y1 = výstup z regulátora<br />
                                u1 = vstup do regulátora<br />
                            </div>
                        </div>

                        <label>{!! trans('LABEL_SYSTEM ') !!}</label>
                        <div class="value">
                            <select id="equipment_id" name="equipment_id">
                                <!-- BEGIN DYNAMIC BLOCK: plant_row -->
                                <option value="{!! trans('PLANT_ID ') !!}">{!! trans('PLANT_NAME ') !!}</option>
                                <!-- END DYNAMIC BLOCK: plant_row -->
                            </select>
                        </div>

                        <label>Zdieľaný</label>
                        <div class="value" style="margin-bottom:20px;">
                            <input type="radio" name="public" id="reg-setting-pulbic-yes" value="1" /><span style="color:#555555;">Áno</span>
                            <input type="radio" name="public" id="reg-setting-pulbic-no" value="2" /><span style="color:#555555;">Nie</span>
                        </div>

                        <div style="float:left">
                            <input type="submit" value="Uložiť nastavenia" class="default-submit-btn" >
                        </div>
                        <div class="ajax_loader" >
                            <img src="images/3.gif" width="25" alt="loading..." />
                        </div>
                        <div style="clear:both"></div>

                    </div>	
                </form>
            </div>
        </div>
    </div>


    <div id="reg-new-form">
        <div style="padding:20px;">
            <h2>Pridať nový regulátor</h2>
            <form id="new-reg" action="" method="post" enctype="multipart/form-data" onsubmit="create_new_reg();
        return false;">
                <div class="err_warning" ></div>
                <label style="margin-top:0px;">{!! trans('LABEL_NAME_REGULATOR ') !!}</label>
                <input class="" type="text" name="name" value="" />

                <label>{!! trans('LABEL_BODY_REGULATOR ') !!}</label>
                <textarea class="" name="body">y1=u1</textarea>
                <div style="font-style:italic;font-size:11px;color:#21759B;">
                    y1 = f(u1)<br />
                    y1 = výstup z regulátora<br />
                    u1 = vstup do regulátora<br />
                </div>

                <label>{!! trans('LABEL_SYSTEM ') !!}</label>
                <select name="equipment_id">
                    <!-- BEGIN DYNAMIC BLOCK: plant_row -->
                    <option value="{!! trans('PLANT_ID ') !!}">{!! trans('PLANT_NAME ') !!}</option>
                    <!-- END DYNAMIC BLOCK: plant_row -->
                </select>

                <label>Zdieľaný</label>
                <input type="radio" name="public" value="1" /><span style="color:#555555;">Áno</span>
                <input type="radio" name="public" checked="checked" value="2" /><span style="color:#555555;">Nie</span>

                <div style="clear:both;height:15px;"></div>
                <input type="button" name="" value="Zatvoriť okno" class="info-dialog-close-btn" style="float:left;margin-top:0px;">
                <input type="hidden" value="1" name="add_regulator" />
                <input type="submit" value="Pridať regulátor" style="float:right;" class="submit" id="">
                <div style="clear:both;"></div>
            </form>
        </div>
    </div>

    <div id="delete-reg">
        <div style="padding:20px;">
            <h2>{!! trans('DELETE_CONTROLLER_QUESTION ') !!}</h2>
            <form id="delete-reg-form" action="" method="post" onsubmit="delete_reg_process();
        return false;">
                <div style="clear:both;height:15px;"></div>
                <input type="button" name="" value="{!! trans('CLOSE_WINDOW_TITLE ') !!}" class="info-dialog-close-btn" style="float:left;margin-top:0px;">
                <input type="hidden" value="1" name="delete_reg" />
                <input type="hidden" id="regId" value="" name="regId">
                <input type="submit" value="{!! trans('TRASH_TITLE ') !!}" style="float:right;" class="submit" id="">
                <div style="clear:both;"></div>	
            </form>	
        </div>
    </div>

</div>