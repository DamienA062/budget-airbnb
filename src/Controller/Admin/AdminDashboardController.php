<?php

namespace App\Controller\Admin;

use App\Service\Stats;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(Stats $stats)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats->getStats(),
            'bestAds' => $stats->getAdsStats('DESC'),
            'worstAds' => $stats->getAdsStats('ASC')
        ]);
    }
}
