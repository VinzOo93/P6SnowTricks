<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PhotoFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
     $tricks = $manager->getRepository(Trick::class)->findAll();

     for ($i=0;$i<4; $i++ ){
         foreach ($tricks as $trick){
             $media = rand(1,7);
             $photo = new  Photo();
             $photo->setSlug('Trick-Grab-' . $media .  '.jpg')->setFolderId('Trick-Grab-' . 1)->setTrick($trick);
             $manager->persist($photo);
             $manager->flush();
         }
     }


    }

    public static function getGroups(): array
    {
        return ['group3'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TypeFixtures::class,
            TrickFixtures::class
        ];
    }
}