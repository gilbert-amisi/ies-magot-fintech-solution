<?php

class Cycle
{
    function addCycle($name, $start, $end, $vslaId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO cycle(name, `start`, `end`, vslaId) VALUES (?,?,?,?)");
            $query->execute([$name, $start, $end, $vslaId]);
            return true;
        } catch (Exeception $th) {
            return false;
        }
    }

    function changeStatus($status, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("UPDATE cycle SET `status` = ? WHERE id = ?");
            $query->execute([$status, $cycleId]);
            return true;
        } catch (Exeception $th) {
            return false;
        }
    }
}