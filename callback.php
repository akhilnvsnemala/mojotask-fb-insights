<?php
require_once 'config.php';

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
    if (!isset($accessToken)) {
        echo "Login failed";
        exit;
    }

    $_SESSION['fb_access_token'] = (string) $accessToken;

    $response = $fb->get('/me?fields=id,name,picture', $accessToken);
    $user = $response->getGraphUser();

    $_SESSION['user'] = [
        'id' => $user->getField('id'),
        'name' => $user->getField('name'),
        'picture' => $user->getField('picture')->getField('url')
    ];

    header("Location: dashboard.php");
    exit;
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo "Graph returned an error: " . $e->getMessage();
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "Facebook SDK returned an error: " . $e->getMessage();
}
?>
