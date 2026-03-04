<?php

class Meeting
{
    function addMeeting($topic, $participant, $men, $women, $date, $socialCase, $cycleId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO meeting(topic, participant, men, women, date, socialCase, cycleId) VALUES (?,?,?,?,?,?,?)");
            $query->execute([$topic, $participant, $men, $women, $date, $socialCase, $cycleId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}