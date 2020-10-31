<?php
$Home_dir = $_SERVER['DOCUMENT_ROOT']."/Camagru/";
require($Home_dir."post/post.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.css">
  <title>Create Post</title>
</head>

<body>
  <div class="d-flex flex-column m-0 p-0">
    <?php require($Home_dir."outils/menu.php"); ?>
    <div class="create_post d-flex flex-column w-100 mt-5">
      <div id="failed" style="display: none;" class="alert alert-danger text-center" role="alert">
        <h4 class="alert-heading">Failed!</h4>
      </div>
      <div class="d-flex flex-column flex-wrap justify-content-center align-items-center">
        <div class="camera d-flex flex-column align-items-center justify-content-center">
          <div class="boxEditEmoji d-none flex-row flex-wrap justify-content-center align-items-center">
            <div class="d-flex flex-column justify-content-center align-items-center m-1" id="sizeEmoji">
              <div>Size:</div>
              <div><input style="box-shadow:none;" type="range" value="50" min="50" max="480"
                  onchange="changeSize('1')"><input class="range-value" type="text" value="50"
                  onchange="changeSize('2')"></div>
            </div>
            <div class="d-flex flex-column justify-content-center align-items-center m-1" id="topEmoji">
              <div>Top:</div>
              <div><input style="box-shadow:none;" type="range" value="0" min="0" max="480"
                  onchange="changePositionEmoji('Top_1')"><input class="range-value" type="text" value="0"
                  onchange="changePositionEmoji('Top_2')"></div>
            </div>
            <div class="d-flex flex-column justify-content-center align-items-center m-1" id="leftEmoji">
              <div>Left:</div>
              <div><input style="box-shadow:none;" type="range" value="0" min="0" max="720"
                  onchange="changePositionEmoji('Left_1')"><input class="range-value" type="text" value="0"
                  onchange="changePositionEmoji('Left_2')"></div>
            </div>
            <div class="mt-3">
              <a onclick="saveEmoji()"><i class="fas fa-check-circle fa-2x" style="color:#4caf50;margin:0 5px"></i></a>
              <a onclick="cancelEmoji()"><i class="fas fa-times-circle fa-2x" style="color:red;"></i></a>
            </div>
          </div>
          <div class="w-100 position-relative d-flex flex-column align-items-center">
            <div style="left: 0px;top:0px;"
              class="box-emoji position-absolute d-none flex-column justify-content-center">
              <div><img width="50" height="50"></div>
              <div class="d-flex flex-row">
              </div>
            </div>
            <canvas width="720" height="420"></canvas>
            <div class="d-flex flex-row justify-content-center align-items-center">
              <div class="createImageButton d-flex flex-column justify-content-center" id="capture"><i class="fas fa-camera fa-2x"
                  style="color:white"></i></div>
              <div class="createImageButton d-flex flex-column justify-content-center" id="upload_image"
                onclick="document.getElementById('upload').click()"><i class="fas fa-upload fa-2x"
                  style="color:white"></i></div>
              <input type="file" id="upload" style="display: none;" onchange="uploadImage()">
              <div class="createImageButton d-flex flex-column justify-content-center"><i class="fas fa-icons fa-2x" style="color:white" onclick="document.querySelectorAll('.gr_emoji > div')[0].style='display:flex!important'"></i></div>
              <div class="createImageButton d-flex flex-column justify-content-center"><i class="fas fa-history fa-2x"  style="color:white" onclick="document.querySelectorAll('.gr_emoji > div')[1].style='display:flex!important'"></i></div>
            </div>
          </div>
        </div>
        <div class="gr_emoji d-flex flex-row">
          <div class="d-none flex-column justofy-content-center align-items-center">
            <div class="w-100 text-right mr-4 text-dark">
              <span class="text-white font-weight-bold" style="cursor:pointer;font-size:18px;" onclick="document.querySelectorAll('.gr_emoji > div')[0].style='display:none!important'">X</span>
            </div>
            <div class="emojiBox d-flex flex-row flex-wrap justify-content-center">
              <div><img class="emoji" src="../img/emoji1.png" onclick="selectEmoji(1)"></div>
              <div><img class="emoji" src="../img/emoji2.png" onclick="selectEmoji(2)"></div>
              <div><img class="emoji" src="../img/emoji3.png" onclick="selectEmoji(3)"></div>
              <div><img class="emoji" src="../img/emoji4.png" onclick="selectEmoji(4)"></div>
              <div><img class="emoji" src="../img/emoji5.png" onclick="selectEmoji(5)"></div>
              <div><img class="emoji" src="../img/emoji6.png" onclick="selectEmoji(6)"></div>
              <div>
                <input type="file" id="fileinput" style="display: none;" onchange="addNewEmoji()">
                <img class="emoji" src="../img/addEmoji.png" onclick="document.getElementById('fileinput').click();">
              </div>
            </div>
          </div>
          <div class="d-none flex-column justofy-content-center align-items-center">
            <div class="w-100 text-right mr-4 text-dark">
              <span class="text-white font-weight-bold" style="cursor:pointer;font-size:18px;" onclick="document.querySelectorAll('.gr_emoji > div')[1].style='display:none!important'">X</span></div>
            <div class="emoji-activity d-flex flex-row flex-wrap justify-content-center">

            </div>
          </div>
        </div>
        <div class="imageCapture d-none">
          <img id="image_capture">
        </div>
        <div class="post_info d-none flex-column justify-content-center align-items-center">
          <div class="w-100 d-flex flex-row">
            <button class="w-100" onclick="post_back()"><i class="fas fa-chevron-left"></i> Back</button>
            <button class="w-100" onclick="post_sent()">Post <i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>
    </section>
  </div>
  <section class="slider w-100 d-flex flex-column" style="overflow: hidden;">
    <div class="controlslide w-100 d-flex flex-row justify-content-end align-items-center pr-3"
      style="color: white!important;">
      <div class="mr-auto ml-2" style="color: white!important;">
        <h4 style="color: white!important;">My Post</h4>
      </div>
      <div class="rightslide" style="cursor: pointer;" onclick="slide_click('previous')">
        <span style="color: white!important;"><i class="fas fa-chevron-left" style="color: white!important;"></i>
          Previous</span>
      </div>
      <div class="leftslide ml-3" style="cursor: pointer;" onclick="slide_click('next')">
        <span style="color: white!important;">Next
          <i class="fas fa-chevron-right" style="color: white!important;"></i>
        </span>
      </div>
    </div>
    <div class="slider-content d-flex flex-row m-1" style="border: 1px solid black;">
      <?php $_GET["search"] = $_SESSION["User"]; echo getAllPost($pdo);?>
    </div>
  </section>
  <?php require($Home_dir."outils/footer.php"); ?>
  <script src="../js/script.js"></script>
  <script src="../js/camera.js"></script>
</body>

</html>