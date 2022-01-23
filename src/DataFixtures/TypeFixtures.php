<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture implements FixtureGroupInterface
{
    const TYPE = ['Grabs', 'Les Rotations', 'Flips', 'Rotations désaxées ', 'Slides', 'One foot tricks', 'Old school'];

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 7; $i++) {
            $type = new Type();
            $type->setName(self::TYPE[$i]);

            $manager->persist($type);

        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
