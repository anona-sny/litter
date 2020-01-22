<?php
session_start();
print_r($_SESSION);
?>

<html>
    <head>
        <title>Litter</title>
        <link rel="stylesheet" href="css/uvod.css">
        <meta charset="utf-8">
        <meta http-equiv="Content-language" content="cs">
        <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit=cover">
        <link rel="preload" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,600,700&display=swap" as="style">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <noscript>Your browser does not support JavaScript!</noscript>
    </head>
    <body>
        <div id="only-js">
            <header>
                <div id="login-button">Přihlásit</div>
                <a href="registration/" id="registration-button">Registrovat</a>
            </header>
            <main>
                <h1>Litter</h1>
                <div>Sdílení myšlenek nebylo nikdy jednodušší</div>
            </main>
        </div>
    <div id="login-overflow">
        <div id="login-header">Přihlášení</div>
        <div class="login-group">
            <label class="label-icon" id="label-icon-username" for="login-username-input"></label>
            <input class="input" type="text" name="username" id="login-username-input" placeholder="Zadejte uživatelské jméno">
        </div>
        <div class="login-group">
            <label class="label-icon" id="label-icon-password" for="login-password-input"></label>
            <input class="input"type="password" name="password" id="login-password-input" placeholder="Zadejte heslo">
        </div>
        <div class="login-group">
            <input type="checkbox" name="not-robot" id="not-robot-checkbox">
            <label class="label-input" for="not-robot-checkbox">Nejsem robot</label>
        </div>
        <div id="back-button">Zpět</div>
        <div id="login">Přihlásit</div>
    </div>
    </body>
    <script src="js/uvod.js"></script>
</html>