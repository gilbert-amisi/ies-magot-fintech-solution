<?php

class VSLA
{
    function addVSLA($name, $type, $country, $location, $nominalvalue, $date, $ngoId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO vsla(name, `type`, country, location, nominalvalue, creationDate, ngoId) VALUES (?,?,?,?,?,?,?)");
            $query->execute([$name, $type, $country, $location, $nominalvalue, $date, $ngoId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}