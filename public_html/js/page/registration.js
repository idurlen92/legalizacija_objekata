
var elementsJSON = null,
    form = null,
    inputEmail = null,
    inputPassword = null,
    inputPasswordRep = null,
    inputUsername = null;


function isGenderSelected(){
    return ($('#form_reg_gender_m').is(':checked') || $('#form_reg_gender_f').is(':checked'));
}


function isPasswordMatch(){
   return ($(inputPassword).val() == $(inputPasswordRep).val());
}


function handlerEmail(){
    $.getJSON(
        "private/ajax/ajax_registration.php",
        {
            'email' : $(inputEmail).val()
        },
        function(jsonObj){
            changeBorder($(inputEmail) ,(jsonObj['exists'] == 'false'));
        }
    );
}


function handlerPassword(){
    var regExPattern = /(?=.*\d){2,}(?=.*[a-z])(?=.*[A-Z]).{8,}/;
    var regexObj = new RegExp(regExPattern);
    changeBorder($(inputPassword), regexObj.test(inputPassword.val()));
}


function handlerPasswordRep(){
    changeBorder($(inputPasswordRep), isPasswordMatch());
}


function handlerUsername() {
    $.getJSON(
        "private/ajax/ajax_registration.php",
        {
            'username' : $(inputUsername).val()
        },
        function(jsonObj){
            changeBorder($(inputUsername) ,(jsonObj['exists'] == 'false'));
        }
    );
}


//TODO: check if empty fields, etc.
function isFormValid() {
    valid = isGenderSelected() && isPasswordMatch();
   // $(form).find('input')

    //return (isGenderSelected() && isPasswordMatch());
    return valid;
}





function init(){
    form = $("#form_registration");

    $("#form_reg_date").val(new Date().getDateString());

    inputEmail = $("#form_reg_email");
    inputPassword = $('#form_reg_password');
    inputPasswordRep = $('#form_reg_pass2');
    inputUsername = $('#form_reg_username');

    $(inputEmail).keyup(handlerEmail);
    $(inputPassword).keyup(handlerPassword);
    $(inputPasswordRep).keyup(handlerPasswordRep);
    $(inputUsername).keyup(handlerUsername);

    var node = createNotificationDiv();
}



init();

