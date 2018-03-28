<?php include('includes/init.php');
$current_page_id="index";
const BOX_UPLOADS_PATH = "uploads/images/";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>Home</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
      <?php
      // query the database for all images
      $records = exec_sql_query($db, "SELECT * FROM images")->fetchAll(PDO::FETCH_ASSOC);
      if (isset($records) and !empty($records)) {
        foreach($records as $record){
          // each image has a href to image.php to view the image bigger
          echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " width= '200'></a> &nbsp";
        }
      }
      ?>
</body>
</html>
