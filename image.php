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
  <?php include("includes/header.php");
  // query to find the username of the user who uploaded this image (with corresponding id), if there exists such a user
  $sql1= "SELECT * FROM accounts INNER JOIN images ON accounts.id=images.account_id AND images.id= :image_id";
  if (isset($_GET['id'])) {
    $im_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  }
  $params1= array('image_id'=> $im_id);
  $records1= exec_sql_query($db, $sql1, $params1);
  global $user;
  if ($records1) {
    foreach ($records1 as $record){
      echo "<p> Uploaded by " . $record['username'] . ". </p>";
      $user= $record['username'];
    }
  }
  ?>

  <div class="column2">
    <button><a href="index.php">Go Back</a></button>
    <?php
        // we query our image table for the clicked-on image and show the full image to the user
        if (isset($_GET['id'])) {
          $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
          $sql= "SELECT * FROM images WHERE id= :id";
          $params = array(':id' => $id);
          $records = exec_sql_query($db, $sql, $params)->fetchAll();
          foreach($records as $record){
            // shows the image with corresponding file name
            echo "<img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " width= '500'>&nbsp";
            // shows that image's description and source
            echo "<div class='box'> Description: " . $record["description"] . "<br><br> Source: " . $record["source"] . "<br><br>";
          }
          // query to find and list all the tags associated with this image
          $sql2= "SELECT * FROM tags INNER JOIN images_tags ON images_tags.tag_id=tags.id INNER JOIN images ON images_tags.image_id=images.id AND images_tags.image_id=:id";
          $tags = exec_sql_query($db, $sql2, $params)->fetchAll();
          echo "Tags: ";
          foreach ($tags as $tag){
            echo "<li>" . $tag['name'] . "<br>";
          }
          echo "</div>";
            ?>
          <form id='addNewTag' action='' method='post' class='box'>
                <label>Tag:</label>
                <input type="text" name="new_tag"/>
                <button name="addNewTag" type="submit">Add Tag</button>
          </form>
          <?php
          // if a tag is added, run the function add_tag (in init.php) and refresh the page to view the tag
          if (isset($_POST['addNewTag'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $tag = filter_input(INPUT_POST, 'new_tag', FILTER_SANITIZE_STRING);
            $tag= trim($tag);
            add_tag($tag, $id);
            echo "<meta http-equiv='refresh' content='1'>";
          }
        }
        // if the current user is the user who uploaded this image, show the delete tag dropdown form
        if (check_login()== $user and check_login()!= NULL){
        ?>
          <form id="deleteTag" action="" method="post" class='box'>
            <select name="tag_to_delete">
              <option value="" selected disabled>Search By</option>
              <?php
              // query to find all the tags associated with this image and put it in a dropdown option
              $sql= "SELECT * FROM tags INNER JOIN images_tags ON tags.id=images_tags.tag_id INNER JOIN images ON images_tags.image_id=images.id AND images.id= :id";
              $params= array(':id'=> $id);
              $records= exec_sql_query($db, $sql, $params);
              foreach($records as $record){
                ?>
                <option value="<?php echo $record['name'];?>"><?php echo $record['name'];?></option>
                <?php
              }
              ?>
            </select>
            <button type="submit">Delete</button>
          </form>
          <?php
          // if the user attempts to delete a tag, run the delete tag function (in init.php) and refresh the page
          if (isset($_POST['tag_to_delete'])) {
            $tag= filter_input(INPUT_POST, 'tag_to_delete', FILTER_SANITIZE_STRING);
            delete_tag($tag, $id);
            echo "<meta http-equiv='refresh' content='1'>";
          }
        }
        ?>
  </div>
</body>
</html>
