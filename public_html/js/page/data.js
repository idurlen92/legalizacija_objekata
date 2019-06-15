var selectTables,
    tableName;


function handlerChangeTable(){
    var tableName = $(selectTables).val();
    if(tableName.length == 1)
        redirect("action=data");
    else
        redirect("action=data&table=" + tableName);
}



function init(){
    selectTables = $("#selectTables");
    $(selectTables).change(handlerChangeTable);

    var tableName = getGetVariable('table');
    if(tableName !== null){
        $(selectTables).val(tableName).prop('selected', true);
        var customTable = new CustomTable(document.getElementById("activeTable"));
        customTable.enableEdit();
    }
}


init();