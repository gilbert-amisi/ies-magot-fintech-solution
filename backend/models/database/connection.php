<?php

class MagoConnection
{
    static function connect()
    {
        ini_set('mysql.connect_timeout', 300);
        ini_set('default_socket_timeout', 300);

        $db = new PDO('mysql:host=localhost;dbname=mago', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('set names utf8');
        return $db;
    }
}