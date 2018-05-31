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

      $profile = getCurrentUserProfile();
      if(isset($profile["ok"]) && $profile["ok"]) {
        echo(getName($profile["profile"])."'s ");
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
    $errorFiles = array();
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST["delfiles"])) {
        foreach($_POST["delfiles"] as $fileid) {
          $response = deleteFile($fileid);
          if(!(isset($response["ok"]) && $response["ok"])) {
            $errorFiles[$fileid] = $response["error"];
          } else {
            echo("Error deleting file: ".json_encode($response));
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
    <form id="mainf" action="<?php echo(htmlentities($_SERVER['PHP_SELF']) . '?sort='.htmlentities($sort).''); ?>" method="post">
      <table>
        <tr>
          <th>Owner</th>
          <th>Filename</th>
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
          <th id="selectall">Delete?</th>
          <?php
          $hasErrorRow = count($errorFiles) != 0;
          if($hasErrorRow) {
            echo('<th>Error</th>');
          } ?>
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

        $uids = array();

        foreach($files as $file) {
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
          echo('
            <td>'.$uids[$file["user"]].'</td>
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
           <td colspan="4"></td>
           <td><input type="submit" value="Delete"></td>
           <?php if($hasErrorRow) { echo("<td></td>"); } ?>
         </tr>
      </table>
    </form>
  </div>
</body>
</html>
