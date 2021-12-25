<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    const ADMIN = 1;
    const GRAB = 1;
    const DESCRIPT_GRAB = 'Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. » Un grab est d\'autant plus réussi que la saisie est longue. De plus, le saut est d\'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d\'accentuer la torsion de son corps grâce à la tension de sa main sur la planche. On dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).';


    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 7; $i++) {
            $trick = new Trick();
            $now = new \DateTime('now');
            $photo = new  Photo();
            $video = new  Video();
            $media = $i + 1;

            $user = $manager->getRepository(User::class)->find(self::ADMIN);
            $trick->setType($manager->getRepository(Type::class)->find(self::GRAB));

            $trick->addPhotos($photo->setSlug('Trick-Grab-' . $media.  '.jpg')->setFolderId('Trick-Grab-' . $media));
            $manager->persist($photo);

            $trick->addVideos($video->setSlug('https://www.youtube.com/embed/UNItNopAeDU'));
            $manager->persist($video);

            $trick->setAuthor($user);
            $trick->setDescription(self::DESCRIPT_GRAB);
            $trick->setName('Trick Grab' . + $media);
            $trick->setDateAdded($now);

            $manager->persist($trick);
        }
        $manager->flush();

    }


    public static function getGroups(): array
    {
        return ['group2'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TypeFixtures::class,
        ];
    }
}