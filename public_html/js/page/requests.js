var buttonNewRq = null,
    contentDiv = null,
    form = null,
    selectCounty = null,
    buttonAcceptConfirm = null,
    selectConstructor = null;




function handlerCounty(){
    $(selectConstructor).empty();
    $(selectConstructor).append($("<option value=\"-\">-</option>"));

    $.getJSON("private/ajax/ajax_constructors.php",{
            'county' : $(selectCounty).val(),
            'loggedIn' : true
        },
        function(jsonObj){
            if(!jsonObj['success']){
                console.log(jsonObj['desc']);
            }
            if(jsonObj['data'] !== undefined){
                for(var i=0; i<jsonObj['data'].length; i++){
                    var option = "<option value='" + jsonObj['data'][i]['id'] + "'>" + jsonObj['data'][i]['name']
                        + " " + jsonObj['data'][i]['surname'] + "</option>";
                    $(selectConstructor).append($(option));
                }
            }
        }//function(jsonObj)
    );
}


function handlerButtonAcceptConfirm(){
    var requestId = getGetVariable('id');
    $.ajax({
        url: "private/ajax/ajax_requests.php",
        type: "GET",
        async: true,
        dataType: "json",
        data: {
            type: 'update',
            id: requestId
        },
        success: function(jsonObj){
            if(jsonObj['success']){
                var href = 'action=requests&type=' + getGetVariable('type') + '&id=' +
                    requestId + '&operation=finished';
                redirect(href);
            }
            else
                alert(jsonObj['desc']);
        }// function()
    });
}



function isFormValid(){
    var valid = true;
    $(form).find('input').each(function(key, value) {
        if(!$(value).is(':submit')){
            valid = valid && ($(value).val().length > 0);
            changeBorder($(value), $(value).val().length > 0);
        }
    });

    $(form).find('select').each(function (key, value) {
        valid = valid && ($(key).val() != '-');
        changeBorder($(value), $(value).val() != '-');
    });


    valid = valid && ($(selectCounty).val() != '-' && $(selectConstructor).val() != '-');

    return  valid;
}



function toggleContent(element){
    $(element.nextSibling).toggle('fast');
    event.stopPropagation();
}



function init(){
    contentDiv = $("#content");

    if(getGetVariable('type') === null){
        buttonNewRq = $("#buttonNewRequest");
        $(buttonNewRq).click(function(){
            redirect('action=requests&type=new');
        });

        var tables = document.getElementsByTagName('table');
        for(var i=0; i<tables.length; i++){
            var arrangedTable = new CustomTable(tables[i]);
           // arrangedTable.enableEdit();
        }
    }
    else if(getGetVariable('type') == 'new'){
        form = $("#form_request");
        selectConstructor = $("#select_constructor");
        selectCounty = $("#select_county");

        $(selectCounty).change(handlerCounty);
    }
    else{
        buttonAcceptConfirm = document.getElementById("buttonAcceptConfirm");
        if(buttonAcceptConfirm !== null && buttonAcceptConfirm !== undefined){
            buttonAcceptConfirm.addEventListener('click', handlerButtonAcceptConfirm);
        }
    }
}//init()


init();