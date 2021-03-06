<?php

namespace App\UI\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\FindFileByIdQuery;
use App\Application\Query\FindAllStationQuery;
use App\Framework\Controller\BaseController;

class UserController extends BaseController
{
    public function profile()
    {
        $list = $this->ask(new FindAllStationQuery());

        return $this->render('admin/file.list.html.twig', array(
            'list' => $list
        ));
    }

    public function list()
    {
        $list = $this->ask(new FindAllStationQuery());

        return $this->render('admin/file.list.html.twig', array(
            'list' => $list
        ));
    }

    public function view($id)
    {
        $item = $this->ask(new FindFileByIdQuery($id));

        return $this->render(
            'station.list.twig', array(
            'item' => $item
        ));
    }

    public function delete($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->dispatch(new DeleteFileCommand($id));

        return $this->redirectToRoute('admin_file_list');
    }
}
