<?php

namespace App\UI\Controller;

use App\Application\Command\ProcessFileCommand;
use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BridgeCronProcessExcelController extends BaseController
{
    public function index(Request $request)
    {
        // Tenemos que aumentar las condiciones de la request
        ini_set('memory_limit','10000M');
        set_time_limit ( 4000 );

        $this->dispatch(new ProcessFileCommand($request->query->get('id')));

        // return new Response(""), if you used NullOutput()
        return new Response('Proceso ejecutado');
    }
}
