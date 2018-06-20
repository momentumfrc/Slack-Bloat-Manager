<?php
session_start();
require_once('api.php');
require_once('functions.php');
 ?>
<html>
<head>
  <?php
  # Log in if not logged in
  if(!isset($_SESSION["oauth_token"])) {
    header("Location: login.php");
  }
  ?>
  
</head>
<body>

</body>
</html>
