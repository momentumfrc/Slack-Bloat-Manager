<?php

include_once("specificvars.php");
global $oauth_token;

/**
* Preform a GET request on a specified url with the specified parameters
* @param string $url The url to query
* @param array $opts The url paramteters
* @return string The server's response
*/
function get_query_slack($url,$opts) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url.'?'.http_build_query($opts));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($curl);
  curl_close($curl);
  return $data;
}

/**
* Queries slack api for a list of files within the team
* @param int $count Number of items to return per page.
* @param int $page Page number of results to return.
* @return array File objects representing every requested file
*/
function listFiles($count, $page) {
  global $oauth_token;
  return json_decode(get_query_slack("https://slack.com/api/files.list",array("token"=>$oauth_token,"count"=>$count,"page"=$page)));
}

/**
* Queries slack api for a list of all files within the team
* @return array File objects representing every file in the team
*/
function listAllFiles() {
  $page = 1;
  $files = array();
  while(true) {
    $response = listFiles(20, $page);
    if(!$response["ok"]) {
      die("Error listing files. Response: ".json_encode($response));
    }
    $files = array_merge($files,$response["files"]);
    $page = $response["paging"]["page"]+1;
    if($page > $response["paging"]["pages"]) {
      return $files;
    }
  }
}

?>
