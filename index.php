<?php

session_start();

const ROOT = __DIR__;

# Автозагрузка классов
include 'classes/Autoload.php';


$url = Application::getUrl();

switch ($url)
{
    case "registration": include "pages/registration.php"; break;
    case "account": include "pages/account/index.php"; break;
    case "account/logout": include "pages/account/logout.php"; break;
    default : include "pages/login.php"; break;
}

