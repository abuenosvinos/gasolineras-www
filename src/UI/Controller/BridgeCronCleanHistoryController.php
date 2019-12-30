<?php

namespace App\UI\Controller;

use App\Application\Command\CleanHistoryCommand;
use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class BridgeCronCleanHistoryController extends BaseController
{
    public function index()
    {
        // Tenemos que aumentar las condiciones de la request
        ini_set('memory_limit','3072M');
        set_time_limit ( 1200 );

        $this->dispatch(new CleanHistoryCommand());

        // return new Response(""), if you used NullOutput()
        return new Response('Proceso ejecutado');
    }
}
