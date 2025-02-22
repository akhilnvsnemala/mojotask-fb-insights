<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page_id'])) {
    $page_id = $_POST['page_id'];
    $accessToken = $_SESSION['fb_access_token'];
    
    // Date handling
    $since = isset($_POST['since']) ? $_POST['since'] : date('Y-m-d', strtotime('-30 days'));
    $until = isset($_POST['until']) ? $_POST['until'] : date('Y-m-d');

    try {
        // Get page access token
        $response = $fb->get("/$page_id?fields=access_token", $accessToken);
        $page_access_token = $response->getGraphNode()['access_token'];

        // Define valid metrics for v22.0
        $metrics = [
            'page_post_engagements',          // Total engagements (likes, comments, shares)
            'page_impressions',               // Total impressions
            'page_actions_post_reactions_total' // Total reactions on posts
        ];

        // Build insights request
        $insightsData = [];
        $metricsString = implode(',', $metrics);
        $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?"
            . "metric={$metricsString}"
            . "&period=total_over_range"
            . "&since={$since}"
            . "&until={$until}"
            . "&access_token={$page_access_token}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            //echo 'Curl error: ' . curl_error($ch);
        } else {
            $insightsData = json_decode($response, true);
            // print_r($insightsData);
        }

        curl_close($ch);
        // print_r($insightsData);
        // Process metrics
        $result = [
            'engagement' => 0,
            'impressions' => 0,
            'reactions' => 0
        ];

        foreach ($insightsData as $metric) {
            switch ($metric['name']) {
                case 'page_post_engagements':
                    $result['engagement'] = $metric['values'][0]['value'];
                    break;
                case 'page_impressions':
                    $result['impressions'] = $metric['values'][0]['value'];
                    break;
                case 'page_actions_post_reactions_total':
                    $result['reactions'] = $metric['values'][0]['value'];
                    break;
            }
        }

        // Get fan count separately
        $fanCountResponse = $fb->get("/{$page_id}?fields=fan_count", $page_access_token);
        $fanCount = $fanCountResponse->getGraphNode()['fan_count'];

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'followers' => number_format($fanCount),
                'engagement' => number_format($result['engagement']),
                'impressions' => number_format($result['impressions']),
                'reactions' => number_format($result['reactions'])
            ]
        ]);
        
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Graph API Error: ' . $e->getMessage()
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $e->getMessage()
        ]);
    }
}