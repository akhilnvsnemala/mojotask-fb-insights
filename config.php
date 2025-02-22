<?php
error_reporting(0);
session_start();
define('APP_ID', '1041390154681511'); // Replace with your Facebook App ID
define('APP_SECRET', '93e45a568cd94e3307796ce2302f8e76'); // Replace with your Facebook App Secret
define('FB_REDIRECT_URI', 'http://localhost/fb-login/callback.php'); // Update this when moving to production
define('FB_GRAPH_API_VERSION', 'v22.0'); // GRAPH API VERSION


require __DIR__ . '/vendor/autoload.php';

use Facebook\Facebook;

$fb = new Facebook([
    'app_id' => APP_ID,
    'app_secret' => APP_SECRET,
    'default_graph_version' => FB_GRAPH_API_VERSION,
]);

?>