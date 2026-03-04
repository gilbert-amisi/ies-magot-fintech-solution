<?php

class User
{
    function addUser($username, $email, $phone, $type)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO user(username,email, phone, type) VALUES (?,?,?,?)");
            $query->execute([$username, $email, $phone, $type]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    function addUserWithoutEmail($username, $phone, $type)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO user(username, phone, type) VALUES (?,?,?)");
            $query->execute([$username, $phone, $type]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}