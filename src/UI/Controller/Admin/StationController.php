<?php

namespace App\UI\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\FindFileByIdQuery;
use App\Application\Query\FindAllStationQuery;
use App\Framework\Controller\BaseController;
use App\Shared\Domain\ValueObject\Page;
use Symfony\Component\HttpFoundation\Request;

class StationController extends BaseController
{
    public function list(Request $request)
    {
        $page = new Page(
            $request->query->get('page', 1),
            10
        );
        $list = $this->ask(new FindAllStationQuery($page));

        return $this->render('admin/station.list.html.twig', array(
            'page' => $page,
            'paginator' => $list
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
