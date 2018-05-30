<?php
session_start();
include_once('api.php');

if(isset($_SESSION["oauth_token"])) {
  header("Location: index.php");
}
if(isset($_GET["code"])) {
  $response = getTokenFromVerificationCode($_GET["code"]);
  if(isset($response["ok"]) && $response["ok"]) {
    $_SESSION["oauth_token"] = $response["access_token"];
    header("Location: index.php");
    exit("Auth success");
  } else {
    die("Error validating verification code: ".json_encode($response));
  }
}

$opts = array(
  "client_id"=>$client_id,
  "scope"=>"files:read files:write:user users.profile:read"
);
header("Location: https://slack.com/oauth/authorize?".http_build_query($opts));
 ?>
