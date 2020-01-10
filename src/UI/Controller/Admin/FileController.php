<?php

namespace App\UI\Controller\Admin;

use App\Application\Command\DeleteFileCommand;
use App\Application\Query\FindFileByIdQuery;
use App\Application\Query\FindAllFilesQuery;
use App\Framework\Controller\BaseController;
use App\Shared\Domain\ValueObject\Page;
use Symfony\Component\HttpFoundation\Request;

class FileController extends BaseController
{
    public function list(Request $request)
    {
        $page = new Page(
            $request->query->get('page', 1),
            10
        );
        $list = $this->ask(new FindAllFilesQuery($page));

        return $this->render('admin/file.list.html.twig', array(
            'page' => $page,
            'paginator' => $list
        ));
    }

    public function delete($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->dispatch(new DeleteFileCommand($id));

        return $this->redirectToRoute('admin_file_list');
    }
}
