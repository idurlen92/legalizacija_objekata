
var userLoggedIn = null;

var divCounty = null,
    divContent = null,
    selectCounties = null;

var commentList = null,
    inputComment = null,
    buttonPost = null;



// ---------- 1) Commenting ----------
function createComment(infoText, commentText){
    var listElement = $("<li/>");
    $(listElement).css({
        display: 'none'
    });

    var commentDiv = $("<div class=\"comment\"/>");
    $(listElement).append($(commentDiv));

    var infoDiv = $("<div class=\"comment_info\"/>");
    var textDiv = $("<div class=\"comment_text\"/>");
    $(infoDiv).append(infoText);
    $(textDiv).append(commentText);

    $(commentDiv).append($(infoDiv));
    $(commentDiv).append($(textDiv));

    return listElement;
}



function handlerButtonPost(){
    var userId = localStorage.getItem('userId');
    var constructor_id = getGetVariable('id');
    var commentText = $(inputComment).val();

    if(commentText.length > 0) {
        $.ajax({
            url: "private/ajax/ajax_comments.php",
            type: "POST",
            async: true,
            dataType: "json",
            data: {
                user_id: userId,
                constructor_id: constructor_id,
                comment: commentText
            },
            complete: function(){
                $(inputComment).val("");
            },
            success: function(jsonObj) {
                if(!jsonObj['success'])
                    alert(jsonObj['desc']);
                else{
                    console.log(jsonObj['timestamp']);
                    var infoText = jsonObj['timestamp'] + " " + localStorage.getItem('username');
                    var newElement = createComment(infoText, commentText);

                    if(document.getElementById("comment_list").children.length > 0){
                        var firstChild = document.getElementById("comment_list").children[0];
                        $(newElement).insertBefore($(firstChild));
                    }
                    else
                        $("#comment_list").append($(newElement));

                    $(newElement).show('slow');
                }//else
            },
            error: function( xhr, status, errorThrown ) {
                alert("error: " + xhr + "\n" + status  + "\n" + errorThrown);
            }
        });

    }
}



function toggleSubList(element){
    if(!userLoggedIn && element.childNodes.length > 1){
        $(element.childNodes[1]).toggle('fast');
        event.stopPropagation();
    }
}



function handlerOptionChange(){
    if($(selectCounties).val() == '-')
        $(divContent).empty();
    else{
        $.getJSON("private/ajax/ajax_constructors.php",
            {
                'county' : $(selectCounties).val(),
                'loggedIn' : userLoggedIn
            },
            function(jsonObj){
                console.log(jsonObj['data']);
                $(divContent).empty();

                if(jsonObj['data'] !== undefined){
                    var list = $("<ul/>");

                    for(var i=0; i<jsonObj['data'].length; i++){
                        var listElement = $("<li onclick=\"toggleSubList(this);\">");
                        if(userLoggedIn){
                            var link = $("<a href=" + window.location.href + "&id=" + jsonObj['data'][i]['id'] + ">"
                            + jsonObj['data'][i]['name'] + " " + jsonObj['data'][i]['surname'] + "</a>");
                            $(listElement).append($(link));
                        }//if[2]
                        else{
                            var emptyAnchor = $("<a>" + jsonObj['data'][i]['name'] + " " + jsonObj['data'][i]['surname'] + "</a>");
                            $(listElement).append($(emptyAnchor));

                            var subList = $("<ul/>");

                            $(subList).append($("<li> Slika: " + jsonObj['data'][i]['images'] + "</li>"));
                            $(subList).append($("<li> Videa: " + jsonObj['data'][i]['videos'] + "</li>"));
                            $(subList).append($("<li> Dokumenata: " + jsonObj['data'][i]['documents'] + "</li>"));
                            $(listElement).append($(subList));
                        }//

                        $(list).append($(listElement));
                    }//for

                    $(divContent).append($(list));
                }//if[1]
                else{
                    var infoTag = $("<h3>Nema građevinara</h3>");
                    $(divContent).append($(infoTag));
                }
            }//function(jsonObj)

        );
    }//else
}




// ---------- 2) Grading ----------
function GradingTable(table){
    if(table === null || table === undefined) {
        console.log("Table error");
        return;
    }

    this.table = table;
    this.cells = table.childNodes[0].childNodes[0].childNodes;
    this.initialStates = [];

    this.insertGrade = function(grade){
        var userId = localStorage.getItem('userId');
        var constructor_id = getGetVariable('id');

        $.ajax({
            url: "private/ajax/ajax_grading.php",
            type: "GET",
            async: true,
            data: {
                type: 'insert',
                user_id: userId,
                constructor_id: constructor_id,
                grade: grade
            },
            dataType: "json",
            success: function(jsonObj) {
                if(!jsonObj['success']) {
                    console.log(jsonObj['desc']);
                    redirect('&operation=fail', true);
                }
                else{
                    console.log(jsonObj);
                    redirect('&operation=success', true);
                }
            },
            error: function(xhr, status, errorThrown ) {
                console.log("error: " + xhr + "\n" + status  + "\n" + errorThrown);
            }
        });
    };

    var reference = this;
    for(var k = 0; k < this.cells.length; k++) {
        this.initialStates.push((this.cells[k].childNodes[0].style.backgroundColor.length > 0));

        $(this.cells[k]).hover(
            function (){
               for(var j=0; j<=this.cellIndex; j++)
                   reference.cells[j].childNodes[0].style.backgroundColor = 'gold';
            },
            function(){
                for(var j=0; j<=this.cellIndex; j++)
                    reference.cells[j].childNodes[0].style.backgroundColor = reference.initialStates[j] ? 'gold' : '';
            }
        );

        $(this.cells[k]).click(function(){
            var clickedGrade = this.cellIndex + 1;
            reference.insertGrade(clickedGrade);
        });
    }
}



function checkGrading(){
    var userId = localStorage.getItem('userId');
    var constructor_id = getGetVariable('id');

    $.ajax({
        url: "private/ajax/ajax_grading.php",
        type: "GET",
        async: true,
        data:{
            type: 'check',
            user_id: userId,
            constructor_id: constructor_id
        },
        dataType: "json",
        success: function(jsonObj) {
            if(!jsonObj['success'])
                console.log(jsonObj['desc']);
            else if(jsonObj['canGrade'])
                var gradingTable = new GradingTable(document.getElementById("tableGrades"));
        },
        error: function(xhr, status, errorThrown ) {
            console.log("error: " + xhr + "\n" + status  + "\n" + errorThrown);
        }
    });
}



function init(){
    userLoggedIn = !(!getGetVariable('type') || getGetVariable('type') == '1');

    if(getGetVariable('id') == null){
        buttonConfirm = $("#buttonConfirm");
        divCounty = $("#divCounty");
        selectCounties = $("#selectCounties");

        divContent = $("<div/>");
        $(divContent).insertAfter($(divCounty));

        $(selectCounties).change(handlerOptionChange);
    }
    else{
        commentList = $("#comment_list");
        inputComment = $("#inputComment");
        buttonPost = $("#buttonPostComment");

        buttonPost.click(handlerButtonPost);
        checkGrading();
    }
}


init();
