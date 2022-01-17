<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    const ADMIN = 1;

    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();


        for ($i=0;$i<40; $i++ ){
            $user = $manager->getRepository(User::class)->find(self::ADMIN);
            foreach ($tricks as $trick){
                $comment = new Comment();
                $now = new \DateTime('now');
                $comment->setTrick($trick)->setContent("Hello World ".$i)->setAuthor($user)->setDateAdded($now);
                $manager->persist($comment);
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