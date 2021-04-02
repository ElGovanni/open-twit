<?php

namespace App\Controller;

use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssetsController extends AbstractController
{
    #[Route("/assets/{path}", requirements: ["path" => ".+"], methods: ["GET"])]
    public function readAsset(string $path, FilesystemInterface $filesystem): Response
    {
        if (empty($filesystem->has($path))) {
            throw $this->createNotFoundException();
        }

        $file = $filesystem->read($path);
        return new Response($file, headers: [
            'Content-Type' => $filesystem->getMimetype($path),
        ]);
    }
}
