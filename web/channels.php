<?php
session_start();
require_once('api.php');
require_once('functions.php');
 ?>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style/style.css">
  <?php
  # Log in if not logged in
  if(!isset($_SESSION["oauth_token"])) {
    header("Location: login.php");
  }
  ?>
  <title>Channels</title>
</head>
<body>
  <div id="container">
    <h1>Channels</h1>
    <?php
      $channels = listAllChannels();
      for($i = 0; $i < count($channels); $i++) {
        $response = getChannelInfo($channels[$i]["id"]);
        if(isset($response["ok"]) && $response["ok"]) {
          $channels[$i] = $response["channel"];
        } else {
          die("Error getting channel info ".json_encode($response));
        }

      }
    ?>
    <table>
      <tr>
        <th>Name</th>
        <th>Purpose</th>
        <th>Created</th>
        <th>Latest activity</th>
        <th>Members</th>
      </tr>
      <?php
      if(count($channels) == 0) {
        echo('<tr colspan="5">No results!</tr>');
      } else {
        foreach($channels as $channel) {
          echo('<tr>');
          echo('<td>&#35;'.$channel["name"].'</td>');
          echo('<td>'.$channel["purpose"]["value"].'</td>');
          echo('<td>'.date('M j, Y \a\t g:i A', $channel["created"]).'</td>');
          echo('<td>'.date('M j, Y \a\t g:i A', $channel["latest"]["ts"]).'</td>');
          echo('<td>'.count($channel["members"]).'</td>');
          echo('</tr>');
        }
      }
       ?>
  </div>
</body>
</html>
