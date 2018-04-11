<?php
include("includes/init.php");

// current location used in header.php
$current_page_id="upload";

// check login to set the most recent user (or NULL)
$upload_user= check_login();

// set maximum file size for uploaded files.
const MAX_FILE_SIZE = 10000000;
// set path prefix to store our image uploads
const BOX_UPLOADS_PATH = "uploads/images/";

// if a user is logged in: they can upload an image; we store the user id
if ($upload_user != NULL) {
  $sql= "SELECT * FROM accounts WHERE username= :username";
  $params= array('username'=> $upload_user);
  $record= exec_sql_query($db, $sql, $params)->fetchAll();
  foreach ($record as $r) {
    $user_id= $r['id'];
  }
  // if the image is uploaded we save the fields needed to insert into our table
  if (isset($_POST["submit_upload"])) {
    $upload_info = $_FILES["image_file"];
    $upload_desc = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $upload_source= filter_input(INPUT_POST, 'source', FILTER_SANITIZE_STRING);
    // if there are no errors associated with the uploaded file
    if ($upload_info['error'] == UPLOAD_ERR_OK) {
      // store basename and file extension
      $upload_name = basename($upload_info["name"]);
      $upload_ext = strtolower(pathinfo($upload_name, PATHINFO_EXTENSION));
      // query to insert the new image upload into images table
      $sql = "INSERT INTO images (file_name, description, source, account_id) VALUES (:filename, :description, :source, :account_id)";
      $params = array(
      ':filename' => $upload_name,
      ':description' => $upload_desc,
      ':source' => $upload_source,
      ':account_id' => $user_id
      );
      $result = exec_sql_query($db, $sql, $params);
      // if the query is executed we move the uploaded file into our database and uploads/images folder
      if ($result) {
        $file_id = $db->lastInsertId("id");
        // add tags and relationships to the tags table and images_tags table
        $tags= array($_POST['tag1'], $_POST['tag2'], $_POST['tag3']);
        if (move_uploaded_file($upload_info["tmp_name"], BOX_UPLOADS_PATH . "$upload_name")){
          foreach ($tags as $tag){
            if ($tag != NULL) {
              add_tag($tag, $file_id);
            }
          }
          array_push($messages, "Your image has been uploaded.");
        }
      } // if query is not executed, send a failure message to the user
      else {
        array_push($messages, "Failed to upload image.");
        print_r($_FILES);
      }
    } // if the file upload had an error, send a failure message to the user
    else {
      array_push($messages, "Failed to upload image.");
      }
  }
} // if no user is logged in, tell them to log in
else {
  array_push($messages, "Log in to upload an image.");
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title><?php echo $pages[$current_page_id] . " - " . $title; ?></title>
</head>

<body>
  <?php include("includes/header.php");?>

  <div id="content-wrap">
    <h1>Upload an Image</h1>

    <?php
    print_messages();
    ?>

    <form id="uploadFile" action="upload.php" method="post" enctype="multipart/form-data" class="box">
      <ul>
        <label>Upload Image:</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
        <input type="file" name="image_file" required>
        <br> <br>
        <label>Description:</label> <input type="text" name="description">
        <br> <br>
        <label>Source:</label> <input type="text" name="source">
        <br> <br>
        <label>Tags:</label>
        <br>
          <input type="text" name="tag1" />
          <input type="text" name="tag2" />
          <input type="text" name="tag3" />
        <br> <br>
          <button name="submit_upload" type="submit">Upload</button>
        </li>
      </ul>
    </form>
  </div>
</body>
</html>
