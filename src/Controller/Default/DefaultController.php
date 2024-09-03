<?php

namespace App\Controller\Cooks;

use App\Service\OpenAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/default', name: 'publicCooks', methods: [Request::METHOD_GET])]
class DefaultController extends AbstractController
{
    public function __construct(protected OpenAiService $openAiService)
    {
    }

    public function __invoke(): JsonResponse
    {
        return $this->json(['success' => true], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
