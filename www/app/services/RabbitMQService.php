<?php
namespace App\Services;

use App\Core\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Core\View;
use \Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use App\Services\RedisService;


class RabbitMQService
{
    public $subscriptions;
    
    function __construct()
    {
    }

    public function addNews(int $topicId, string $title, string $text)
    {
        try {
            $connection = new AMQPStreamConnection(
                rabbitmq,	    #host - имя хоста, на котором запущен сервер RabbitMQ
                5672,       	#port - номер порта сервиса, по умолчанию - 5672
                'guest',    	#user - имя пользователя для соединения с сервером
                'guest'     	#password
            );
            $channel = $connection->channel();
            $exchange = 'news_exchange'; //.$topicId;
    
            # Create the exchange if it doesnt exist already.
            $channel->exchange_declare(
                $exchange, 
                'fanout', # type
                false,    # passive
                false,    # durable
                false     # auto_delete
            );
    
          //  $subscriptions = new Subscription();
        //    $subscriptions = $subscriptions->getSubscriptionsByTopic($topicId);
    
         //   foreach($subscriptions as $subscription) {
             //   $queueName = 'subscription'.$subscription['user_id'];
                $queueName = 'subscription'.$topicId;
                /** @var $channel AMQPChannel */
                $channel->queue_declare(
                    $queueName,	#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
                    false,      	#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
                    false,      	#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
                    false,      	#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
                    false       	#autodelete - очередь удаляется, когда отписывается последний подписчик
                );
                $channel->queue_bind($queueName, $exchange);
    
                $message = serialize(['title' => $title, 'text' => $text]);
                $msg = new AMQPMessage($message, ['delivery_mode' => 2]);
                $channel->basic_publish(
                    $msg,       	#message
                    '',         	#exchange
                    $queueName 	#routing key
                );
         //   }
    
            $channel->close();
            $connection->close();

        }  catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function consumeNews($topicId, $userId, $WSconnection, $redisService)
    {
        $exchange = 'news_exchange'; //.$topicId;
        $queue_name = 'subscription'.$topicId;
        $connection = new AMQPStreamConnection(
            '127.0.0.1',	#host
            5672,       	#port
            'guest',    	#user
            'guest'     	#password
        );

        $channel = $connection->channel();
        $messages = [];

        try {
            # Create the exchange if it doesnt exist already.
            $channel->exchange_declare(
                $exchange, 
                'fanout', # type
                false,    # passive
                false,    # durable
                false     # auto_delete
            );

            $channel->queue_declare(
                $queue_name,    # queue
                false, # passive
                false, # durable
                false,  # exclusive
                false  # auto delete
            );

            $channel->queue_bind($queue_name, $exchange);
            print 'Waiting for logs. To exit press CTRL+C' . PHP_EOL;

            $callback = function($msg) use ($WSconnection) {
                $msgData = unserialize($msg->body);
                $WSconnection->send(json_encode($msgData));
                print "Read: " . json_encode($msgData) . PHP_EOL;
                $msg->ack();
            };

            $channel->basic_consume(
                $queue_name, 
                $exchange, 
                false, 
                false, 
                false, 
                false, 
                $callback
            );

            while ($channel->is_open())
            {
                $channel->wait(null, false, 30);
            }

        } catch (Exception $e) {
            $channel->close();
            $connection->close();
            if ($e->getCode() != 404) {
                die('Ошибка Redis: '.$e->getMessage());
            } else {
                die('queue not found: '.$e->getMessage());
            }
        }

        $channel->close();
        $connection->close();

    }


}
