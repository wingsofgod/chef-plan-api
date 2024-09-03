<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'api:createcook',
    description: 'Add a short description for your command',
)]
class CreateCookCommand extends Command
{
    const OPEN_AI_API_URL = 'https://api.openai.com/v1/';

    public function __construct(
        protected HttpClientInterface $client
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $result = [];
        $prompt = 'Elimde tavuk salça biber soğan patlıcan var hangi yemekleri yapabilirim. Yemekleri resim url ile getirip tarifindeki malzeme oranlarıyla anlatabilir misin? Cevabının formatı bu json tipinde {"cooks": [ {"name": "özet", "equipment": [{"name": "tava"}] "materials": [{"name": "isim", "count": 0, "unit": "kg"}] "image": "url", "specification": ["step": 1, "description": "tarif"]}]}} olsun';
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
                    'Authorization' => 'Bearer ' . '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'
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
            print_r(json_decode($result[1], true));
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
            return Command::SUCCESS;
        }

        $io->error('open ai assessment failed');
        return Command::FAILURE;
    }
}
