<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.04.2018
 * Time: 22:24
 */

class Country
{
    public static function getCountries()
    {
        $db = Db::getConnection();
        $query = $db->query("select * from countries");
        $result = $query->fetchAll();
        return $result;
    }

    public static function existCountryById($id)
    {
        $db = Db::getConnection();
        $query = $db->prepare("select count(*) as count from countries where id = :id");
        $query->execute(['id' => $id]);

        $result = $query->fetch();
        $result = $result['count'];

        if ($result > 0) return true;

        return false;
    }
}