function Activity(id, width, top, left, img) {
  return {
    id: id,
    img: img,
    width: width,
    top: top,
    left: left,
  };
}
var csrfToken = document.querySelector('input[name="csrfToken"]').value;
var post_info = document.getElementsByClassName("post_info")[0];
var gr_emoji = document.getElementsByClassName("gr_emoji")[0];
var camera = document.getElementsByClassName("camera")[0];
var imageCapture = document.getElementsByClassName("imageCapture")[0];
var image_capture = document.getElementById("image_capture");
var array_activity = Array();
var width = 720;
var height = 420;
var canvas = document.getElementsByTagName("canvas")[0];
var context = canvas.getContext("2d");
var IdInterval = null;
var ImageToDraw = null;
var mediaStream = null;
var newimage = document.createElement('img');
window.addEventListener("resize", sizeOfVideo);
window.addEventListener("load", function () {
  var capture = document.getElementById("capture");
  sizeOfVideo();
  navigator.mediaDevices
    .getUserMedia({
      video: {
        width: 1920,
        height: 1080,
      },
    })
    .then(function(Stream){
      mediaStream = Stream;
      DrawVideo();
    })
    .catch(function (error) {
      console.log(error);
    });
  capture.addEventListener("click", function () {
    image_capture.setAttribute("src", canvas.toDataURL("image/jpeg"));
    post_info.className =
      "post_info d-flex flex-column justify-content-center align-items-center";
    gr_emoji.className = "gr_emoji d-none flex-row";
    gr_emoji.className = "gr_emoji d-none flex-row";
    camera.className =
      "camera d-none flex-column align-items-center justify-content-center";
    imageCapture.className = "imageCapture d-block";
    document.getElementsByClassName("slider")[0].className = "slider d-none";
  });
});
function DrawVideo(){
  var video = document.createElement("video");
    if (typeof video.srcObject == "object") video.srcObject = mediaStream;
    else video.src = URL.createObjectURL(mediaStream);
    ImageToDraw = video;
    video.play();
    IdInterval = setInterval(DrawImage, 16);
}
function DrawImage(){
  context.drawImage(ImageToDraw, 0, 0, width, height);
  for (var i = 0; i < array_activity.length; i++) {
    var elem = array_activity[i];
    newimage.setAttribute("src", elem.img);
    newimage.width = elem.width;
    newimage.height = elem.width;
    context.drawImage(
      newimage,
      elem.left,
      elem.top,
      elem.width,
      elem.width
    );
  }
}
function deleteActivity(id) {
  array_activity.splice(id, 1);
  resetIndex();
  document
    .querySelectorAll(".gr_emoji > div")[1]
    .setAttribute("style", "display:none!important");
}
function editActivity(id) {
  var emoji_box = document.getElementsByClassName("box-emoji")[0];
  var boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  var left_input = document.querySelector("#leftEmoji input[type='range']");
  var top_input = document.querySelector("#topEmoji input[type='range']");
  var size_input = document.querySelector("#sizeEmoji input[type='range']");
  var emoji_image = document.querySelector(".box-emoji img");
  emoji_box.className =
    "box-emoji position-absolute d-flex flex-column justify-content-center";
  emoji_image.setAttribute("src", array_activity[id].img);
  emoji_image.width = array_activity[id].width;
  emoji_image.height = emoji_image.width;
  emoji_box.setAttribute(
    "style",
    "left:" +
      array_activity[id].left +
      "px;top:" +
      array_activity[id].top +
      "px;"
  );
  left_input.value = array_activity[id].left;
  top_input.value = array_activity[id].top;
  size_input.value = emoji_image.width;
  boxEditEmoji.setAttribute("style", "display:flex!important");
  changePositionEmoji("Left_1");
  changePositionEmoji("Top_1");
  deleteActivity(id);
}
function resetIndex() {
  var emoji_activity = document.getElementsByClassName("emoji-activity")[0];
  var i = 0;
  emoji_activity.innerHTML = "";
  array_activity.forEach(function (val) {
    val.id = i;
    emoji_activity.innerHTML +=
      '<div class="emoji-act d-flex flex-column align-items-center">' +
      '<div class="d-flex flex-row">' +
      '<button onclick="editActivity(' +
      val.id +
      ')">Edit</button>' +
      '<button onclick="deleteActivity(' +
      val.id +
      ')">Delete</button>' +
      "</div>" +
      '<img src="' +
      val.img +
      '">' +
      "</div>";
    i++;
  });
  if (ImageToDraw instanceof Image)
    DrawImage();
}
function sizeOfVideo() {
  var canvas = document.getElementsByTagName("canvas")[0];
  var img = document.getElementById("image_capture");
  var slide = document.getElementsByClassName("slider-content")[0];
  var all_post = document.getElementsByClassName("post");
  var camera = document.getElementsByClassName("camera")[0];
  slide.setAttribute("style", "width:" + all_post.length * 310 + "px");
  changeSize(2);
  if (window.outerWidth < 720 || window.innerWidth < 720) {
    width = 320;
    height = 360;
    document.querySelector(".boxEditEmoji").className =
      "boxEditEmoji d-none flex-column flex-wrap justify-content-center align-items-center";
  } else {
    width = 720;
    height = 480;
  }
  camera.setAttribute("style", "width:" + width + "px!important;");
  canvas.width = width;
  canvas.height = height;
  img.width = width;
  img.height = height;
  if (ImageToDraw instanceof Image)
    DrawImage();
}
function post_back() {
  post_info.className =
    "post_info d-none flex-column justify-content-center align-items-center";
  gr_emoji.className = "gr_emoji d-flex flex-row";
  gr_emoji.className = "gr_emoji d-flex flex-row";
  camera.className =
    "camera d-flex flex-column align-items-center justify-content-center";
  imageCapture.className = "imageCapture d-none";
  document.getElementById("image_capture").setAttribute("src","");
  document.getElementsByClassName("slider")[0].className =
    "slider w-100 d-flex flex-column";
}
function post_sent() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText == "Failed") {
        document
          .getElementById("failed")
          .setAttribute("style", "display:block");
        document.getElementById("failed").innerHTML =
          "Image(JPG OR JPEG OR PNG OR GIF) And Size less than 10MB";
        setTimeout(function () {
          document
            .getElementById("failed")
            .setAttribute("style", "display:none;");
        }, 30000);
      }
      window.location.reload();
    }
  };
  xhttp.open("POST", "image.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send(
    "csrfToken="+csrfToken+"&image_data=" + document.getElementById("image_capture").getAttribute("src")
  );
}
function uploadImage() {
  var file = document.getElementById("upload").files[0];
  var reader = new FileReader();
  reader.onload = function () {
    var imgUplaoad = new Image();;
    imgUplaoad.src = this.result;
    imgUplaoad.onload = function(){
      array_activity = Array();
      resetIndex();
      Display_createImageButton(document.getElementsByClassName('createImageButton').length-1);
      clearInterval(IdInterval);
      ImageToDraw = imgUplaoad;
      DrawImage();
    };
  };
  if (file) reader.readAsDataURL(file);
}
function Display_createImageButton(index){
  var createImageButton = document.getElementsByClassName('createImageButton');
  if(createImageButton[index].className.indexOf("d-none") > -1)
    createImageButton[index].className = createImageButton[index].className.replace("d-none","d-flex");
  else if (createImageButton[index].className.indexOf("d-flex") > -1)
    createImageButton[index].className = createImageButton[index].className.replace("d-flex","d-none");
}
function selectEmoji(index) {
  var emoji_box = document.getElementsByClassName("box-emoji")[0];
  var boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  var emoji_image = document.querySelector(".box-emoji img");
  emoji_box.className =
    "box-emoji position-absolute d-flex flex-column justify-content-center";
  emoji_image.setAttribute(
    "src",
    document.getElementsByClassName("emoji")[index].src
  );
  boxEditEmoji.setAttribute("style", "display:flex!important");
  document
    .querySelectorAll(".gr_emoji > div")[0]
    .setAttribute("style", "display:none!important");
}
function saveEmoji() {
  var boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  var emoji_box = document.getElementsByClassName("box-emoji")[0];
  var img = document.querySelector(".box-emoji img");
  array_activity[array_activity.length] = Activity(
    array_activity.length,
    img.width,
    emoji_box.offsetTop,
    emoji_box.offsetLeft,
    img.getAttribute("src")
  );
  emoji_box.className =
    "box-emoji position-absolute d-none flex-column justify-content-center";
  boxEditEmoji.setAttribute("style", "display:none!important");
  resetIndex();
  if (ImageToDraw instanceof Image)
    DrawImage();
}
function cancelEmoji() {
  var emoji_box = document.getElementsByClassName("box-emoji")[0];
  var boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  boxEditEmoji.setAttribute("style", "display:none!important");
  emoji_box.className =
    "box-emoji position-absolute d-none flex-column justify-content-center";
}
function changeSize(id) {
  var input_range = document.querySelector("#sizeEmoji input[type='range']");
  var input_text = document.querySelector("#sizeEmoji input[type='text']");
  var img = document.querySelector(".box-emoji img");
  if (id == "1") {
    input_text.value = input_range.value;
    img.width = input_range.value;
  } else if (id == "2") {
    input_range.value = input_text.value;
    img.width = input_text.value;
  }
  if (img.width > width) {
    input_text.value = width;
    input_range.value = width;
    img.width = width;
  }
  if (img.width < 50) {
    input_text.value = 50;
    input_range.value = 50;
    img.width = 50;
  }
  img.height = img.width;
  changePositionEmoji("Left_1");
  changePositionEmoji("Top_1");
}
function changePositionEmoji(id) {
  var box = document.querySelector(".box-emoji");
  var img = document.querySelector(".box-emoji img");
  if (id == "Left_1" || id == "Left_2") {
    var input_range = document.querySelector("#leftEmoji input[type='range']");
    var input_text = document.querySelector("#leftEmoji input[type='text']");
    if (id == "Left_1") {
      input_text.value = input_range.value;
      box.setAttribute(
        "style",
        "left:" + input_text.value + "px;top:" + box.offsetTop + "px;"
      );
    } else if (id == "Left_2") {
      input_range.value = input_text.value;
      box.setAttribute(
        "style",
        "left:" + input_range.value + "px;top:" + box.offsetTop + "px;"
      );
    }
    if (box.offsetLeft > width - img.width) {
      input_text.value = width - img.width;
      input_range.value = width - img.width;
      box.setAttribute(
        "style",
        "left:" + (width - img.width) + "px;top:" + box.offsetTop + "px;"
      );
    }
    if (box.offsetLeft < 0) {
      input_text.value = 0;
      input_range.value = 0;
      box.setAttribute(
        "style",
        "left:" + 0 + "px;top:" + box.offsetTop + "px;"
      );
    }
  } else if (id == "Top_1" || id == "Top_2") {
    var input_range = document.querySelector("#topEmoji input[type='range']");
    var input_text = document.querySelector("#topEmoji input[type='text']");
    if (id == "Top_1") {
      input_text.value = input_range.value;
      box.setAttribute(
        "style",
        "left:" + box.offsetLeft + "px;top:" + input_text.value + "px;"
      );
    } else if (id == "Top_2") {
      input_range.value = input_text.value;
      box.setAttribute(
        "style",
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;"
      );
    }
    if (box.offsetTop > height - img.width) {
      input_text.value = height - img.width;
      input_range.value = height - img.width;
      box.setAttribute(
        "style",
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;"
      );
    }
    if (box.offsetTop < 0) {
      input_text.value = 0;
      input_range.value = 0;
      box.setAttribute(
        "style",
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;"
      );
    }
  }
}
function addNewEmoji() {
  var file = document.getElementById("fileinput").files[0];
  var reader = new FileReader();
  var emojiBox = document.getElementsByClassName("emojiBox")[0];
  var div = document.createElement("div");
  var img = document.createElement("img");
  reader.onload = function () {
    img.src = this.result;
    img.className = "emoji";
    img.setAttribute(
      "onClick",
      "selectEmoji(" +
        (document.getElementsByClassName("emoji").length - 1) +
        ")"
    );
    div.appendChild(img);
    emojiBox.appendChild(div);
    var all_div_emojiBox = document.querySelectorAll(".emojiBox > div");
    var copyDiv = all_div_emojiBox[all_div_emojiBox.length - 1].innerHTML;
    all_div_emojiBox[all_div_emojiBox.length - 1].innerHTML =
      all_div_emojiBox[all_div_emojiBox.length - 2].innerHTML;
    all_div_emojiBox[all_div_emojiBox.length - 2].innerHTML = copyDiv;
  };
  if (file) reader.readAsDataURL(file);
}

