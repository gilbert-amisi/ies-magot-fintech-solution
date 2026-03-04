<?php

class Donation
{
    function addDonation($donor, $amount, $use, $date, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO donation(donor, amount, objective, date, cycleId) VALUES (?,?,?,?,?)");
            $query->execute([$donor, $amount, $use, $date, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
