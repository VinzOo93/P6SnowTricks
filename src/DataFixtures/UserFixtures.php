<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    const NAME = ['Admin'];
    const EMAIL = ['admin@gmail.com'];
    const PASSWORD = ['admin93'];

    private UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager)
    {
            $i = 0;
            $user = new User();
            $now = new \DateTime('now');

            $user->setName(self::NAME[$i]);
            $user->setEmail(self::EMAIL[$i]);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, self::PASSWORD[$i]));
            $user->setSignInDate($now);
            $user->setIsVerified(true);

            $manager->persist($user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }

}