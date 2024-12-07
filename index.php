<?php

    require_once 'Router.php';

    Router::addPage('/','indexController@homePage');

    Router::addPage('/Profile', 'userController@getUser');

    Router::addPage('/Settings',function() {

        echo 'Display Settings Page';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            echo 'Update Settings Page';

        }

    }, 'GET|POST');

    Router::executeRoute();

    echo Router::$Error;