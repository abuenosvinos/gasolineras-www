<?php

namespace App\UI\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\FindFileByIdQuery;
use App\Application\Query\FindAllStationQuery;
use App\Framework\Controller\BaseController;

class FileController extends BaseController
{
    public function list()
    {
        $list = $this->ask(new FindAllStationQuery());

        return $this->render('admin/file.list.html.twig', array(
            'title' => 'Listado de File',
            'list' => $list
        ));
    }

    public function view($id)
    {
        $item = $this->ask(new FindFileByIdQuery($id));

        return $this->render('admin/file.item.html.twig', array(
            'item' => $item
        ));
    }

    public function delete($id)
    {
        $this->dispatch(new DeleteFileCommand($id));

        return $this->redirectToRoute('admin_file_list');
    }
}
