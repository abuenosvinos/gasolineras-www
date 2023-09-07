<?php

namespace App\UI\Http\Controller\Admin;

use App\Framework\Controller\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        return $this->render('admin/home.html.twig', [
            'num_stations_active' => 66,
            'users' => 77
        ]);
    }

    public function bridge()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/bridge.html.twig', [
            'num_stations_active' => 66,
            'users' => 77
        ]);
    }
}
