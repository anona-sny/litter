let timeout = null;

$(document).ready(function () {
    $("#only-js").css("display", "block");
    $("#username-input").on("keyup", function () {
        if(timeout) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(function () {
            checkUsername();
        }, 300);
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#profile-photo-preview').css('background-image', 'url(' + e.target.result + ')');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function checkForm() {
    let username = $("#username-input");
    let password1 = $("#password-input");
    let password2 = $("#password-input-again");
    let email = $("#email-input");
    let usernameRegex = /^[A-Za-z0-9_]{1,25}$/;
    let passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/;
    let emailRegex = /[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/;
    let allPass = true;
    if(!usernameRegex.test(username.val())) {
        allPass = false;
        username.addClass("invalid");
        username.on("keydown", function () {
            username.removeClass("invalid");
            username.off("keydown");
        });
    }
    if(!passwordRegex.test(password1.val())) {
        allPass = false;
        password1.addClass("invalid");
        password1.on("keydown", function () {
            password1.removeClass("invalid");
            password1.off("keydown");
        });
    }
    if(password1.val()!=password2.val()){
        allPass = false;
        password2.addClass("invalid");
        password2.on("keydown", function () {
            password2.removeClass("invalid");
            password2.off("keydown");
        });
    }
    if(!emailRegex.test(email.val())){
        allPass = false;
        email.addClass("invalid");
        email.on("keydown", function () {
            email.removeClass("invalid");
            email.off("keydown");
        });
    }
    if(allPass){
        $.ajaxSetup({async: false});
        let ajax = $.post("index.php", {lookusernames: true, username: username.val()}, function(resultData) {});
        console.log(ajax.responseText);
        if(ajax.responseText == "false") {
            username.addClass("invalid");
            username.on("keydown", function () {
                username.removeClass("invalid");
                username.off("keydown");
            });
            return false;
        }
        return true;
    } else {
        return false;
    }
}

function checkUsername() {
    console.log("hello");
    let username = $("#username-input");
    $.ajaxSetup({async: false});
    let ajax = $.post("index.php", {lookusernames: true, username: username.val()}, function(resultData) {});
    console.log(username.val());
    console.log(ajax.responseText);
    if(ajax.responseText == "false") {
        username.addClass("invalid");
        username.on("keydown", function () {
            username.removeClass("invalid");
            username.off("keydown");
        });
    }
}