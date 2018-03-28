<?php
include("includes/init.php");

// current location used in header.php
$current_page_id="logout";

// log out user and end session (see init.php)
log_out();

// return message to user
if (!$current_user) {
  record_message("You've been successfully logged out.");
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>Log in- <?php echo $title;?></title>
</head>

<body>
  <?php include("includes/header.php");?>

  <div id="content-wrap">
    <h1>Log Out</h1>
    <?php
    // return message to user
    print_messages();
    ?>
  </div>

</body>

</html>
