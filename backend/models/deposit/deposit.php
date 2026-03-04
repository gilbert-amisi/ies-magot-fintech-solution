<?php

class Deposit
{
    function addDeposit($amount, $type, $date, $memberId, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO deposit(amount, `type`, date, memberId, cycleId) VALUES (?,?,?,?,?)");
            $query->execute([$amount, $type, $date, $memberId, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}