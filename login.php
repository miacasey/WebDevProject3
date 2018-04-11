<?php
include("includes/init.php");

// declare the current location, utilized in header.php
$current_page_id="login";
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
    <h1>Log in</h1>

    <?php
    print_messages();
    ?>

    <form id="galleryLogin" action="login.php" method="post" class="box">
      <ul>
          <label>Username:</label>
          <input type="text" name="username" required/> <br> <br>
          <label>Password:</label>
          <input type="password" name="password" required/> <br> <br>
          <button name="login" type="submit">Log In</button>
      </ul>
    </form>
  </div>

</body>

</html>
