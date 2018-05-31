<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', TRUE);

date_default_timezone_set("America/Los_Angeles");

/**
* Format a byte measurement in a method easily read and understood by humans
* @param int $bytes The byte measurement to be formated
* @param int $decimals How many decimal places to use
* @return String The formatted size
*/
function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

/**
* Compare two files by creation date, newest to oldest
* @param array $file1 The first file to compare
* @param array $file2 The second file to compare
* @return integer An integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
*/
function compare_newest($file1, $file2) {
  return $file2["created"] - $file1["created"];
}

/**
* Compare two files by creation date, oldest to newest
* @param array $file1 The first file to compare
* @param array $file2 The second file to compare
* @return integer An integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
*/
function compare_oldest($file1, $file2) {
  return $file1["created"] - $file2["created"];
}

/**
* Compare two files by size, largest to smallest
* @param array $file1 The first file to compare
* @param array $file2 The second file to compare
* @return integer An integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
*/
function compare_biggest($file1, $file2) {
  return $file2["size"] - $file1["size"];
}

/**
* Compare two files by size, smallest to largest
* @param array $file1 The first file to compare
* @param array $file2 The second file to compare
* @return integer An integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
*/
function compare_smallest($file1, $file2) {
  return $file1["size"] - $file2["size"];
}

/**
* Get a name from a user profile
* @param array $user The profile to get a name from
* @param boolean $full Whether or not to try to get the full name
* @return string The user's name
*/
function getName($user, $full=false) {
  if(isset($user["first_name"]) && !$full) {
    return $user["first_name"];
  } elseif(isset($user["real_name"])) {
    return $user["real_name"];
  } elseif(isset($user["display_name"])) {
    return $user["display_name"];
  }
  return false;
}
 ?>
