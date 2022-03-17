<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\View;
use \Exception;

class Controller_User extends Controller
{
    function __construct()
    {
        $this->model = new User();
        $this->view = new View();
    }

    public function action_profile()
    {
        if (!isset($_COOKIE['auth'])) {
            header("Location: /?message=Вы не авторизованы!");
            die();
        }
        $currentUser = [];
        $isCurrentUser = false;
        
        if (isset($_COOKIE['auth'])) {
            $currentUser = $this->model->getCurrentUser();
        }
        if (isset($GLOBALS['GET_PARAMS']['id']) && !empty($GLOBALS['GET_PARAMS']['id'])) {
            $id = (int)$GLOBALS['GET_PARAMS']['id'];
            $user = $this->model->getUserById($id);
            if (!empty($currentUser) && $user !== false && $currentUser['id'] == $user['id']) {
                $isCurrentUser = true;
            }
        } else {
            $user = $currentUser;
            $isCurrentUser = true;
        }
        if ($user === false || empty($user) ) {
            header("Location: /?message=Пользователь не найден!");
            die();
        }
        unset($user['password']);

        $this->view->generate('user/profile.php', 'template_view.php', ['user' => $user, 'isCurrentUser' => $isCurrentUser] );
    }

}