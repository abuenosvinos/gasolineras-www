<?php

namespace App\Controller;

use App\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileController extends AbstractController
{
    public function list()
    {
        $list = $this->getDoctrine()->getRepository(File::class)->findAllWithTotalStations();

        return $this->render('admin/file.list.html.twig', array(
            'title' => 'Listado de File',
            'list' => $list
        ));
    }

    public function view($id)
    {
        $item = $this->getDoctrine()->getRepository(File::class)->find($id);

        return $this->render('admin/file.item.html.twig', array(
            'item' => $item
        ));
    }

    public function delete($id)
    {
        $item = $this->getDoctrine()->getRepository(File::class)->find($id);
        if ($item) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();
        }

        return $this->redirectToRoute('admin_file_list');
    }
}
