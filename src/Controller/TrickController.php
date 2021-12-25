<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\TrickType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{


    /**
     * @Route("/new", name="trick_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trick = new Trick();
        $filsystem = new  Filesystem();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $entityManager->getRepository(User::class);
            $now = new \DateTime('now');
            $sysPath = sys_get_temp_dir() . '/upload';
            $fileId = uniqid();
            $pathImage = $this->getParameter('photo_dir') . '/' . $fileId;

            $filePhotos = $trick->getPhotos();
            $fileVideos = $trick->getVideos();
            $name = $trick->getName();

            try {
                if ($entityManager->getRepository('App:Trick')->findOneBy(['name' => $name])) {
                    throw new Exception();
                }
            } catch (Exception $exception) {
                $this->addFlash('alert_Same_Name', 'Ce nom est déjà utilisé');
                return $this->redirectToRoute('trick_new');
            }

            if ($filePhotos) {

                $filsystem->mkdir($sysPath);
                $filsystem->rename($sysPath, $pathImage);

                foreach ($filePhotos as $filePhoto) {
                    $file = $filePhoto->getFile();
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                    $filePhoto->setSlug($newFilename);
                    $filePhoto->setFolderId($fileId);
                    $filePhoto->setTrick($trick);

                    $entityManager->persist($filePhoto);

                    try {
                        $file->move(
                            $pathImage,
                            $newFilename
                        );

                    } catch (fileException $e) {
                        echo('error upload');
                    }
                }
            }

            if ($fileVideos) {
                foreach ($fileVideos as $fileVideo) {

                    $slug = $fileVideo->getSlug();
                    $fileVideo->setSlug($slug);
                    $fileVideo->setTrick($trick);

                    $entityManager->persist($fileVideo);
                }
            }

            $trick->setDescription($form->get('description')->getData());
            $trick->setAuthor($user->find($this->getUser()));
            $trick->setDateAdded($now);

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/show", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->renderForm('trick/edit.html.twig', [
                'trick' => $trick,
                'form' => $form,
            ]);
            foreach ($form->get('photos')->getData() as $photo) {
                dump($photo);
            }
            $now = new \DateTime('now');
            $user = $entityManager->getRepository(User::class);
            $photo = new Photo();

            $filePhotos = $form->get('photos')->getData();

            if ($filePhotos) {
                foreach ($filePhotos as $filePhoto) {

                    $originalFilename = pathinfo($filePhoto->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $filePhoto->guessExtension();
                    try {
                        $filePhoto->move(
                            $this->getParameter('photo_dir'),
                            $newFilename
                        );

                        $trick->addPhotos($photo->setSlug($newFilename));
                    } catch (fileException $e) {
                        dd('error upload');
                    }
                }
            }


            $trick->setDescription($form->get('description')->getData());
            $trick->setAuthor($user->find($this->getUser()));
            $trick->setDateAdded($now);

            $entityManager->persist($trick);
            $entityManager->flush();
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="trick_delete", methods={"POST"})
     */
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $filsystem = new  Filesystem();

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {

            $photos = $trick->getPhotos()->getValues();
            $folderId = $photos[0]->getFolderId();
            $pathImage = $this->getParameter('photo_dir') . '/' . $folderId;

            if ($filsystem->exists($pathImage)) {
                $filsystem->remove($pathImage);
            }

            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
