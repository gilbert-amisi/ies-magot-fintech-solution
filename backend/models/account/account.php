<?php

class Account
{
    function addAccount($number, $type, $vslaId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO account(number, `type`, vslaId) VALUES (?,?,?)");
            $query->execute([$number, $type, $vslaId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}