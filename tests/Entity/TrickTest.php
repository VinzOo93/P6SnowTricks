<?php


namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Video;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TrickTest extends TestCase
{
    private function getEntityTrick(): Trick
    {
        return  new Trick();
    }

    private function getEntityType(): Type
    {
        return  new Type();
    }

    private function getEntityPhoto(): Photo
    {
        return  new Photo();
    }

    private function getEntityVideo(): Video
    {
        return  new Video();
    }

    private function getEntityComment(): Comment
    {
        return  new Comment();
    }

    private function getEntityUser(): User
    {
        return  new User();
    }

    public function testName(){
      $trick =  $this->getEntityTrick();

      $trick->setName('Toto');
      $this->assertEquals('Toto', $trick->getName());
    }

    public function testDescription(){
        $trick =  $this->getEntityTrick();

        $trick->setDescription('Toto');
        $this->assertEquals('Toto', $trick->getDescription());
    }

    public function testType(){
        $trick =  $this->getEntityTrick();
        $type = $this->getEntityType();

        $type->setName('Snow-board');
        $trick->setType($type);

        $this->assertEquals($type, $trick->getType());
    }

    public function testPhoto(){

        $request = new Request();
        $fileName = "scroll-arrow.png";

        $pathImage = $request->get('photo_dir') . '/' . $fileName;

        $trick =  $this->getEntityTrick();
        $photo = $this->getEntityPhoto();
        $photo->setFile($pathImage);
        $trick->addPhotos($photo);

        foreach ($trick->getPhotos() as $photoFile){
            $this->assertEquals($pathImage, $photoFile->getFile());
        }
    }

    public function testVideo(){

        $videoSlug = 'https://www.youtube.com/embed/UNItNopAeDU';

        $trick =  $this->getEntityTrick();
        $video = $this->getEntityVideo();
        $video->setSlug($videoSlug);

        $trick->addVideos($video);

        foreach ($trick->getVideos() as $videoUrl){
            $this->assertEquals($videoSlug, $videoUrl->getSlug());
        }
    }

    public  function  testComment(){

        $commentString = "Hello World";

        $trick =  $this->getEntityTrick();
        $comment = $this->getEntityComment();

        $comment->setContent($commentString);
        $trick->addComment($comment);

        foreach ($trick->getComments() as $commentContent){
            $this->assertEquals("Hello World", $commentContent->getContent());
        }
    }

    public function testDateAdded(){
        $now = new \DateTime('now');

        $trick =  $this->getEntityTrick();
        $trick->setDateAdded($now);

        $this->assertEquals($now, $trick->getDateAdded());

    }

    public function testAuthor(){

        $user = $this->getEntityUser();
        $trick =  $this->getEntityTrick();

        $user->setName('Admin');

        $trick->setAuthor($user);

        $this->assertEquals('Admin', $trick->getAuthor()->getName());

    }

    public function testDateUpdated(){
        $now = new \DateTime('now');

        $trick =  $this->getEntityTrick();
        $trick->setDateUpdated($now);

        $this->assertEquals($now, $trick->getDateUpdated());

    }

}