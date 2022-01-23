<?php

namespace App\Tests\Entity;

use App\Entity\Trick;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function getEntityUser(): User
    {
        return  new User();
    }


    private function getEntityTrick(): Trick
    {
        return  new Trick();
    }


    public function testName(){
        $user =  $this->getEntityUser();

        $user->setName('Toto');
        $this->assertEquals('Toto', $user->getName());
    }

    public function testEmail(){
        $user =  $this->getEntityUser();
        $user->setEmail('v.12344@live.fr');
        $this->assertEquals('v.12344@live.fr', $user->getEmail());
    }

    public function testRole(){
        $user =  $this->getEntityUser();
        $user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testPassWord(){
        $user =  $this->getEntityUser();
        $user->setPassword('pa$$word');
        $this->assertEquals('pa$$word', $user->getPassword());
    }

    public function testSignInDate(){
        $now = new \DateTime('now');

        $user =  $this->getEntityUser();
        $user->setSignInDate($now);
        $this->assertEquals($now, $user->getSignInDate());

    }

    public function testTrick(){

        $user =  $this->getEntityUser();
        $trick =  $this->getEntityTrick();
        $trick->setName('TOTO');

        $user->addTrick($trick);
        foreach ($user->getTricks() as $userTrick) {
            $this->assertEquals('TOTO', $userTrick->getName());
        }
    }

}