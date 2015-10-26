<link href="{!! trans('ROOT_PATH ') !!}includes/modules/equipments/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/equipments/js/default.js"></script>

<!--  Pager-->
<div class="ok_warning"></div>

<div id="pager_holder"></div>

<!-- Real devices listing -->
<div id="equipments">

    <div id="equipments_list">

        <div class="buttons">
            <a id="create-new-equipment" href="javascript:void();" title="" class="default-btn"><span>{!! trans('ADD_NEW_EQUIPMENT_TITLE ') !!}</span></a>
        </div>

        <table class="equipments" cellspacing="0">
            <thead>
                <tr>
                    <th class="first">Id.</th>
                    <th class="equip_name">{!! trans('EQUIP_NAME ') !!}</th>
                    <th class="equip_ip">{!! trans('EQUIP_IP ') !!}</th>
                    <th class="equip_colour">{!! trans('EQUIP_COLOUR ') !!}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot>
                <tr>
                    <th class="first">Id.</th>
                    <th>{!! trans('EQUIP_NAME ') !!}</th>
                    <th>{!! trans('EQUIP_IP ') !!}</th>
                    <th>{!! trans('EQUIP_COLOUR ') !!}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>


    <div id="equip-new-form">
        <div style="padding:20px;">
            <h2>{!! trans('ADD_NEW_EQUIPMENT_TITLE ') !!}</h2>
            <form id="new-equip" action="" method="post" enctype="multipart/form-data" onsubmit="create_new_equipment();
                    return false;">

                <div class="err_warning" ></div>

                <label style="margin-top:0px;">{!! trans('EQUIP_NAME ') !!}</label>
                <input class="" type="text" name="name" value="" />

                <div style="clear:both;height:15px;"></div>

                <label style="margin-top:0px;">{!! trans('EQUIP_IP ') !!}</label>
                <input class="" type="text" name="ip" value="" />

                <div style="clear:both;height:15px;"></div>

                <label style="margin-top:0px;">{!! trans('EQUIP_COLOUR ') !!}</label>
                <input class="" type="text" name="colour" value="" />

                <div style="clear:both;height:15px;"></div>

                <input type="button" name="" value="{!! trans('CLOSE_W ') !!}" class="info-dialog-close-btn" style="float:left;margin-top:0px;">

                <input type="hidden" value="1" name="add_equip" />

                <input type="submit" value="{!! trans('ADD_DEVICE ') !!}" style="float:right;" class="submit" id="">

                <div style="clear:both;"></div>
            </form>
        </div>
    </div>


    <div id="equip-settings">
        
        <div class="buttons">
            <a id="back-to-equip" href="javascript:void();" title="" class="default-btn"><span>{!! trans('BACK_TO_EQUIPMENTS ') !!}</span></a>
        </div>
        
        <div class="default-box" id="equip-settings-box">
            
            <div class="header"><span>{!! trans('CHANGE_EQUIPMENT_SETTINGS ') !!}</span></div>
            
            <div class="box-content" style="padding:20px;">
                
                <form id="equip-settings-form" action="" method="post" enctype="multipart/form-data" onsubmit="save_equipment_settings(); return false;">
                    
                    <input type="hidden" name="equip_id" value="" />
                    
                    <input type="hidden" name="equip_change_settings" value="1">
                    
                    <div class="column1">
                        
                        <label>Id</label>
                        <div class="value"><span id="settings_equip_id"></span></div>

                        <label>{!! trans('EQUIP_NAME ') !!}</label>
                        <div class="value">
                            <input type="text" value="" name="settings_equip_name" id="settings_equip_name">
                        </div>
                        
                        <label>{!! trans('EQUIP_IP ') !!}</label>
                        <div class="value">
                            <input type="text" value="" name="settings_equip_ip" id="settings_equip_ip">
                        </div>

                        <label>{!! trans('EQUIP_COLOUR ') !!}</label>
                        <div class="value">
                            <input type="text" value="" name="settings_equip_colour" id="settings_equip_colour">
                        </div>
                        
                        <div style="float:left">
                            <input type="submit" value="{!! trans('SAVE_CHANGE_EQUIPMENT_SETTINGS ') !!}" class="default-submit-btn" >
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


    <div id="delete-equipment">
        <div style="padding:20px;">
            <h2>{!! trans('DELETE_EQUIPMENT_QUESTION ') !!}</h2>
            <form id="delete-equipment-form" action="" method="post" onsubmit="delete_equipment_process();
                    return false;">

                <div style="clear:both;height:15px;"></div>

                <input type="button" name="" value="{!! trans('CLOSE_W ') !!}" class="info-dialog-close-btn" style="float:left;margin-top:0px;">

                <input type="hidden" value="1" name="delete_equipment" />

                <input type="hidden" id="equipmentId" value="" name="equipmentId">

                <input type="submit" value="{!! trans('TRASH_TITLE ') !!}" style="float:right;" class="submit" id="">
                <div style="clear:both;"></div>	
            </form>	
        </div>
    </div>
</div>