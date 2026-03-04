<?php

class Spending
{
    function addSpending($date, $amount, $use, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO spending(date, amount, objective, cycleId) VALUES (?,?,?,?)");
            $query->execute([$date, $amount, $use, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}