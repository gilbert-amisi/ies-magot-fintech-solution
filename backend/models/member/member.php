<?php

class Member
{
    function addMember($name, $gender, $age, $education, $activity, $position, $marginalization, $phone, $operateur, $userId, $vslaId)
    {
        try {
            $query = MagoConnection::connect()->prepare("INSERT INTO member(name, gender, age, educationLevel, mainactivity, `position`, marginalization, phone, operateur, userId, vslaId) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $query->execute([$name, $gender, $age, $education, $activity, $position, $marginalization, $phone, $operateur, $userId, $vslaId]);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}