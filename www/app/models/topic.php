<?php
namespace App\Models;

use App\Core\Model;

class Topic extends Model
{
    protected $table = 'topics';
    
    public function getTopics()
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' ;');
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }


}