function switchMenu(){
    let menu = document.getElementById("mobile-menu");
    if(menu.style.display == "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "";
    }
}

$(document).ready(function () {
    $("#bar-avatar-with-menu").click(function () {
        switchMenu();
    })
    $("#add-story").on("click", function () {
        showAdd();
    });
    $("#new-file-back").on("click", function () {
        showAdd();
    });
    $("#new-file-submit").on("click", function () {
        saveArticle();
    });
    $("#search-button").on("click", function () {
        searchProfiles();
    });
});

function showAdd(){
    let addArticle = document.getElementById("new-article-shadow");
    if(addArticle.style.display == "") {
        addArticle.style.display = "block";
    } else {
        addArticle.style.display = "";
    }
}

function saveArticle() {
    let header = $("#new-article-header").val();
    let body = $("#new-article-body").val();
    if(header.length > 0&&body.length) {
        let fileHTML = document.getElementById("new-article-file");
        var formData = new FormData();
        let article = {
            header: header,
            body: body
        }
        formData.append("command", "saveArticle");
        formData.append("data", JSON.stringify(article));
        console.log(fileHTML.files[0]);
        formData.append('file', fileHTML.files[0]);
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
                console.log(response);
                if (response === "true") {
                    window.location.reload();
                } else if (response === "text invalid data") {
                    alert("Text obsahuje neplatná data");
                } else if (response === "image corrupted") {
                    alert("Obrázek je poškozen nebo soubor není vůbec obrázkem");
                } else if(response === "header short") {
                    alert("Název článku musí být alespoň 3 znaky dlouhý");
                } else {
                    alert("Sakra, nastala výjimka, kterou nedokážu ošetřit");
                }
            }
        });
    } else {
        alert("Název ani text nesmí být prázdné");
    }
}

function searchProfiles() {
    let searchValue = $("#search-input").val();
    let usernameRegex = /^[A-Za-z0-9_]{0,25}$/;
    if(searchValue.length < 0 || !usernameRegex.test(searchValue)){
        return;
    }
    window.location.href = "../search/?query="+searchValue;
}