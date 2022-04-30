<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class DeleteController extends AbstractController
{
    #[Route('/delete/{id}', name: 'app_delete', methods: 'GET')]
    public function index(int $id, PostRepository $repository): Response
    {
        $record = $repository->findOneBy(['id' => $id]);
        if($record) {
            $repository->remove($record);
            return $this->json([
                'message' => 'Record deleted',
            ]);
        }
        return $this->json([
            'message' => 'Record not exist'
        ]);
    }
}
