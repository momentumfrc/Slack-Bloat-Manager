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
    <table>
      <tr>
        <th>Name</th>
        <th>Size</th>
        <th>Date</th>
      </tr>
      <?php
      # Limit number of results per page. Might make this configurable by the user in the future
      $limit = 50;

      # See if the user specified a page, defaulting to page 1
      if(isset($_GET["page"])) {
        $page = $_GET["page"];
      } else {
        $page = 1;
      }

      $files = listAllFiles();

      if(isset($_GET["sort"])) {
        $sort = htmlentities($_GET["sort"]);
      } else {
        $sort = "newest";
      }
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
        </tr>
        ');
      }
       ?>
    </table>
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
