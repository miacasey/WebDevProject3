<header id= "head">
  <h1 id="title"><?php echo $title; ?></h1>
      <?php
      foreach($pages as $page_id => $page_name) {
        // utilize the current location to style it differently (easier for the user)
        if ($page_id == $current_page_id) {
          $css_id = "id='current_page'";
        } else {
          $css_id = "";
        }
        echo "<a " . $css_id . " href='" . $page_id. ".php'>$page_name</a>";
      }
      ?>
    <p>
      <?php
      if ($current_user) {
        echo "Logged in as $current_user";
      }
      ?>
    </p>
</header>
