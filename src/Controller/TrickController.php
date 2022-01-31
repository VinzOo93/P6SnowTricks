<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
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
            $name = $form->get('name')->getData();

            try {
                if ($entityManager->getRepository('App:Trick')->findOneBy(['name' => $name])) {
                    throw new Exception();
                } else {
                    try {
                        if (count($filePhotos) == 0 || count($fileVideos) == 0) {
                            throw new Exception();
                        } else {
                            $filsystem->mkdir($sysPath);
                            $filsystem->rename($sysPath, $pathImage);

                            foreach ($filePhotos as $filePhoto) {

                                $file = $filePhoto->getFile();
                                if ($file != null) {

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
                                        $this->addFlash('alert_trick', 'erreur enregistrement fichier');
                                        return $this->redirectToRoute('trick_new');
                                    }
                                }
                            }
                            foreach ($fileVideos as $fileVideo) {

                                $slug = $fileVideo->getSlug();
                                $watch = 'watch?v=';
                                $embed = 'embed/';
                                $newSlug = '';

                                if (str_contains($slug, $watch)) {
                                    $newSlug = str_replace($watch, $embed, $slug);
                                }

                                $fileVideo->setSlug($newSlug);
                                $fileVideo->setTrick($trick);

                                $entityManager->persist($fileVideo);
                            }
                            $trick->setAuthor($user->find($this->getUser()));
                            $trick->setDateAdded($now);
                            $trick->setSlug($slugger->slug($trick->getName()));
                            $entityManager->persist($trick);
                            $entityManager->flush();

                            $this->addFlash('success', 'Bravo, votre trick est bien enregistré ☑️ ! ');
                            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
                        }
                    } catch (Exception $exception) {
                        $this->addFlash('alert_trick', 'Un trick doit posséder au minimum une Photo et une Video');
                        return $this->redirectToRoute('trick_new');
                    }
                }
            } catch (Exception $exception) {
                $this->addFlash('alert_trick', 'Ce nom est déjà utilisé');
                return $this->redirectToRoute('trick_new');
            }

        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{slug}/show", name="trick_show", methods={"GET","POST"})
     */
    public function show(CommentRepository $commentRepository, Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $comId = $request->get('comId');
        $trickId = strval($trick->getId());

        if ($request->get('loadCom')) {
            if ($comId && $trickId) {
                $comments = $commentRepository->findComments($trickId, $comId);

                return new  JsonResponse([
                    'content' => $this->renderView('comment/_comment.html.twig', [
                        'comments' => $comments,
                    ])
                ]);
            }
        }
        $commentShow = $commentRepository->findBy(['trick' => $trick->getId()],['dateAdded'=> 'DESC']);
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_USER') && $comment->getContent() != null) {
            $content = $comment->getContent();
            $now = new \DateTime('now');
            $user = $entityManager->getRepository(User::class);

            $comment->setContent($content);
            $comment->setDateAdded($now);
            $comment->setAuthor($user->find($this->getUser()));
            $comment->setTrick($trick);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug(),
                'trick' => $trick,
                'form' => $form,
                'comments' => $commentShow
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $commentShow,
            'form' => $form->createView(),
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

            $filePhotos = $trick->getPhotos();
            $fileVideos = $trick->getVideos();
            $name = $form->get('name')->getData();

            try {
                $name = $entityManager->getRepository('App:Trick')->findOneBy(['name' => $name]);
                if ($name == true && $name->getId() != $trick->getId()) {
                    throw new Exception();
                } else {
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
                                        $file->move(
                                            $pathImage,
                                            $newFilename
                                        );
                                }
                            }
                        };
                    }

                    if ($fileVideos) {
                        foreach ($fileVideos as $fileVideo) {

                            $slug = $fileVideo->getSlug();
                            $watch = 'watch?v=';
                            $embed = 'embed/';

                            if (str_contains($slug, $watch)) {
                                $newSlug = str_replace($watch, $embed, $slug);
                                $fileVideo->setSlug($newSlug);

                            } else {
                                $fileVideo->setSlug($slug);
                            }
                            $entityManager->persist($fileVideo);
                            $fileVideo->setTrick($trick);
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

            } catch (Exception $exception) {
                $this->addFlash('alert_trick', 'Ce nom est déjà utilisé');
                return $this->redirectToRoute('trick_edit', [
                    'id' => $trick->getId(),
                    'trick' => $trick,
                    'form' => $form
                ],
                    Response::HTTP_SEE_OTHER);
            }
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
        $this->addFlash('success', 'Bravo, votre trick est bien éffacé ☑️ ! ');
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
                $this->addFlash('alert-last-photo', 'il ne reste plus qu\'une photo !!');
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

            $videos = $entityManager->getRepository('App:Video')->findBy(['trick' => $video->getTrick()]);

            try {
                if (count($videos) === 1) {
                    throw new Exception();
                } else {
                    $trick = $video->getTrick();
                    $now = new \DateTime('now');
                    $trick->setDateUpdated($now);
                    $entityManager->remove($video);
                    $entityManager->persist($trick);
                    $entityManager->flush();
                }
            } catch (Exception $exception) {
                $this->addFlash('alert-last-video', 'il ne reste plus qu\'une video !!');
                return new JsonResponse(['error' => 'Token Invalide'], 301);
            }
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
