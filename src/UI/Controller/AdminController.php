<?php

namespace App\UI\Controller;

use App\Framework\Controller\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        return $this->render('admin/home.html.twig');
    }
}
