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
  </div>
</body>
</html>
