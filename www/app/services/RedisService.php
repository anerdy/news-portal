<?php
namespace App\Services;

use App\Core\Controller;
use App\Models\Post;
use App\Models\User;
use App\Core\View;
use \Exception;
use Predis\Client as RedisClient;


class RedisService
{
    private $client;

    function __construct()
    {
        $this->client = new RedisClient([
            'host' => redis,
            "password" => "testpass"
        ]);
    }

    public function setNews($news, $topicId)
    {
        try {
            $this->client->set('news_topic_'.$topicId, serialize($news));
            $this->client->expire('news_topic_'.$topicId, 60);
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
    }

    public function getNews($topicId): array
    {
        try {
            $news = $this->client->get('news_topic_'.$topicId);
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
        if (is_null($news)) {
            $news = [];
        } else {
            $news = unserialize($news);
        }

        return $news;
    }

    public function setTopics($topics)
    {
        try {
            $this->client->set('topics', serialize($topics));
            $this->client->expire('news_topic_'.$topicId, 10000);
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
    }

    public function getTopics(): array
    {
        try {
            $topics = $this->client->get('topics');
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
        if (is_null($topics)) {
            $topics = [];
        } else {
            $topics = unserialize($topics);
        }

        return $topics;
    }

    public function setWsFlag($flag)
    {
        try {
            $this->client->set('ws_flag', $flag);
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
    }
    
    public function getWsFlag(): int
    {
        try {
            $flag = $this->client->get('ws_flag');
        } catch (Exception $e) {
            die('Ошибка Redis: '.$e->getMessage());
        }
        if (is_null($flag)) {
            $flag = 0;
        } else {
           // $flag = 1;
        }

        return $flag;
    }

}
