<?php include('includes/init.php');
$current_page_id="index";
const BOX_UPLOADS_PATH = "uploads/images/";
global $current_user_id;

// query to find the current user, so we can show the delete button for all the images he/she uploaded
$records1= exec_sql_query($db, "SELECT * FROM accounts WHERE username=:username", array('username'=>$current_user));
if ($records1) {
  foreach ($records1 as $record){
    $current_user_id= $record['id'];
  }
}

// if a tag is searched for, set do_search to true and sanitize the search
if (isset($_GET['tag'])) {
  $do_search = TRUE;
  $tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
} else {
  // No search provided, so set the product to query to NULL
  $do_search = FALSE;
  $tag = NULL;
}

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
    ?>
    <div>
      <form id="searchForm" action="index.php" method="get" class="box2">
        <select name="tag">
          <option value="" selected disabled>Search By</option>
          <?php
          // search form has a dropdown with all the tag options
          $records= exec_sql_query($db, "SELECT * FROM tags");
          foreach($records as $record){
            ?>
            <option value="<?php echo $record['name'];?>"><?php echo $record['name'];?></option>
            <?php
          }
          ?>
        </select>
        <button type="submit">Search</button>
        <button name="view_all"> View All Images </button>
      </form>
    <div class= 'row'>

      <?php
      if ($do_search){
        // find all the images associated with the searched tag
        $sql = "SELECT * FROM images INNER JOIN images_tags ON images.id=images_tags.image_id INNER JOIN tags ON images_tags.tag_id=tags.id AND tags.name=:tag";
        $params = array(':tag' => $tag);
        $records = exec_sql_query($db, $sql, $params)->fetchAll();
        if (isset($records) and !empty($records)) {
          $len= count($records);
          if ($len >= 4) {
            $first= array_slice($records, 0, $len/4);
            $second= array_slice($records, $len/4, $len/4);
            $third= array_slice($records, $len/2, $len/4);
            $fourth= array_slice($records, (3*$len)/4);
          } else {
            $first = array_slice($records, 0, $len-1);
            $second= array_slice($records, $len-1);
            $third= array();
            $fourth= array();
          }
          echo "<div class='column'>";
          foreach($first as $record){
            $sql= "SELECT * FROM images WHERE file_name= :name";
            $params= array(':name'=> $record["file_name"]);
            $results=exec_sql_query($db, $sql, $params);
            foreach($results as $result){
              $record= $result;
            }
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($second as $record){
            $sql= "SELECT * FROM images WHERE file_name= :name";
            $params= array(':name'=> $record["file_name"]);
            $results=exec_sql_query($db, $sql, $params);
            foreach($results as $result){
              $record= $result;
            }
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($third as $record){
            $sql= "SELECT * FROM images WHERE file_name= :name";
            $params= array(':name'=> $record["file_name"]);
            $results=exec_sql_query($db, $sql, $params);
            foreach($results as $result){
              $record= $result;
            }
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($fourth as $record){
            $sql= "SELECT * FROM images WHERE file_name= :name";
            $params= array(':name'=> $record["file_name"]);
            $results=exec_sql_query($db, $sql, $params);
            foreach($results as $result){
              $record= $result;
            }
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
          }
          echo "</div></div>";
        }
      } else {
      ?>
      <div class= "row">
        <?php
        // query the database for all images
        $records = exec_sql_query($db, "SELECT * FROM images")->fetchAll(PDO::FETCH_ASSOC);
        if (isset($records) and !empty($records)) {
          $len= count($records);
          $first= array_slice($records, 0, $len/4);
          $second= array_slice($records, $len/4, $len/4);
          $third= array_slice($records, $len/2, $len/4);
          $fourth= array_slice($records, (3*$len)/4);
          echo "<div class='column'>";
          foreach($first as $record){
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
            if ($current_user != NULL and $record['account_id']==$current_user_id) {
              echo "<form action='index.php' method='get'><button type='submit' name='submit_delete_" . $record["id"] . "'>Delete</button></form>";
              if (isset($_GET["submit_delete_". $record["id"]])){
                delete_photos($record);
              }
            }
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($second as $record){
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
            if ($current_user != NULL and $record['account_id']==$current_user_id) {
              echo "<form action='index.php' method='get'><button type='submit' name='submit_delete_" . $record["id"] . "'>Delete</button></form>";
              if (isset($_GET["submit_delete_". $record["id"]])){
                delete_photos($record);
              }
            }
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($third as $record){
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
            if ($current_user != NULL and $record['account_id']==$current_user_id) {
              echo "<form action='index.php' method='get'><button type='submit' name='submit_delete_" . $record["id"] . "'>Delete</button></form>";
              if (isset($_GET["submit_delete_". $record["id"]])){
                delete_photos($record);
              }
            }
          }
          echo "</div>";
          echo "<div class='column'>";
          foreach($fourth as $record){
            // each image has a href to image.php to view the image bigger
            echo "<a href='image.php?id=" . $record["id"] . "'><img src=" . BOX_UPLOADS_PATH . $record["file_name"] . " alt=" . $record["description"] . " ></a>";
            if ($current_user != NULL and $record['account_id']==$current_user_id) {
              echo "<form action='index.php' method='get'><button type='submit' name='submit_delete_" . $record["id"] . "'>Delete</button></form>";
              if (isset($_GET["submit_delete_". $record["id"]])){
                delete_photos($record);
              }
            }
          }
          echo "</div></div>";
        }
      }
          ?>
    </div>
</body>
</html>
