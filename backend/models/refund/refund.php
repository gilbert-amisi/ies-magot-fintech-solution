<?php

class Refund
{
    function addRefund($amount, $date, $loanId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO refund(amount, date, loanId) VALUES (?,?,?)");
            $query->execute([$amount, $date, $loanId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}