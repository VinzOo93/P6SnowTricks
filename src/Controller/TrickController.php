<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\TrickType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $filsystem = new  Filesystem();
        $filesField = $trick->getPhotos()->getValues();
        $pathImage = $this->getParameter('photo_dir') . '/';
        $now = new \DateTime('now');

        foreach ($filesField as $fileField) {

            $fileField->setFile(new UploadedFile($pathImage . $fileField->getFolderId() . '/' . $fileField->getSlug(), $fileField->getSlug()));
        }
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nameForm = $form->get('name')->getData();
            $filePhotos = $trick->getPhotos();
            $fileVideos = $trick->getVideos();
            $name = $trick->getName();

            try {
                if ($nameForm != $name) {
                    if ($entityManager->getRepository('App:Trick')->findOneBy(['name' => $name])) {
                        throw new Exception();
                    }
                }
            } catch (Exception $exception) {
                $this->addFlash('alert_Same_Name', 'Ce nom est déjà utilisé');
                return $this->redirectToRoute('trick_edit');
            }

            if ($filePhotos) {

                $photosBase = $trick->getPhotos()->getValues();
                $folderId = $photosBase[0]->getFolderId();

                if ($filsystem->exists($pathImage)) {

                    foreach ($filePhotos as $filePhoto) {

                        if ($folderId == null) {
                            $folderId = uniqid();
                        }

                        $pathImage = $this->getParameter('photo_dir') . '/' . $folderId;

                        $file = $filePhoto->getFile();
                        if ($file != null) {
                            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                            if ($filePhoto->getId()) {
                                $filsystem->remove($pathImage . '/' . $filePhoto->getSlug());
                            }
                            if ($filePhoto->getFolderId() == null) {
                                $filePhoto->setFolderId($folderId);
                            };
                            $filePhoto->setSlug($newFilename);
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
                };
            }

            if ($fileVideos) {
                foreach ($fileVideos as $fileVideo) {

                    $slug = $fileVideo->getSlug();
                    $fileVideo->setSlug($slug);
                    $fileVideo->setTrick($trick);

                    $entityManager->persist($fileVideo);
                }
            }
            $trick->setDateUpdated($now);
            $trick->setDescription($form->get('description')->getData());
            $entityManager->persist($trick);
            $entityManager->flush();


            return $this->redirectToRoute('trick_edit', [
                'id' => $trick->getId(),
                'trick' => $trick,
                'form' => $form
            ],
                Response::HTTP_SEE_OTHER);

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

            if ($photos != null) {

                $folderId = $photos[0]->getFolderId();

                $pathImage = $this->getParameter('photo_dir') . '/' . $folderId;

                if ($filsystem->exists($pathImage)) {
                    $filsystem->remove($pathImage);
                }
            }


            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete-photo", name="photo_delete", methods={"DELETE"})
     */
    public function deletePhoto(Request $request, Photo $photo, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $filsystem = new  Filesystem();

        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete-photo' . $photo->getId(), $data['_token'])) {

            $photos = $entityManager->getRepository('App:Photo')->findBy(['trick' => $photo->getTrick()]);
            try {
                if (count($photos) === 1) {
                    throw new Exception();
                } else {
                    $folderId = $photo->getFolderId();
                    $slug = $photo->getSlug();
                    $trick = $photo->getTrick();
                    $now = new \DateTime('now');
                    $pathImage = $this->getParameter('photo_dir') . '/' . $folderId . '/' . $slug;

                    if ($filsystem->exists($pathImage)) {
                        $filsystem->remove($pathImage);
                    }
                    $trick->setDateUpdated($now);

                    $entityManager->remove($photo);
                    $entityManager->persist($trick);
                    $entityManager->flush();


                    return new JsonResponse(['success' => 1]);
                }

            } catch (Exception $exception) {
                $this->addFlash('alert-last-image', 'il ne reste plus qu\'une photo !!');
                return new JsonResponse(['error' => 'Token Invalide'], 301);
            }
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/{id}/delete-video", name="video_delete", methods={"DELETE"})
     */
    public function deleteVideo(Request $request, Video $video, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete-video' . $video->getId(), $data['_token'])) {

            $trick = $video->getTrick();
            $now = new \DateTime('now');
            $trick->setDateUpdated($now);
            $entityManager->remove($video);
            $entityManager->persist($trick);
            $entityManager->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
