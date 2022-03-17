<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    protected $allowedFields = [
        'id',
        'login',
        'password'
    ];


    public function getUsers($count = 50, $offset = 0)
    {
        $result = $this->slave->prepare('SELECT * FROM ' . $this->table . ' ORDER BY id ASC LIMIT ' . $count . ' OFFSET ' . $offset . ' ;');
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function findUsers($name, $surname)
    {
        $name = $name.'%';
        $surname = $surname.'%';
        $result = $this->slave->prepare('SELECT * FROM ' . $this->table . ' WHERE `name` LIKE :name AND `surname` LIKE :surname ORDER BY id ASC;');
        $result->bindParam(':name', $name);
        $result->bindParam(':surname', $surname);
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getUsersCount()
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ';');
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->rowCount();
        } else {
            return 0;
        }
    }

    public function isUserExist($login, $password)
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE `login` = :login AND `password` = :password;');
        $result->bindParam(':login', $login);
        $result->bindParam(':password', $password);
        $result->execute();

        if ($result->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserByLogin($login)
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE `login` = :login ;');
        $result->bindParam(':login', $login);
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetch(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getUserById($id)
    {
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE `id` = :id ;');
        $result->bindParam(':id', $id, \PDO::PARAM_INT);
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetch(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function getCurrentUser()
    {
        if (!isset($_COOKIE['auth'])) {
            header("Location: /?message=Вы не авторизованы!");
            die();
        }
        $password = $_COOKIE['auth'];
        $result = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE `password` = :password ;');
        $result->bindParam(':password', $password);
        $result->execute();

        if ($result->rowCount() > 0) {
            return $result->fetch(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    
    public function createUser($login, $password, $name, $age, $interests)
    {
        try {
            $result = $this->connection->prepare('INSERT INTO ' . $this->table . ' (login, password, name, age, interests) 
        value (:login, :password, :name, :age, :interests);');
            $result->bindParam(':login', $login);
            $result->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
            $result->bindParam(':name', $name);
            $result->bindParam(':age', $age, \PDO::PARAM_INT);
            $result->bindParam(':interests', $interests);
            $result->execute();
        } catch (\Exception $exception) {
            header("Location: /auth/register?message=Ошибка сохранения!");
            die();
        }
    }

}
