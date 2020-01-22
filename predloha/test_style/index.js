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
})