<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ToDo;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadToDosData implements FixtureInterface
{
    public function load(ObjectManager $om)
    {
        $texts = [
          'Todo 1',
          'Todo 2',
          'Todo 3',
        ];
        foreach ($texts as $text) {
            $toDo = new ToDo();
            $toDo->setValue($text);
            $toDo->setIsDone(false);
            $om->persist($toDo);
        }

        $om->flush();
    }
}
