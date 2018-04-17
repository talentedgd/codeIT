<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.04.2018
 * Time: 22:24
 */

class Application
{
    /**
     * Возвращает URL
     *
     * @return string
     */
    public static function getUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = trim($url, '/');

        $dirUrl = self::getDirUrl();
        # Удаляем директорию из url
        $url = str_replace($dirUrl, '', $url);
        $url = trim($url, '/');

        return $url;
    }

    /**
     *  Возвращает url директорию проекта
     *
     * @return string
     */
    public static function getDirUrl()
    {
        $file = explode('/', $_SERVER['PHP_SELF']);
        $file = end($file);
        $path = str_replace($file, '', $_SERVER['PHP_SELF']);
        $path = trim($path, '/');
        
        return $path;
    }
}