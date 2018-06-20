<?php
session_start();
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
  <title>Slack Bloat Manager</title>
</head>
<body>
  <div id="container">
    <h1>Slack Bloat Manager</h1>
    <a class="mainlink" href="files.php">Files</a>
    <a class="mainlink" href="channels.php">Channels</a>
  </div>
</body>
</html>
