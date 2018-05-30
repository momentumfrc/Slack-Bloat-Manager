<?php
session_start();
require_once('api.php');
require_once('functions.php');
 ?>
<html>
<head>
</head>
<body>
  <div id="navigator"><button id="openfiles">Files</button><button id="openchannels">Channels</button></div>
  <div id="files">
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
      $sort = "newest";
    }
    # See if the user specified a page, defaulting to page 1
    if(isset($_GET["page"])) {
      $page = $_GET["page"];
    } elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["page"])) {
      $page = $_POST["page"];
    }else {
      $page = 1;
    }

     ?>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
      <table>
        <tr>
          <th>Name</th>
          <th>
            <?php
            if($sort == "biggest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=smallest">Size&#9660;</a>');
            } elseif($sort == "smallest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=biggest">Size&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=biggest">Size</a>');
            }
            ?>
          </th>
          <th>
            <?php
            if($sort == "oldest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=newest">Date&#9660;</a>');
            } elseif($sort == "newest") {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=oldest">Date&#9650;</a>');
            } else {
              echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.$page.'&sort=oldest">Date</a>');
            }
            ?>
          </th>
          <th>Delete?</th>
        </tr>
        <?php
        # Limit number of results per page. Might make this configurable by the user in the future
        $limit = 50;


        $files = listAllFiles();


        switch($sort) {
          case "newest":
            usort($files, "compare_newest");
            $files = array_slice($files, ($page-1)*$limit, $limit);
            break;
          case "oldest":
            usort($files, "compare_oldest");
            $files = array_slice($files, ($page-1)*$limit, $limit);
            break;
          case "biggest":
            usort($files, "compare_biggest");
            $files = array_slice($files, ($page-1)*$limit, $limit);
            break;
          case "smallest":
            usort($files, "compare_smallest");
            $files = array_slice($files, ($page-1)*$limit, $limit);
            break;
        }

        if(count($files) == 0 ) {
          echo('<tr><td rowspan="3">No results!</td></tr>');
        }

        foreach($files as $file) {
          echo('
          <tr>
            <td>'.$file["title"].'</td>
            <td>'.human_filesize($file["size"]).'</td>
            <td>'.date('M j, Y \a\t g:i A', $file["created"]).'</td>
            <td><input type="checkbox" name="delfiles[]" value='.$file["id"].'></td>
          </tr>
          ');
        }
         ?>
      </table>
      <input type="submit" value="Delete">
      <input type="hidden" name="page" value="<?php echo(htmlentities($page)); ?>">
      <input type="hidden" name="sort" value="<?php echo(htmlentities($sort)); ?>">
    </form>
    <?php
    if($page > 1) {
      echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.($page-1).'&sort='.$sort.'">prev</a>');
    }
    if(isset($files["paging"]) && $files["paging"]["pages"] > $page) {
      echo('<a href="'.htmlentities($_SERVER["PHP_SELF"]).'?page='.($page+1).'&sort='.$sort.'">next</a>');
    }
     ?>
  </div>
  <div id="channels">
  </div>
</body>
</html>
