var inputUsername = null,
    inputPassword = null,
    errorDiv = null;



function saveCredentials(jsonObj, username){
    localStorage.setItem('username', username);
    localStorage.setItem('userId', jsonObj['userId']);
    localStorage.setItem('userType', jsonObj['userType']);
}


function checkCredentials(){
    var username = $(inputUsername).val();
    var password = $(inputPassword).val();

    if(username.length > 0 && password.length > 0){
        $.ajax({
            url: "private/ajax/ajax_login.php",
            type: "GET",
            async: false,
            dataType: "json",
            data: {
                'username': $(inputUsername).val(),
                'password': $(inputPassword).val()
            },
            success: function(jsonObj){
                if(jsonObj['success'])
                    saveCredentials(jsonObj, $(inputUsername).val());
            }// function()
        });
    }

    return true;
}



function showNotification(){
    var message = "Unesite ";
    if($(inputUsername).val().length == 0)
        message += "korisničko ime";
    if($(inputPassword).val().length == 0)
        message += ($(inputUsername).val().length == 0) ? " i lozinku" : "lozinku";
    message += "!";

    $(errorDiv).empty();
    $(errorDiv).append($("<h5>" + message + "</h5>"));
    $(errorDiv).show('fast');
}



function isFormValid(){
    if($(inputUsername).val().length > 0 && $(inputPassword).val().length > 0){
        if($(errorDiv).is(":visible")){
            $(errorDiv).hide('fast');
        }
        return checkCredentials();
    }
    else{
        showNotification();
        return false;
    }
}



function init(){
    inputUsername = $("#form_login_username");
    inputPassword = $("#form_login_password");
    errorDiv = $("#errorDiv");

    if(getGetVariable("attempt") != null && getGetVariable("attempt") < 3)
        $(errorDiv).show('fast');
    else if(getGetVariable("locked") != null){
        $(errorDiv).empty();
        $(errorDiv).append("<h5>Korisnički račun zaključan, kontaktirajte administratora.</h5>");
        $(errorDiv).show('fast');
    }
    else if(getGetVariable("noUser") != null){
        $(errorDiv).empty();
        $(errorDiv).append("<h5>Nepostojeći korisnik!</h5>");
        $(errorDiv).show('fast');
    }

}


init();