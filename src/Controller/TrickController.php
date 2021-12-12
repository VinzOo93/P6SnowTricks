<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $now = new \DateTime('now');
            $user = $entityManager->getRepository(User::class);
            $photo = new Photo();

            $filePhoto = $form->get('photos')->getData();
            if ($filePhoto) {

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

            $now = new \DateTime('now');
            $user = $entityManager->getRepository(User::class);
            $photo = new Photo();

            $filePhoto = $form->get('photos')->getData();
            if ($filePhoto) {

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

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {

            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
