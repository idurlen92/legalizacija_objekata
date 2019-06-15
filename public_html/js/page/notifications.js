var inputTitle = null,
    inputMessage = null,
    checkUsers = null;


function handler(inputElement){
    if($(inputElement).val().length == 0)
        changeBorder(inputElement, false);
    else{
        //alert($(inputElement).css('border'));
        $(inputElement).css({
            border: ''
        });
    }

}


function isFormValid(){
    var valid = ($(inputTitle).val().length > 0 && $(inputMessage).val().length > 0);
    valid = valid && ($("input:checked").length > 0);

    return valid;
}



function init(){
    inputTitle = $("#inputTitle");
    inputMessage = $("#inputMessage");
    checkUsers = $("#checkUsers");

    inputTitle.on('focusout', function(){
       handler(inputTitle);
    });
    inputMessage.on('focusout', function () {
       handler(inputMessage);
    });
}



init();