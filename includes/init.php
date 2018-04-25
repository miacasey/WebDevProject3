<?php

// the title
$title = "Mia's Photo Gallery";

// associative array that maps page id to page title in header.php
$pages = array(
  "index" => "Home",
  "upload" => "Upload",
  "login" => "Login",
  "logout" => "Logout"
);

// messages to deliver to the user
$messages = array();

// record a message and append to $messages
function record_message($message) {
  global $messages;
  array_push($messages, $message);
}

// print message for the user
function print_messages() {
  global $messages;
  foreach ($messages as $message) {
    echo "<p><strong>" . htmlspecialchars($message) . "</strong></p>\n";
  }
}

// execute the sql query (if possible, else return NULL)
function exec_sql_query($db, $sql, $params = array()) {
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return NULL;
}

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename) {
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_init_sql = file_get_contents($init_sql_filename);
    if ($db_init_sql) {
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return NULL;
}

// open connection to database
$db = open_or_init_sqlite_db("gallery.sqlite", "init/init.sql");

// check the login: if there is a session return the user that is logged in else return NULL
function check_login() {
  global $db;
  if (isset($_COOKIE["session"])) {
    $session = $_COOKIE["session"];
    $sql = "SELECT * FROM accounts WHERE session = :session";
    $params = array(
      ':session' => $session
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
      $user = $records[0];
      return $user['username'];
    }
  }
  return NULL;
}

// log in our user (or return the specific error message)
function log_in($username, $password) {
  global $db;
  if ($username && $password) {
    $sql = "SELECT * FROM accounts WHERE username = :username;";
    $params = array(
      ':username' => $username
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
      $account = $records[0];
      // check password with hash
      if ( password_verify($password, $account['password']) ) {
        $session = uniqid();
        $sql = "UPDATE accounts SET session = :session WHERE id = :user_id;";
        $params = array(
          ':user_id' => $account['id'],
          ':session' => $session
        );
        $result = exec_sql_query($db, $sql, $params);
        if ($result) {
          // successfully logged in, set a cookie to expire in 1 hour
          setcookie("session", $session, time()+3600);
          echo "<meta http-equiv='refresh' content='1'>";
          record_message("Logged in as $username.");
          return $username;
        } else {
          record_message("Log in failed.");
        }
      } else {
        record_message("Invalid username or password.");
      }
    } else {
      record_message("Invalid username or password.");
    }
  } else {
    record_message("No username or password given.");
  }
  return NULL;
}

function log_out() {
  global $current_user;
  global $db;
  if ($current_user) {
    $sql = "UPDATE accounts SET session = :session WHERE username = :username;";
    $params = array(
      ':username' => $current_user,
      ':session' => NULL
    );
    if (!exec_sql_query($db, $sql, $params)) {
      record_message("Log out failed.");
    }
  }
  setcookie("session", "", time()-3600);
  $current_user = NULL;
}


if (isset($_POST['login'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $username = trim($username);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $current_user = log_in($username, $password);
} else {
  $current_user = check_login();
}


function delete_photos($image){
  global $db;
  global $current_user;
  global $im_id;
  $sql= "SELECT username FROM accounts WHERE id = :id";
  $params= array(':id' => $image['account_id']);
  $records= exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  if ($records) {
    $im_id= $image['id'];
    if ($records[0]['username']==$current_user){
      $sql3= "SELECT * FROM tags LEFT OUTER JOIN images_tags ON tags.id=images_tags.tag_id LEFT OUTER JOIN images ON images_tags.image_id= images.id WHERE images.id= :image_id";
      $records3= exec_sql_query($db, $sql3, array(':image_id'=> $im_id));
      foreach ($records3 as $rec){
        delete_tag($rec['name'], $im_id);
      }
      $sql= "DELETE FROM images WHERE id=:id";
      $params= array(':id'=> $im_id);
      $records= exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
      $Path = '/uploads/images/' . $image['file_name'];
      unlink($_SERVER['DOCUMENT_ROOT'].$Path);
      echo "<script> location.reload() </script>";

      echo "<p> Photo successfully deleted! </p>";
    }
  } else if ($current_user != NULL) {
    echo "<p> Cannot delete photo with current user. </p>";
  } else {
    echo "<p> Log in to delete photo. </p>";
  }
}

function add_tag($tag_name, $file_id){
  global $db;
  // check that is the tag does not already exist for this image
  $sql= "SELECT * FROM tags INNER JOIN images_tags ON tags.id=images_tags.tag_id INNER JOIN images ON images_tags.image_id=images.id AND images.id=:file_id";
  $params= array(':file_id'=> $file_id);
  $records= exec_sql_query($db, $sql, $params);
  $add= TRUE;
  foreach ($records as $record){
    if ($record['name']==$tag_name){
      $add= FALSE;
    }
  }
  if ($add){
    // check if this tag already exists
    $sql1= "SELECT * FROM tags WHERE name LIKE '%' || :tag_name || '%'";
    $params1= array(':tag_name'=> $tag_name);
    $records1= exec_sql_query($db, $sql1, $params1)->fetchAll(PDO::FETCH_ASSOC);
    // if tag exists only add an entry in images_tags table
    if (isset($records1) and !empty($records1)) {
      $sql2= "INSERT INTO images_tags (image_id, tag_id) VALUES (:image_id, :tag_id)";
      foreach ($records1 as $record){
        $tag= $record['id'];
      }
      $params2= array(':image_id'=>$file_id, 'tag_id'=>$tag);
      $result= exec_sql_query($db, $sql2, $params2);
    } else {
    // create a new tag in tag table and new entry in images_tags table
      $sql2= "INSERT INTO tags('name') VALUES (:tag_name)";
      $params2= array(':tag_name'=>$tag_name);
      $result2= exec_sql_query($db, $sql2, $params2);
      $tag = $db->lastInsertId("id");
      $sql3= "INSERT INTO images_tags (image_id, tag_id) VALUES (:image_id, :tag_id)";
      $params3= array(':image_id'=>$file_id, 'tag_id'=>$tag);
      $result3= exec_sql_query($db, $sql3, $params3);

    }
  } else {
    echo "<p> Tag already tagged!</p>";
  }
}

function delete_tag($tag, $image_id){
  global $db;
  // check that only the user who uploaded the image is deleting a tag
  $sql= "SELECT * FROM accounts INNER JOIN images ON accounts.id=images.account_id AND images.id= :id";
  $params= array(':id'=> $image_id);
  $records= exec_sql_query($db, $sql, $params);
  $correct_user=FALSE;
  foreach($records as $record){
    if ($record['username']==check_login()){
      $correct_user= TRUE;
    }
  }
  if ($correct_user){
  // find tag id with the name given
    $sql1= "SELECT * FROM tags WHERE name= :name";
    $params1= array(':name'=> $tag);
    $records1= exec_sql_query($db, $sql1, $params1);
    $tag_id_number=0;
    foreach ($records1 as $record){
      if ($record['name']==$tag){
        $tag_id_number= $record['id'];
      }
    }
    // delete relationship in images_tags table
    $sql2= "DELETE FROM images_tags WHERE image_id= :image_id AND tag_id= :tag_id";
    $params2= array(':image_id'=> $image_id, ':tag_id'=>$tag_id_number);
    exec_sql_query($db, $sql2, $params2);

    // delete tag if there are no images left with this tag
    $sql4= "SELECT * FROM images_tags WHERE tag_id= :tag_id";
    $params4= array(':tag_id'=> $tag_id_number);
    $count= count(exec_sql_query($db, $sql4, $params4))-1;
    if ($count==0){
      echo "<p> Deleted tag! </p>";
      $sql3= "DELETE FROM tags WHERE name= :tag_name";
      $params3= array(':tag_name'=> $tag);
      exec_sql_query($db, $sql3, $params3);
    }
  }
}
?>
