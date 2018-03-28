<?php

// the title
$title = "My Photo Gallery";

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
      // Username is UNIQUE, so there should only be 1 record.
      $account = $records[0];
      return $account['username'];
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

// log out our user and set the user's session to NULL
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
  // remove the session from the cookie; force the session to expire
  setcookie("session", "", time()-3600);
  // no user logged in, set current user to NULL
  $current_user = NULL;
}

// check if we should login the user
if (isset($_POST['login'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $username = trim($username);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $current_user = log_in($username, $password);
} else {
  $current_user = check_login();
}
?>
