<?php

namespace App\UI\Http\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\FindFileByIdQuery;
use App\Application\Query\SearchStationQuery;
use App\Framework\Controller\BaseController;
use App\UI\Http\ValueObject\Search\Search;
use Symfony\Component\HttpFoundation\Request;

class StationController extends BaseController
{
    public function search(Request $request)
    {
        $search = Search::fromRequest($request);

        $list = $this->ask(new SearchStationQuery($search));

        return $this->render('admin/station.list.html.twig', array(
            'search' => $search,
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

        return $this->redirectToRoute('admin_file_search');
    }
}
