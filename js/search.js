function searchProfiles() {
    let searchValue = $("#search-input").val();
    let usernameRegex = /^[A-Za-z0-9_]{0,25}$/;
    if(searchValue.length < 0 || !usernameRegex.test(searchValue)){
        return;
    }
    window.location.href = "../search/?query="+searchValue;
}

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
    });
    $("#search-button").on("click", function () {
        searchProfiles();
    });
});

function follow(username, button){
    dataObj = {
        username: username
    };
    $.ajax({
        url: '../robot.php',
        type: 'POST',
        data: {command: "follow", data: JSON.stringify(dataObj)},
        success: function (response) {
            console.log(response);
            if(response === "removed") {
                button.innerHTML = "Sledovat";
            } else if (response === "added") {
                button.innerHTML = "Nesledovat";
            } else {
                alert("error");
            }
        }
    });
}
