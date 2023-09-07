<?php

namespace App\UI\Http\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\SearchFilesQuery;
use App\Framework\Controller\BaseController;
use App\UI\Http\ValueObject\Search\Search;
use Symfony\Component\HttpFoundation\Request;

class FileController extends BaseController
{
    public function search(Request $request)
    {
        $search = Search::fromRequest($request);

        $list = $this->ask(new SearchFilesQuery($search));

        return $this->render('admin/file.list.html.twig', array(
            'search' => $search,
            'paginator' => $list
        ));
    }

    public function delete($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->dispatch(new DeleteFileCommand($id));

        return $this->redirectToRoute('admin_file_search');
    }
}
