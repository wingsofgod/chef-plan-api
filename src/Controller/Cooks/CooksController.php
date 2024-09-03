<?php

namespace App\Controller\Cooks;

use App\Dto\Param\Public\PublicCooksParam;
use App\Service\OpenAiService;
use App\Trait\SerializerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/public/cooks', name: 'publicCooks', methods: [Request::METHOD_POST])]
class CooksController extends AbstractController
{
    use SerializerTrait;
    public function __construct(protected OpenAiService $openAiService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var PublicCooksParam $body */
        $body = $this->deserialize($request->getContent(), PublicCooksParam::class);
        $prompt = $body->personCount . ' kişi için, elimde ' . implode(", ", $body->materials) . ' var hangi yemekleri yapabilirim. Yemekleri resim url ile getirip tarifindeki malzeme oranlarıyla anlatabilir misin? Cevabının formatı bu json tipinde {"cooks": [ {"name": "özet", "equipment": [{"name": "tava"}] "materials": [{"name": "isim", "count": 0, "unit": "kg"}] "image": "url", "specification": ["step": 1, "description": "tarif"]}]}} olsun';
        return $this->json($this->openAiService->aiAssistant($prompt), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
