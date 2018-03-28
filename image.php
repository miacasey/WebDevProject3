<?php include('includes/init.php');

// current location used in header.php
$current_page_id="index";

// constant to define the path to our images
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
  <?php include("includes/header.php");?>
  <div>
    <?php
        //we query our image table for the clicked-on image and show the full image to the user
        if (isset($_GET['id'])) {
          $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
          $sql= "SELECT * FROM images WHERE id= :id";
          $params = array(':id' => $id);
          $records = exec_sql_query($db, $sql, $params)->fetchAll();
          foreach($records as $record){
            echo "<img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " width= '500'>&nbsp";
          }
        }
        ?>
  </div>
</body>
</html>
