<?php
require("post/post.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/all.css">
  <title>HOME</title>
</head>

<body>
  <div class="d-flex flex-column m-0 p-0 align-items-center">
    <?php require("outils/menu.php"); ?>
    <div class="index-content d-flex flex-row flex-wrap justify-content-center w-100 mx-auto mt-5">
      <?php echo getAllPost($pdo);?>
    </div>
    </section>
    <form method="GET" class="pagination d-flex flex-rowr">
      <?php echo getPagination($pdo);?>
    </form>
    </section>
    <?php require("outils/footer.php"); ?>
  </div>
  <script src="js/script.js"></script>
</body>

</html>