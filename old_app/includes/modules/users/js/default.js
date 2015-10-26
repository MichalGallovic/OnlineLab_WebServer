$(document).ready(function() {

    // nacitame si udaje
    loadTable();
    
});



/**
 * FUnkcia nacita obsah
 * 
 * @returns {undefined}
 */
function loadTable() {
    $("table.users tbody").load(ROOT_PATH + "includes/modules/users/loaddata.php?action=get_rows");
}



/**
 * Tato funkcia zrobi dialogovu ponuku pre editaciu usera
 * 
 * @param {type} eqpID
 * @returns {undefined}
 */
function edit_usr(eqpID) {

    // presmerujeme sa na modul PROFIL UZIVATELA, a predame mu id-cko
    window.location = ROOT_PATH + "dashboard.php?section_id=16&uid="+eqpID;
}
