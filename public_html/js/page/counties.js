var buttonAddCounty = null,
    divCounty = null,
    editingCounty = false,
    countyName = null,
    counties = [];


function getCountyId(countyName){
    var id = 0;

    for(var i=0; i<counties.length; i++){
        var values = counties[i].split(':');
        if(values[1] == countyName){
            id = values[0];
            break;
        }
    }

    return id;
}


function getCounties(){
    $.getJSON(
        "private/ajax/ajax_counties.php",
        {
            type: 'fetch'
        },
        function(jsonObj){
            if(jsonObj['success']){
                jsonObj['countiesList'].forEach(function(element){
                   counties.push(element['id'] + ":" + element['name']);
                });
            }
            else
                console.log("Error");
        }//function
    );
}


function updateCountyName(countyName, countyId){
    $.ajax({
        url: "private/ajax/ajax_counties.php",
        type: "GET",
        async: true,
        dataType: "json",
        data:{
            type: 'update',
            name: countyName,
            id: countyId
        },
        success: function(jsonObj) {
            if(!jsonObj['success'])
                console.log("Ivica error");
        },
        error: function( xhr, status, errorThrown ) {
            alert("error: " + xhr + "\n" + status  + "\n" + errorThrown);
        }
    });
}



function handlerButtonConfirm(listElementOne, listElementTwo, listElementThree){
    var newCountyName = listElementOne.childNodes[0].value;
    if(newCountyName.length == 0)
        newCountyName = countyName;
    updateCountyName(newCountyName, getCountyId(countyName));
    redirect();
}



function handlerButtonCancel(listElementOne, listElementTwo, listElementThree){
    redirect('action=counties');
}



function removeModerator(element){
    console.log(element);
    console.log(element.parentNode);
}



function clearElements(listElementOne, listElementTwo, listElementThree){
    $(listElementOne).empty();
    $(listElementTwo).empty();
    $(listElementThree).empty();
}


function createInitialElements(countyName, listElementOne, listElementTwo, listElementThree){
    var elementOne = $("<a onclick=\"toggleSubList(this);\">" + countyName + "</a>");
    var elementTwo = $("<a onclick=\"editCounty(this);\">Uredi</a>");
    var elementThree = $("<a onclick=\"deleteCounty(this);\">Izbriši</a>");

    $(listElementOne).append($(elementOne));
    $(listElementTwo).append($(elementTwo));
    $(listElementThree).append($(elementThree));
}



function editCounty(element){
    if(editingCounty)
        return;

    editingCounty = true;
    var listElementTwo = element.parentNode;
    var listElementOne = listElementTwo.previousSibling;
    var listElementThree = listElementTwo.nextSibling;

    console.log(listElementOne);

    countyName = listElementOne.childNodes[0].innerHTML;
    clearElements(listElementOne, listElementTwo, listElementThree);

    var inputChangeName = $("<input type=\"text\" value=\"" + countyName + "\">");
    var textButtonConfirm = $("<a>Potvrdi</a>");
    var textButtonCancel = $("<a>Odustani</a>");

    $(listElementOne).append($(inputChangeName));
    $(listElementTwo).append($(textButtonConfirm));
    $(listElementThree).append($(textButtonCancel));

    $(inputChangeName).focus();

    $(textButtonConfirm).click(function () {
        handlerButtonConfirm(listElementOne, listElementTwo, listElementThree);
    });

    $(textButtonCancel).click(function(){
        handlerButtonCancel(listElementOne, listElementTwo, listElementThree);
    });
}




function deleteCounty(element){
    var currentCountyName = element.parentNode.previousSibling.previousSibling.childNodes[0].innerHTML;
    var countyId = getCountyId(currentCountyName);
    console.log(countyId);

    $.ajax({
        url: "private/ajax/ajax_counties.php",
        type: "GET",
        async: true,
        dataType: "json",
        data:{
            type: 'delete',
            id: countyId
        },
        success: function(jsonObj) {
            if(jsonObj['success'])
                redirect('&operation=success', true);
            else{
                console.log(jsonObj['desc']);
                redirect('&operation=fail', true);
            }
        },
        error: function( xhr, status, errorThrown ) {
            alert("error: " + xhr + "\n" + status  + "\n" + errorThrown);
        }
    });
}





function init(){
    buttonAddCounty = $("#buttonAddCounty");
    divCounty = $("#divCountyInput");

    getCounties();
}


init();

