<?php

namespace App\UI\Controller;

use App\Application\Command\GetDataCommand;
use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class BridgeCronDownloadExcelController extends BaseController
{
    public function index()
    {
        // Tenemos que aumentar las condiciones de la request
        ini_set('memory_limit','10000M');
        set_time_limit ( 4000 );

        $this->dispatch(new GetDataCommand());

        // return new Response(""), if you used NullOutput()
        return new Response('Proceso ejecutado');
    }
}
