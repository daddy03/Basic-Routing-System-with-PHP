<?php
    // Include the Router class for handling routes
    require_once 'Router.php';
    
    // Add a route for the home page, linking to the homePage method of the indexController
    Router::addPage('/', 'indexController@homePage');
    
    // Add a route for the Profile page, linking to the getUser method of the userController
    Router::addPage('/Profile', 'userController@getUser');
    
    // Add a route for the Settings page. This route can handle both GET and POST requests
    Router::addPage('/Settings', function() {
        // Display the Settings Page
        echo 'Display Settings Page';
        
        // If the request method is POST, display the Update Settings Page
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo 'Update Settings Page';
        }
    }, 'GET|POST');
    
    // Execute the matched route
    Router::executeRoute();
    
    // Output any error messages that have occurred
    echo Router::$Error;
