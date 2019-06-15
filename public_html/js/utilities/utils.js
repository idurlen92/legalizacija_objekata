
Date.prototype.getDateString = function(){
    var strDate = this.getFullYear().toString() + "-";
    strDate += (this.getMonth() + 1) < 10 ? "0" + (this.getMonth() + 1).toString() : (this.getMonth() + 1).toString();
    strDate += "-";
    strDate += (this.getDate() < 10) ? "0" + this.getDate().toString() : this.getDate().toString();
    return strDate;
};


String.prototype.startsWith = function(str){
    return this.substring(0, str.length).toLowerCase() === str;
};


function pxToEm(px){
    return px / parseFloat($("body").css("font-size"));
}


function emToPx(em){
    return em * parseFloat($("body").css("font-size"));
}


function getGetVariable(key){
    var queryString = window.location.search;
    var variables = queryString.substr(1).split("&");

    var value = null;
    for(var i=0; i< variables.length; i++){
        if(decodeURIComponent(variables[i]).search(key) > -1){
            value = decodeURIComponent(variables[i]).split("=")[1];
            break;
        }
    }

    return value;
}


function redirect(queryString, append){
    if(queryString == null || queryString == undefined)
        window.location.reload();
    else if(append === null || append === undefined || !append)
        window.location.href = 'index.php?' + queryString;
    else{
        var currentQueryStr = window.location.search;
        if(currentQueryStr.search(queryString) > -1)
            window.location.reload();
        else
            window.location.search += queryString;
    }

}


//TODO: function createDialog(){}



// ---------- Object for sorting, searching and editing tables ---------
var tableReferences = [];

function getCustomTableReference(table){
    for(var k=0 ; k<tableReferences.length && tableReferences[k].table != table; ++k)
        ;// semicollon IMPORTANT
    return tableReferences[k];
}


function CustomTable(table){
    if(table === undefined || table === null)
        return;

    tableReferences.push(this);

    this.table = table;
    this.isEditing = false;
    this.isEditEnabled = false;
    this.isInserting = false;
    this.headerIndex = (this.table.childNodes.length > 2 ? 1 : 0);
    this.bodyIndex = (table.childNodes.length === 3) ? 2 : 1;
    this.buttonAdd = undefined;
    this.buttonCancel = undefined;
    this.buttonDelete = undefined;
    this.rowInput = undefined;
    this.tempEditCells = undefined;

    if(this.table.childNodes[this.bodyIndex].childNodes === undefined || this.table.childNodes[this.bodyIndex].childNodes.length === 0)
        return;

    for(var i=0; i<this.table.childNodes[this.headerIndex].childNodes[0].childNodes.length; i++){
        var headerCol = this.table.childNodes[this.headerIndex].childNodes[0].childNodes[i];
        if(headerCol.childNodes !== undefined && headerCol.childNodes.length > 0)
            headerCol.onclick = CustomTable.prototype.handlerSort;
    }

    this.inputSearch = $("<input type=\"text\" id=\"inputSearchTable\"/>")[0];
    this.inputSearch.onkeyup = CustomTable.prototype.handlerSearch;

    var labelSearch = $("<label for=\"inputSearchTable\">Pretraži</label>")[0];
    var divSearch = $("<div id=\"tableSearch\"/>")[0];
    $(divSearch).append($(labelSearch)).append($(this.inputSearch));
    $(divSearch).insertAfter($(this.table));
}



CustomTable.prototype.getStartIndex = function (span) {
    var cells = this.table.childNodes[this.headerIndex].childNodes[0].childNodes;
    var startIndex = 0;
    for(var k=0; cells[k].innerHTML.length === 0 && k < cells.length; k++){
        if(span === null || span === undefined || !span)
            startIndex++;
        else
            startIndex += parseInt(cells[k].getAttribute('colspan'));
    }

    return startIndex;
};



CustomTable.prototype.handlerSearch = function(){
    var searchText = $(this).val().toLowerCase();
    var table = this.parentNode.previousSibling;
    var reference = getCustomTableReference(table);

    var rows = table.childNodes[reference.bodyIndex].childNodes;
    var length = reference.isEditEnabled ? (rows.length - 2) : rows.length;
    for(var i=0; i<length; i++){
        var currentRow =rows[i];
        var containsText = false;
        for(var j=0; j<currentRow.childNodes.length; j++){
            var cellText = currentRow.childNodes[j].innerHTML.toString();
            if(cellText.startsWith(searchText)){
                containsText = true;
                break;
            }
        }// for 2
        currentRow.style.display = containsText ? 'table-row' : 'none';
    }// for 1
};



CustomTable.prototype.handlerSort = function (){
    var reference = getCustomTableReference(this.parentNode.parentNode.parentNode);
    var tableName = getGetVariable('table');

    var columnIndex = this.cellIndex - reference.getStartIndex(false) + 1;

    var sortOrder = getGetVariable('order') === null ? 'asc' : (getGetVariable('order') == 'asc' ? 'desc' : 'asc');
    var tableName = getGetVariable('table');

    var queryString = 'action=' + getGetVariable('action') + '&';
    if(tableName !== null)
        queryString += 'table=' + tableName + '&';
    queryString += 'column=' + (columnIndex) + '&order=' + sortOrder;
    redirect(queryString);
};



CustomTable.prototype.addRow = function(){
    var row = this.parentNode.parentNode;
    var reference = getCustomTableReference(row.parentNode.parentNode);
    if(reference.isEditing)
        return;

    reference.isInserting = true;
    row.style.display = 'none';
    row.nextSibling.style.display = 'table-row';
    $(row.nextSibling.childNodes[1].childNodes[0]).focus();
};


CustomTable.prototype.cancelInput = function (){
    var row = this.parentNode.parentNode;

    var reference = getCustomTableReference(row.parentNode.parentNode);
    reference.isInserting = false;

    row.style.display = 'none';
    row.previousSibling.style.display = 'table-row';
};


CustomTable.prototype.checkBoxClick = function(){
    var tableBody = this.parentNode.parentNode.parentNode;
    var reference = getCustomTableReference(tableBody.parentNode);

    var checked = 0;
    for(var k=0; k<tableBody.childNodes.length; k++){
        if($(tableBody.childNodes[k].childNodes[0].childNodes[0]).is(':checked'))
            checked++;
    }

    console.log(checked);
    reference.buttonDelete[0].style.display = (checked === 0) ? 'none' : 'inherit';
};



CustomTable.prototype.cancelEdit = function(){
    var row = this.parentNode.parentNode;
    var reference = getCustomTableReference(row.parentNode.parentNode);

    $(row.childNodes[0]).empty();
    $(row.childNodes[1]).empty();
    $(row.childNodes[0]).append($(reference.tempEditCells[0]));
    $(row.childNodes[1]).append($(reference.tempEditCells[1]));

    var contentIndex = reference.getStartIndex(true);
    for(var k = contentIndex; k < row.childNodes.length; k++){
        var value = row.childNodes[k].childNodes[0].getAttribute("value");
        $(row.childNodes[k]).empty();
        $(row.childNodes[k]).append(value);
    }

    reference.isEditing = false;
    reference.assignEditHandlers();
};



CustomTable.prototype.confirmEdit = function(){
    var row = this.parentNode.parentNode;
    var reference = getCustomTableReference(row.parentNode.parentNode);

    var jsonData = {table: getGetVariable('table')};
    var sortCol = getGetVariable('column');
    var sortOrder = getGetVariable('order');
    if(sortCol !== null){
        jsonData['column'] = sortCol;
        jsonData['order'] = sortOrder;
    }
    jsonData['rowNumber'] = row.rowIndex;

    var dataArray = [];
    for(var i = reference.getStartIndex(true); i<row.childNodes.length; i++){
        dataArray.push($(row.childNodes[i].childNodes[0]).val());
    }
    jsonData['data'] = dataArray;

    $.ajax({
        url: "private/ajax/ajax_data.php",
        type: "POST",
        async: true,
        dataType: "json",
        data: jsonData,
        success: function(jsonObj) {
            if(!jsonObj['success']){
                console.log("ajax error: " + jsonObj['desc'])
                redirect('&operation=false', true);
            }
            else {
                redirect('&operation=success', true);
            }
        },
        error: function( xhr, status, errorThrown ) {
            console.log("error: " + xhr + "\n" + status  + "\n" + errorThrown);
        }
    });
};



CustomTable.prototype.editRow = function (){
    var row = this.parentNode.parentNode;
    var reference = getCustomTableReference(row.parentNode.parentNode);
    if(reference.isEditing || reference.isInserting)
        return;

    reference.isEditing = true;

    var buttonCancelEdit = $("<input type=\"button\" value=\"Poništi\"/>");
    var buttonConfirmEdit = $("<input type=\"button\" value=\"Potvrdi\"/>");
    $(buttonCancelEdit).click(CustomTable.prototype.cancelEdit); // TODO:
    $(buttonConfirmEdit).click(CustomTable.prototype.confirmEdit);  // TODO:

    reference.tempEditCells = [$(row.childNodes[0].childNodes[0]).clone(true), $(row.childNodes[1].childNodes[0]).clone(true)];
    $(row.childNodes[0]).empty();
    $(row.childNodes[1]).empty();
    $(row.childNodes[0]).append($(buttonConfirmEdit));
    $(row.childNodes[1]).append($(buttonCancelEdit));


    var contentIndex = reference.getStartIndex(true);
    for(var k = contentIndex; k < row.childNodes.length; k++){
        var value = row.childNodes[k].innerHTML;
        $(row.childNodes[k]).empty();
        var input = $("<input type=\"text\" id=\"crudInput" + (k - contentIndex + 1) +  "\" value=\"" + value + "\">");
        $(row.childNodes[k]).append($(input));
    }

    $(row.childNodes[contentIndex].childNodes[0]).focus();
};


CustomTable.prototype.assignEditHandlers = function(){
    for(var i=0; i<this.table.childNodes[this.bodyIndex].childNodes.length - 2; i++){
        var row = this.table.childNodes[this.bodyIndex].childNodes[i];
        row.childNodes[1].childNodes[0].onclick = CustomTable.prototype.editRow;
    }
};


CustomTable.prototype.enableEdit = function () {
    if(this.isEditEnabled)
        return;

    this.isEditEnabled = true;
    var contentRows = this.table.childNodes[this.bodyIndex].childNodes;
    var isEmpty = (contentRows === undefined || contentRows.length == 0);

    // ---------- 1)Empty header column ----------
    var titlesRow = this.table.childNodes[this.headerIndex].childNodes[0];
    var emptyCell = $('<td/>')[0];
    $(titlesRow.childNodes[0]).before($(emptyCell));

    if(!isEmpty)
        emptyCell.setAttribute("colspan", 2);

    // ---------- 2) Adding checkBoxes and edit/delete 'buttons' ----------
    for(var i=0; i<contentRows.length; i++){
        var checkBox = $("<input type=\"checkbox\" name=\"checkedRows[]\" value=\"" + i + "\">");
        $(checkBox).click(CustomTable.prototype.checkBoxClick);
        var checkCell = $("<td/>").append($(checkBox));

        var editAnchor = $("<a>Uredi</a>");
        var editCell = $("<td/>").append($(editAnchor));

        $(checkCell).insertBefore($(contentRows[i].childNodes[0]));
        $(editCell).insertAfter($(checkCell));
    }

    // ---------- 3) Buttons row (add/delete) ----------
    if(!isEmpty){
        this.buttonAdd = $("<input type=\"button\" id=\"buttonAddRow\" value=\"Dodaj\"/>");
        this.buttonDelete = $("<input type=\"submit\" id=\"buttonDeleteMulti\" name=\"delete\" value=\"Izbriši označeno\"/>");

        this.buttonDelete[0].style.display = 'none';
        $(this.buttonAdd).click(CustomTable.prototype.addRow);

        var rowCell = $("<td/>");
        $(rowCell)[0].setAttribute('colspan', contentRows[0].childNodes.length);
        $(rowCell).append($(this.buttonAdd)).append($(this.buttonDelete));

        var row = $("<tr/>").append($(rowCell));
        $(this.table.childNodes[this.bodyIndex]).append($(row));
    }

    // ---------- 4) Input row ----------
    this.buttonCancel = $("<input type=\"button\" id=\"buttonCancelEdit\" value=\"Poništi\"/>");
    var buttonConfirm = $("<input type=\"submit\" id=\"buttonConfirmEdit\" name=\"insert\" value=\"Potvrdi\"/>");
    $(this.buttonCancel).click(CustomTable.prototype.cancelInput);

    var buttonsCell = $('<td/>');
    $(buttonsCell).append($(buttonConfirm));

    this.rowInput = $('<tr/>');
    $(this.rowInput).append($(buttonsCell));

    if(!isEmpty){
        $(buttonsCell).append($(this.buttonCancel));
        $(buttonsCell)[0].setAttribute('colspan', this.getStartIndex(true));
        this.rowInput[0].style.display = 'none';
    }

    // ----- 4.1) Adding input elements to input row -----
    var startIndex = this.getStartIndex(false);
    for(var j = startIndex; j < titlesRow.childNodes.length ; j++){
        //TODO: input type(optional)
        var cell = $('<td/>').append($("<input type=\"text\" name=\"crudInput" + (j - startIndex + 1) + "\"/>"));
        $(this.rowInput).append($(cell));
    }

    $(this.table.childNodes[this.bodyIndex]).append($(this.rowInput));

    if(!isEmpty)
        this.assignEditHandlers();
};