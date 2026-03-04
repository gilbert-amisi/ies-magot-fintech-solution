<?php

class Shareout
{
    function addShareout($amount, $date, $memberId, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO shareout(amount, date, memberId, cycleId) VALUES (?,?,?,?)");
            $query->execute([$amount, $date, $memberId, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}