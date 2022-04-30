<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Symfony\Component\Serializer\SerializerInterface;

class ListaController extends AbstractController
{
    #[Route('/lista', name: 'app_posts')]
    public function index(PostRepository $repository, SerializerInterface $serializer): Response
    {
        $records = $repository->findAll();
        $posts = $serializer->normalize($records);
        return $this->render('lista/index.html.twig', [
            'controller_name' => 'ListaController',
            'records' => $records
        ]);
    }
}
