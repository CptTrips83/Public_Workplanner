<?php

namespace App\Tools;

use App\Entity\EntryChange;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Toolkit
{
    /* Fügt die Datumangabe aus $date mit der Zeitangabe aus $time 
        zusammen und gibt den Wert als Unix-Timestamp zurück.
    */
    public static function mergeDateTime(int $date, int $time) : int
    {
        return strtotime(date("Y-m-d", $date)." ".date("H:i:s", $time));
    }

    public static function checkChange(string $entity, string $feldName, mixed $oldValue, mixed $newValue, int $workEntryId, int $userId, EntityManagerInterface $entityManager)
    {
        if($oldValue != $newValue)
            {
                $change = new EntryChange();
                $change->setFeldName($feldName);
                $change->setAlterWert($oldValue);
                $change->setNeuerWert($newValue);
                $change->setDatum(new \DateTime());
                $change->setEntryId($userId);
                $change->setEntity($entity);
                $change->setUserId($userId);

                $entityManager->persist($change);
                $entityManager->flush();
            }
    }
}

?>