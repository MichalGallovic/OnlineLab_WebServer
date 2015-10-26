$(document).ready(function() {
    
    // nacitame si udaje
    loadTable();

    // Dialogove okno na vytvorenie noveho device
    $('#create-new-equipment').click(function() {

        $("#equip-new-form").dialog({
            width: 356,
            dialogClass: "info-dialog",
            modal: true,
            position: ['center', 'center']
        });

        $(".info-dialog-close-btn").click(function() {
            $("#equip-new-form").dialog("close");
        });
    });

    // Akcia na BACK button v casti editacie realneho zariadenia
    $('#back-to-equip').click(function() {
        $('#equip-settings').slideUp(function() {
            $('#equipments_list').slideDown(function() {
                loadTable();
            });
        });
    });
});



/**
 * FUnkcia nacita obsah
 * 
 * @returns {undefined}
 */
function loadTable() {
    $("table.equipments tbody").load(ROOT_PATH + "includes/modules/equipments/loaddata.php?action=get_rows");
}



/**
 * Funkcia na pridanie noveho realneho zariadenia
 * 
 * @returns {Boolean}
 */
function create_new_equipment() {
    var form = $('#new-equip').serialize();
    $('.err_warning').hide();
    $('#new-equip input ').removeClass('red-border');

    $.post(ROOT_PATH + "includes/modules/equipments/loaddata.php?action=add_row", $('#new-equip').serialize(), function(data) {

        // test na nevyplnene udaje
        if (data.status === -1) {
            $('.err_warning').html(data.msg);
            for (i = 0; i < data.empty.length; i++) {
                $('#new-equip input[name="' + data.empty[i] + '"]').addClass('red-border');
            }

            $('#new-equip input[name="' + data.empty[0] + '"]').focus();

            $('.err_warning').show();
        }

        // ak vsetko prebehlo v poriadku
        if (data.status === 1) {

            $(".ok_warning").html(data.msg);
            $("#equip-new-form").dialog("close");

            // nahodime animaciu ze prebieha pridavanie
            $("table.equipments tbody").html('<tr><td colspan="6" style="padding:25px;"><div id="chart_ajax_loader"></div></td></tr>');

            // nakoniec nacitame novy obsah
            var t = setTimeout("loadTable()", 2500);

            $(".ok_warning").fadeIn().delay(2500).fadeOut();
        }

    }, "json");


    return false;
}



/**
 * Tato funkcia otvori dialog pre zmazanie device.
 * Je volana ked clovek klikne na ikonku kosa. Cize na zmazanie
 * 
 * @param {type} equipmentId
 * @returns {undefined}
 */
function delete_equipment(equipmentId) {
    $('#equipmentId').val(equipmentId);

    $("#delete-equipment").dialog({
        width: 356,
        dialogClass: "info-dialog",
        modal: true,
        position: ['center', 'center']
    });

    $(".info-dialog-close-btn").click(function() {
        $("#delete-equipment").dialog("close");
    });

}



/**
 * Tato funkcia zmaze realne zariadenie
 * 
 * @returns {undefined}
 */
function delete_equipment_process() {

    var equipmentId = $('#equipmentId').val();

    $("#delete-equipment").dialog("close");

    $('#row-' + equipmentId).fadeOut(function() {

        $.post(ROOT_PATH + "includes/modules/equipments/loaddata.php?action=delete_row", {"delete_equipment": 1, "equipmentId": equipmentId}, function(data) {
        }, "json");

        // obnovime stred
        loadTable();
    });
}



/**
 * Tato funkcia zrobi dialogovu ponuku pre editaciu realneho zariadenia
 * 
 * @param {type} eqpID
 * @returns {undefined}
 */
function edit_equip(eqpID) {

    // najprv skryjeme udaje, aby sme ich potom mohli zobrazit
    $("#equip-settings-box").hide();

    // zrolujeme - skryjeme zoznam zariadeni
    $('#equipments_list').slideUp(function() {

        // odskrolujeme - zobrazime zoznam zariadeni
        $('#equip-settings').slideDown(function() {

            $.post(ROOT_PATH + "includes/modules/equipments/loaddata.php?action=prepare_edit", {"eqp_settings": 1, "eqpID": eqpID}, function(data) {

                // do formulara pridame hodnoty
                $('input[name="equip_id"]').val(data.eqp_id);
                $("#settings_equip_id").html(data.eqp_id);
                $("#settings_equip_name").val(data.eqp_name);
                $("#settings_equip_ip").val(data.eqp_ip);
                $('#settings_equip_colour').val(data.eqp_colour);

                // zobrazime uz udaje
                $("#equip-settings-box").show();
            }, "json");
        });
    });
}

/**
 * FUnkcia ulozi zmeny vykonane pri editacii realneho zariadenia
 * 
 * @returns {undefined}
 */
function save_equipment_settings() {

    $(".ajax_loader").show();

    $.post(ROOT_PATH + "includes/modules/equipments/loaddata.php?action=save_edited", $('#equip-settings-form').serialize(), function(data) {
        
    $(".ok_warning").html(data.msg);
    $(".ok_warning").fadeIn().delay(2500).fadeOut();
    $(".ajax_loader").hide();
    
    }, "json");
}