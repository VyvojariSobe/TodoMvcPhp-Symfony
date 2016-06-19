<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ToDo;

class LoadToDosData implements FixtureInterface
{
    public function load(ObjectManager $om)
    {
        $texts = file_get_contents('http://loripsum.net/api/10/short/plaintext');
        $texts = array_filter(explode("\n", $texts));

        foreach ($texts as $text) {
            $toDo = new ToDo();
            $toDo->setValue($text)
                ->setIsDone((bool) random_int(0, 1));
            $om->persist($toDo);
        }

        $om->flush();
    }
}
