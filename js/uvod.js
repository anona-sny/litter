// zobrazí obsah stránky pouze pokud je k dispozici JS
$(document).ready(function () {
    $("#only-js").css("display", "block");
    $("#login-button").click(function () {
        loginOvrflw();
    })
    $("#back-button").click(function () {
        loginOvrflw();
    })
    $("#login").click(function () {
        loginUser();
    })
})

function loginOvrflw() {
    let a = $("#login-overflow");
    console.log(a);
    if(a.css("display") === "" || a.css("display") === "none"){
        a.css("display", "block");
    } else {
        a.css("display", "none");
    }
}

function loginUser() {
    let checkboxInput = $("#not-robot-checkbox");
    let usernameInput = $("#login-username-input");
    let passwordInput = $("#login-password-input");
    let username = usernameInput.val();
    let password = passwordInput.val();
    passwordInput.val("");
    if(username.length < 2 ){
        let color = usernameInput.css("borderColor");
        usernameInput.css("border", "1px solid red");
        setTimeout(function () {
            usernameInput.css("border", "1px solid "+color);
        }, 2000);
    }
    if(password.length < 2) {
        let color = passwordInput.css("borderColor");
        passwordInput.css("border", "1px solid red");
        setTimeout(function () {
            passwordInput.css("border", "1px solid "+color);
        }, 2000);
    }
    if(checkboxInput.is(":checked") === false) {
        let color = checkboxInput.css("borderColor");
        checkboxInput.css("border", "1px solid red");
        setTimeout(function () {
            checkboxInput.css("border", "1px solid "+color);
        }, 2000);
    }
    if(username.length < 2 || password.length < 2 || checkboxInput.is(":checked") === false) {
        return;
    }
    let data = {
        username: username,
        password: password
    };
    $.post("robot.php", {command: "login", data: JSON.stringify(data)}, function (data) {
        console.log(data);
        let obj = JSON.parse(data);
        console.log(obj);
        if(obj.hasOwnProperty("data")&&obj.data == "logged") {
            window.location.href += "wall/";
        } else if(obj.hasOwnProperty("error")&&obj.error == "logined"){
            window.location.href += "wall/";
        } else {
            console.log(obj);
            alert(obj.hasOwnProperty("error")?obj.error:"Undefined error");
        }
    });
}