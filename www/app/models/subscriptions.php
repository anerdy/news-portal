<?php
namespace App\Models;

use App\Core\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    
    public function getSubscriptionsByTopic($topicId)
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