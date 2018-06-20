<?php
session_start();
require_once('api.php');
require_once('functions.php');
 ?>
<html>
<head>
  <script src="scripts/jquery-3.3.1.min.js"></script>
  <script src="scripts/index.js"></script>
  <link rel="stylesheet" type="text/css" href="style/style.css">
  <?php


    # Log in if not logged in
    if(!isset($_SESSION["oauth_token"])) {
      header("Location: login.php");
    }

    # Get user parameter, defaulting to currently logged in user
    if(isset($_GET["user"])) {
      $filteruser = $_GET["user"];
    } else {
      $authdata = getAuthData();
      if(isset($authdata["ok"]) && $authdata["ok"]) {
        $filteruser = $authdata["user_id"];
      } elseif(isset($authdata["error"]) && $authdata["error"] == "not_authed") {
        header("Location: login.php");
      }
    }

    # Get the sort parameter, defaulting to biggest
    if(isset($_GET["sort"])) {
      $sort = htmlentities($_GET["sort"]);
    }  else {
      $sort = "biggest";
    }

  ?>
</head>
<body>
  <div id="container">
    <h1><?php
      # Print the filtered user in the title
      if($filteruser == "all") {
        echo("All ");
      } else {
        $profile = getUserProfile($filteruser);
        if(isset($profile["ok"]) && $profile["ok"]) {
          echo(getName($profile["profile"])."'s ");
        } else {
          if(isset($profile["error"]) && $profile["error"] == "not_authed") {
            header("Location: login.php");
          }
          die("Error retrieving user info: ".json_encode($profile));
        }
      }
      ?>Files
    </h1>
    <?php

    if($filteruser != "all") {
      echo('<a id="allusers" href="'.htmlentities($_SERVER['PHP_SELF']).'?user=all&sort='.$sort.'">View all files</a>');
    }

    $errorFiles = array();
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["delfiles"])) {
        foreach($_POST["delfiles"] as $fileid) {
          $response = deleteFile($fileid);
          if(!(isset($response["ok"]) && $response["ok"])) {
            $errorFiles[$fileid] = $response["error"];
          }
        }
      }

    }



     ?>
    <form id="mainf" action="<?php echo(htmlentities($_SERVER['PHP_SELF']) . '?user='.htmlentities($filteruser).'&sort='.$sort); ?>" method="post">
      <table>
        <tr>
          <th>Owner</th>
          <th>Filename</th>
          <th>
            <?php
            if($sort == "biggest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=smallest">Size&#9660;</a>');
            } elseif($sort == "smallest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=biggest">Size&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=biggest">Size</a>');
            }
            ?>
          </th>
          <th>
            <?php
            if($sort == "oldest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=newest">Date&#9660;</a>');
            } elseif($sort == "newest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=oldest">Date&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?user='.htmlentities($filteruser).'&sort=oldest">Date</a>');
            }
            ?>
          </th>
          <th id="selectall">Delete?</th>
          <?php
          $hasErrorRow = count($errorFiles) != 0;
          if($hasErrorRow) {
            echo('<th>Error</th>');
          } ?>
        </tr>
        <?php
        if($filteruser == "all") {
          $files = listAllFiles();
        } else {
          $files = listAllFilesForUser($filteruser);
        }



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
          if($hasErrorRow) {
            echo('<tr><td colspan="6">No results!</td></tr>');
          } else {
            echo('<tr><td colspan="5">No results!</td></tr>');
          }
        }

        $uids = array();

        $totalbytes = 0;

        foreach($files as $file) {
          $totalbytes += $file["size"];

          if(!isset($uids[$file["user"]])) {
            $uprofile = getUserProfile($file["user"]);
            if(isset($uprofile["ok"]) && $uprofile["ok"]) {
              $uids[$file["user"]] = getName($uprofile["profile"], true);
            } else {
              die("Error retrieving user info: ".json_encode($uprofile));
            }
          }
          $errored = isset($errorFiles[$file["id"]]);
          if($errored) {
            echo('<tr class="erroredfile">');
          } else {
            echo('<tr>');
          }
          if($filteruser == "all") {
            echo('<td><a class="userlink" href="'.htmlentities($_SERVER['PHP_SELF']).'?user='.$file["user"].'&sort='.$sort.'">'.$uids[$file["user"]].'</a></td>');
          } else{
            echo('<td>'.$uids[$file["user"]].'</td>');
          }
          echo('
            <td><a href="'.$file["permalink"].'" target="_blank">'.$file["title"].'</a></td>
            <td>'.human_filesize($file["size"]).'</td>
            <td>'.date('M j, Y \a\t g:i A', $file["created"]).'</td>
            <td class="checkbox"><input type="checkbox" name="delfiles[]" value="'.$file["id"].'"');
            if($errored) {
              echo(' checked');
            }
          echo('></td>');
          if($errored) {
            echo("<td>");
            switch($errorFiles[$file["id"]]) {
              case "file_deleted":
                echo("Already deleted");
                break;
              case "cant_delete_file":
                echo("Insufficient permissions");
                break;
              case "ratelimited":
                echo("Exceeded rate limit (try again)");
              default:
                echo($errorFiles[$file["id"]]);
                break;
            }
            echo("</td>");
          } elseif($hasErrorRow) {
            echo("<td></td>");
          }
          echo('</tr>');
        }
         ?>
         <tr>
           <td colspan="2"></td>
           <td><span id="totalsize">Total:</span> <?php echo(human_filesize($totalbytes)); ?></td>
           <td></td>
           <td><input type="submit" value="Delete"></td>
           <?php if($hasErrorRow) { echo("<td></td>"); } ?>
         </tr>
      </table>
    </form>
  </div>
</body>
</html>
