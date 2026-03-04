<?php

class Staff
{
    function Addstaff($name, $email, $position, $phone, $ngoId, $userId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO staff(name, email, `position`,phone, ngoId, userId) VALUES (?,?,?,?,?,?)");
            $query->execute([$name, $email, $position, $phone, $ngoId, $userId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}