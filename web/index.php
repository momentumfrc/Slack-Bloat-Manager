<?php
session_start();
require_once('api.php');
require_once('functions.php');
 ?>
<html>
<head>
  <script src="scripts/jquery-3.3.1.min.js"></script>
  <script src="scripts/index.js"></script>
  <link rel="stylesheet" type="text/css" href="style/index.css">
</head>
<body>
  <div id="container">
    <h1>
      <?php

      if(!isset($_SESSION["oauth_token"])) {
        header("Location: login.php");
      }

      $profile = getUserProfile();
      if(isset($profile["ok"]) && $profile["ok"]) {
        $profile = $profile["profile"];
        if(isset($profile["first_name"])) {
          echo($profile["first_name"]."'s ");
        } elseif(isset($profile["real_name"])) {
          echo($profile["real_name"]."'s ");
        } elseif(isset($profile["display_name"])) {
          echo($profile["display_name"]."'s ");
        }
      } else {
        if(isset($profile["error"]) && $profile["error"] == "not_authed") {
          header("Location: login.php");
        }
        die("Error retrieving user info: ".json_encode($profile));
      }
      ?>
      Files
    </h1>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["delfiles"])) {
        foreach($_POST["delfiles"] as $fileid) {
          $response = deleteFile($fileid);
          if(!(isset($response["ok"]) && $response["ok"])) {
            if($response["error"] == "file_deleted") {
              echo("<p>File ".$fileid." already deleted</p>");
            } else {
              die("Error deleting file: ".json_encode($response));
            }
          }
        }
      }

    }

    if(isset($_GET["sort"])) {
      $sort = htmlentities($_GET["sort"]);
    } elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sort"])) {
      $sort = $_POST["sort"];
    } else {
      $sort = "biggest";
    }

     ?>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
      <table>
        <tr>
          <th>Name</th>
          <th>
            <?php
            if($sort == "biggest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=smallest">Size&#9660;</a>');
            } elseif($sort == "smallest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=biggest">Size&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=biggest">Size</a>');
            }
            ?>
          </th>
          <th>
            <?php
            if($sort == "oldest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=newest">Date&#9660;</a>');
            } elseif($sort == "newest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=oldest">Date&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?sort=oldest">Date</a>');
            }
            ?>
          </th>
          <th>Delete?</th>
        </tr>
        <?php

        $files = listAllFiles();


        switch($sort) {
          case "newest":
            usort($files, "compare_newest");
            break;
          case "oldest":
            usort($files, "compare_oldest");
            break;
          case "biggest":
            usort($files, "compare_biggest");
            break;
          case "smallest":
            usort($files, "compare_smallest");
            break;
        }

        if(count($files) == 0 ) {
          echo('<tr><td rowspan="3">No results!</td></tr>');
        }

        foreach($files as $file) {
          echo('
          <tr>
            <td><a href="'.$file["permalink"].'" target="_blank">'.$file["title"].'</a></td>
            <td>'.human_filesize($file["size"]).'</td>
            <td>'.date('M j, Y \a\t g:i A', $file["created"]).'</td>
            <td class="checkbox"><input type="checkbox" name="delfiles[]" value='.$file["id"].'></td>
          </tr>
          ');
        }
         ?>
         <tr><td colspan="3"></td><td><input type="submit" value="Delete"></td></tr>
      </table>

      <input type="hidden" name="sort" value="<?php echo(htmlentities($sort)); ?>">
    </form>
  </div>
</body>
</html>
