var header = null,
    content  = null,
    navigation  = null,
    contentPadding  = null,
    footer = null;


function changeBorder(element, valid){
    $(element).css({
        "border-color" : (valid == true) ? "green" : "#c55053",
        "border-width" : "2px",
        "border-style" : "inset"
    });
}


function createNotificationDiv(text){
    var element = $("<div>Tekst nije postavljen</div>");
    $(element).css({
        border: "2px groove #c55053",
        borderRadius: "10px",
        backgroundColor: "#c55053",
        fontWeight: "bold",
        padding: "0.25em 0.5em",
        display: "none"
    });

    return element;
}



function positionNavigation(){
    if($(window).scrollTop() >= $(header).height()){
        $(navigation).css({
            position: "fixed",
            top: "0px",
            left: "0px",
            width: "100%"
        });
        $(content).css({
           paddingTop: ( 1 + pxToEm($(navigation).height())).toString() + "em"
        });
    }
    else{
        $(navigation).css({
            position: "static"
        });
        $(content).css({
            padding: "1em 0.5em 1em 0.5em"
        });
    }
}



function positionFooter(){
    var windowNoFooter = Math.round($(window).height() - $(footer).height());
    var contentHeight = Math.round($(content).height() + emToPx(2));
    var topHeight = Math.round($(navigation).height() + $(header).height());

    if((topHeight + contentHeight) < windowNoFooter){
        contentHeight += (windowNoFooter - contentHeight - topHeight);
        $(content).css({
            height: contentHeight.toString() + "px"
        });
    }
}




function init(){
    header = $("#header");
    navigation = $("#navigation");
    content = $("#content");
    footer = $("#footer");

    positionFooter();

    if(getGetVariable("action") == "profile")
        $("#profileTable").css({
           margin: 'auto'
        });

    $(window).bind("scroll", positionNavigation);
    $(window).bind("resize", positionNavigation);
    $(window).bind("resize", positionFooter);

    $(content).css({
       visibility: "visible"
    });
    $(footer).css({
        visibility: "visible"
    });

}


$(window).load(init);