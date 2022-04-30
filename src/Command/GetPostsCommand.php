<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\PostRepository;
use App\Entity\Post;

#[AsCommand(
    name: 'app:get-posts',
    description: 'Get posts from remote server.',
)]
class GetPostsCommand extends Command
{
    const URL = 'https://jsonplaceholder.typicode.com';
    protected HttpClientInterface $client;
    protected PostRepository $repository;

    public function __construct(HttpClientInterface $client, PostRepository $repository)
    {
        $this->client = $client;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $posts = $this->client->request('GET', self::URL . '/posts');
        $users = $this->client->request('GET', self::URL . '/users');

        $posts = $posts->toArray();
        $users = $users->toArray();

        $count = 0;
        foreach($posts as $post){
            if($this->check_if_exist($post['id'])){
                continue;
            }
            $record = new Post();
            $record->setTitle($post['title']);
            $record->setBody($post['body']);
            foreach($users as $user){
                if($user['id'] == $post['userId']){
                    $record->setName($user['name']);
                    break;
                }
            }
            $record->setExternalId($post['id']);
            $this->repository->add($record);
            $count++;
        }
        $io->success('Dodano ' . $count . ' recordów.');

        return Command::SUCCESS;
    }

    protected function check_if_exist($id)
    {
        $result = $this->repository->findOneBy(['external_id'=> $id]);
        if($result)
            return true;
        return false;
    }
}

