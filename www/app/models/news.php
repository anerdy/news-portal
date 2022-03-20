<?php
namespace App\Models;

use App\Core\Model;

class News extends Model
{
    protected $table = 'news';
    
    public function addNews($topicId, $authorId, $title, $text)
    {
        try {
            $result = $this->connection->prepare('INSERT INTO news (topic_id, author_id, title, text) values (:topic_id, :author_id, :title, :text );');
            $result->bindParam(':topic_id', $topicId, \PDO::PARAM_INT);
            $result->bindParam(':author_id', $authorId, \PDO::PARAM_INT);
            $result->bindParam(':title', $title);
            $result->bindParam(':text', $text);
            $result->execute();
        } catch (\Exception $exception) {
            header("Location: /");
            die();
        }
    }


    public function getNewsByTopicId($topicId)
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE `topic_id` = :topic_id ;');
        $result->bindParam(':topic_id', $topicId, \PDO::PARAM_INT);
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

}