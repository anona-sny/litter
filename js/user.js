$(document).ready(function () {
    sett();
    $("#save").on("click", function () {
        saveUserData();
    });

    $("#back-home").on("click", function () {
        window.location.href = "../wall/";
    })
});

function sett(){

}

function saveUserData(){
    let newPassword = $("#new-password-input").val();
    let oldPassword = $("#old-password-input").val();
    let aboutMe = $("#about-me-text").val();
    let passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/;
    let dataObj = {};
    if(newPassword.length > 0){
        if(passwordRegex.test(newPassword)){
            dataObj.newPassword = newPassword;
            dataObj.oldPassword = oldPassword;
        } else {
            alert("Nové heslo je zadáno ve špatném formátu, proto nebude uloženo");
        }
    }
    dataObj.aboutme = aboutMe;
    $.ajax({
        url: '../robot.php',
        type: 'POST',
        data: {command: "changeUserData", data: JSON.stringify(dataObj)},
        success: function (response) {
            console.log(response);
            if(response != "true"){
                alert("nastala chyba");
            }
        }
    });

}

function deleteTweet(object){
    let data = {
        idPost: object.getAttribute("post-id")
    };
    $.ajax({
        url: '../robot.php',
        type: 'POST',
        data: {command: "deleteArticle", data: JSON.stringify(data)},
        success: function (response) {
            console.log(response);
            if(response === "true"){
                alert("Úspěšně odstraněno");
            } else {
                alert("cannot delete, access denied");
            }
        }
    });
}

function likeTweet(object){
    let numberCountHTML = object.parentElement.lastChild;
    let data = {
        idPost: object.getAttribute("post-id")
    };
    $.ajax({
        url: '../robot.php',
        type: 'POST',
        data: {command: "likeArticle", data: JSON.stringify(data)},
        success: function (response) {
            console.log(response);
            if(response === "liked"){
                numberCountHTML.innerHTML = parseInt(numberCountHTML.innerHTML)+1;
            } else if(response === "unliked") {
                numberCountHTML.innerHTML = parseInt(numberCountHTML.innerHTML)-1;
            } else {
                alert("chyba");
            }
        }
    });
}

function uploadImage(element){
    var formData = new FormData();
    formData.append("command", "saveImage");
    formData.append("data", "empty");
    formData.append('file', element.files[0]);
    $.ajax({
        url: '../robot.php',
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function (response) {
            if(response == "true") {
                window.location.reload();
            } else {
                alert("fotka je moc veliká, neplatná, nebo ve špatném formátu");
            }
        }
    });
}
