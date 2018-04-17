<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.04.2018
 * Time: 16:32
 */

spl_autoload_register(function ($class) {
    include_once ROOT . '/classes/' . $class . '.php';
});