<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();

        for ($i=0;$i<4; $i++ ){
            foreach ($tricks as $trick){
                $video = new  Video();
                $trick->addVideos($video->setSlug('https://www.youtube.com/embed/UNItNopAeDU'));
                $manager->persist($video);
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