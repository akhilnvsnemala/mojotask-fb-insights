<?php

include('header.php');

// Get User Pages
try {
    $response = $fb->get('/me/accounts', $accessToken);
    // print_r($response);
    $pages = $response->getGraphEdge()->asArray();
    // print_r($pages);
} catch (Exception $e) {
    echo "Error fetching pages: " . $e->getMessage();
    exit;
}

?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">  
  <div class="container-fluid">  
    <a class="navbar-brand" href="#">Welcome, <?= htmlspecialchars($user['name']) ?></a>  
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">  
      <span class="navbar-toggler-icon"></span>  
    </button>  
    <div class="collapse navbar-collapse" id="navbarNav">  
      <ul class="navbar-nav ms-auto">  
        <li class="nav-item dropdown">  
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">  
          <img height="40" src="<?= htmlspecialchars($user['picture']) ?>" alt="Profile Picture">
          </a>  
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">  
             <li><a class="dropdown-item" href="logout.php">Logout</a></li> 
          </ul>  
        </li>  
      </ul>  
    </div>  
  </div>  
</nav>
   
<main class="container mt-4">
    <div class="row">
    <?php if(!empty($pages)) { ?>
    <form  method="POST">
        <div class="form-group">
            <div class="col-4">
            <label for="page">Please select the page:</label> </div>
            <div class="col-8">
            <select class="form-control" name="page_id" id="page" >
                <option value="">-- Select Page --</option>
                <?php foreach ($pages as $page) { ?>
                    <option value="<?= htmlspecialchars($page['id']) ?>" <?= isset($pageId) && $pageId == $page['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($page['name']) ?>
                    </option>
                <?php } ?>
            </select> </div>
        </div>
    </form>
                
    <?php } else{ ?>

    <div> No page available for this account</div>

    </div> 

    <?php } ?>

        <div class="row mt-3 card-section d-none">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Followers / Fans</h5>
                        <h3 id="page_fans">-</h3>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Engagement</h5>
                        <h3 id="page_engaged_users">-</h3>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Impressions</h5>
                        <h3 id="page_impressions">-</h3>
                    </div>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Total Reactions</h5>
                        <h3 id="page_reactions_total">-</h3>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</main>
 

<script>
$(document).ready(function () {
    $('#page').change(function () {
        var pageId = $(this).val();
        if (pageId) {
            $.ajax({
                url: 'fetch_metrics.php',  // Backend PHP file
                type: 'POST',
                data: { page_id: pageId },
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    $('.card-section').removeClass('d-none');
                    // Update the card values dynamically
                    $('#page_fans').text(response.data.followers || '-');
                    $('#page_engaged_users').text(response.data.engagement || '-');
                    $('#page_impressions').text(response.data.impressions || '-');
                    $('#page_reactions_total').text(response.data.reactions || '-');
                },
                error: function () {
                    alert('Error fetching metrics.');
                    $('.card-section').addClass('d-none');
                }
            });
        } else {
            // Reset metrics if no page is selected
            $('.card h3').text('-');
        }
    });
});

</script>
   

<? include('footer.php'); ?>
