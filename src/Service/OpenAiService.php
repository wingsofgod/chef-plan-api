<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAiService
{
    const OPEN_AI_API_URL = 'https://api.openai.com/v1/';

    public function __construct(protected ContainerInterface  $container,
                                protected HttpClientInterface $client
    )
    {
    }

    public function aiAssistant(string $prompt) {
        $messages = [
            [
                "role" => "user",
                "content" => $prompt
            ]
        ];

        $response = $this->client->request(
            'POST',
            self::OPEN_AI_API_URL . 'chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->container->getParameter('open_ai_api_key')
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.0,
                    'messages' => $messages
                ]
            ]
        );
        if ($response->getStatusCode() === 200) {
            $content = $response->toArray()['choices'][0]['message']['content'];
            $pattern = '/```json(.*?)```/s';
            preg_match($pattern, $content, $result);
            return json_decode($result[1], true);
        }
        return [];
    }
}