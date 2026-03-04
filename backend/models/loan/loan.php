<?php

class Loan
{
    function addLoan($amount, $start, $rate, $method, $end, $creditId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO loan(amount, datestart, rate, method, dateend, creditId) VALUES (?,?,?,?,?,?)");
            $query->execute([$amount, $start, $rate, $method, $end, $creditId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}