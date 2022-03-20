<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Topic;
use App\Models\News;
use App\Core\View;
use \Exception;
use App\Services\RabbitMQService;
use App\Services\RedisService;
use \GuzzleHttp\Client as GuzzleClient;

class Controller_News extends Controller
{
    private $rabbitMQService;

    function __construct()
    {
        $this->user = new User();
        $this->view = new View();
        $this->topic = new Topic();
        $this->news = new News();
        $this->rabbitMQService = new RabbitMQService();
        $this->redisService = new RedisService();
    }

    public function action_index()
    {
       // $this->redisService->setWsFlag(0);
        $currentUser = [];
        if (!isset($_COOKIE['auth'])) {
            header("Location: /?message=Вы не авторизованы!");
            die();
        } else {
            $currentUser = $this->user->getCurrentUser();
        }

        /*
        $posts = $this->rabbitMQService->getPosts($userId);
        if (!empty($posts)) {
            $redisPosts = $this->redisService->getPosts($userId);
            $posts = array_merge($posts, $redisPosts);
            $this->redisService->setPosts($posts, $userId);
        } else {
            $posts = $this->redisService->getPosts($userId);
        }*/
        
        $topics = $this->redisService->getTopics();
        if (empty($topics)) {
            $topics = $this->topic->getTopics();
            $this->redisService->setTopics($topics);
        }

        if (isset($GLOBALS['GET_PARAMS']['topic']) && !empty($GLOBALS['GET_PARAMS']['topic'])) {
            $topicId = (int)$GLOBALS['GET_PARAMS']['topic'];
        } else {
            $topicId = 1;
        }
        
     //   $news = $this->redisService->getNews($topicId);
       // if (empty($news)) {
            $news = $this->news->getNewsByTopicId($topicId);
         //   $this->redisService->setNews($news, $topicId);
       // }

        $this->view->generate('news/feed.php', 'template_view.php', ['currentUser' => $currentUser, 'currentTopicId' => $topicId, 'topics' => $topics, 'news' => $news] );
    }

    public function action_add()
    {
        $currentUser = [];
        if (!isset($_COOKIE['auth'])) {
            header("Location: /?message=Вы не авторизованы!");
            die();
        } else {
            $currentUser = $this->user->getCurrentUser();
        }
        $topics = $this->redisService->getTopics();
        if (empty($topics)) {
            $topics = $this->topic->getTopics();
            $this->redisService->setTopics($topics);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['text'])) {
                $topic = $_POST['topic'];
                $title = $_POST['title'];
                $text = $_POST['text'];
                $this->news->addNews($topic, $currentUser['id'], $title, $text);
                
                $this->rabbitMQService->addNews($topic, $title, $text);
                
                header("Location: /?message=Новость добавлена!");
                die();
            }

            header("Location: /?message=Новость не добавлена!");
            die();
        }

        $this->view->generate('news/add.php', 'template_view.php', ['topics' => $topics]);
    }
}