<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{

    private function getEntityTrick(): Trick
    {
        return  new Trick();
    }

    private function getEntityComment(): Comment
    {
        return  new Comment();
    }

    private function getEntityUser(): User
    {
        return  new User();
    }

    public function testContent(){
        $comment =  $this->getEntityComment();

        $comment->setContent("lorem ipsum");
        $this->assertEquals("lorem ipsum", $comment->getContent());
    }

    public function testDateAdded(){
        $now = new \DateTime('now');

        $comment =  $this->getEntityComment();
        $comment->setDateAdded($now);

        $this->assertEquals($now, $comment->getDateAdded());

    }

    public function testAuthor(){

        $user = $this->getEntityUser();
        $comment =  $this->getEntityComment();

        $user->setName('Admin');
        $comment->setAuthor($user);
        $this->assertEquals('Admin', $comment->getAuthor()->getName());

    }

    public function testTrick(){

        $comment =  $this->getEntityComment();
        $trick =  $this->getEntityTrick();

        $trick->setName('Toto');
        $comment->setTrick($trick);


        $this->assertEquals($trick, $comment->getTrick());

    }

}