<?php
namespace App\Core;

use App\Models\User;
use \GuzzleHttp\Client as GuzzleClient;

class View
{
    function __construct()
    {
        $this->user = new User();
    }

    function generate($content_view, $template_view, $data = null)
    {
        include 'app/views/'.$template_view;
    }
}