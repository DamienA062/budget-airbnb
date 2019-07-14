<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $adRepository, UserRepository $userRepository)
    {
        return $this->render('home/homepage.html.twig', [
            'ads' => $adRepository->findBestAds(3),
            'users' => $userRepository->findBestUsers(2)
        ]);
    }
}
