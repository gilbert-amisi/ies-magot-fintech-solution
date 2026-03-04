<?php

class Creditrequest
{
    function addCreditrequest($amount, $date, $use, $memberId, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO creditrequest(amount, date, objective, memberId, cycleId) VALUES (?,?,?,?,?)");
            $query->execute([$amount, $date, $use, $memberId, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    function updateStatus($status, $creditId)
    {
        try {
            $query = MagoConnection::connect()->prepare("UPDATE creditrequest SET `status` = ? WHERE id = ?");
            $query->execute([$status, $creditId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}