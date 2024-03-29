<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $load = $request->get('id');

        if ($request->get('load')) {
            if ($load != null) {
                $tricks = $trickRepository->findNextDate($load);
                return new JsonResponse([
                    'content' =>
                        $this->render('trick/_list.html.twig', [
                            'tricks' => $tricks,
                        ])
                ]);
            }
        }

        return $this->render('home/index.html.twig', [
            'tricks' => $trickRepository->findBy([], ['id' => 'DESC'], 20),
        ]);
    }

}
