<?php

class NGO
{
    function addNGO($name, $logo, $website)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO ngo(name, logo, website) VALUES (?,?,?)");
            $query->execute([$name, $logo, $website]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    function addNGOWithoutLogo($name, $website)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO ngo(name, website) VALUES (?,?)");
            $query->execute([$name, $website]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}