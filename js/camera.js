class Activity {
  constructor(id, width, top, left, img) {
    this.id = id;
    this.img = img;
    this.width = width;
    this.top = top;
    this.left = left;
  }
}
let post_info = document.getElementsByClassName("post_info")[0];
let gr_emoji = document.getElementsByClassName("gr_emoji")[0];
let camera = document.getElementsByClassName("camera")[0];
let imageCapture = document.getElementsByClassName("imageCapture")[0];
let image_capture = document.getElementById("image_capture");
let array_activity = Array();
let width = 720;
let height = 420;
window.addEventListener("resize", sizeOfVideo);
// window.addEventListener("visibilitychange", sizeOfVideo);
window.addEventListener("load", () => {
  let video = document.createElement("video");
  let canvas = document.getElementsByTagName("canvas")[0];
  let context = canvas.getContext("2d");
  let newimage = document.createElement("IMG");
  let capture = document.getElementById("capture");
  sizeOfVideo();
  navigator.mediaDevices
    .getUserMedia({
      audio: false,
      video: {
        width: 720,
        height: 480,
      },
    })
    .then((stream) => {
      video.srcObject = stream;
      video.play();
      setInterval(() => {
        context.drawImage(video, 0, 0, width, height);
        array_activity.forEach((val) => {
          newimage.setAttribute("src", val.img);
          newimage.width = val.width;
          newimage.height = val.width;
          context.drawImage(newimage, val.left, val.top, val.width, val.width);
        });
      }, 10);
    })
    .catch((error) => {
      console.log(error);
    });
  capture.addEventListener("click", () => {
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
// **************************/Activity****************************
function deleteActivity(id) {
  let emoji_activity = document.getElementsByClassName("emoji-activity")[0];
  array_activity.splice(id, 1);
  emoji_activity.innerHTML = "";
  resetIndex();
}
function editActivity(id) {
  let emoji_box = document.getElementsByClassName("box-emoji")[0];
  let boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  let left_input = document.querySelector("#leftEmoji input[type='range']");
  let top_input = document.querySelector("#topEmoji input[type='range']");
  let size_input = document.querySelector("#sizeEmoji input[type='range']");
  let emoji_image = document.querySelector(".box-emoji img");
  let img = document.querySelector(".box-emoji img");
  emoji_box.className =
    "box-emoji position-absolute d-flex flex-column justify-content-center";
  emoji_image.setAttribute("src", array_activity[id].img);
  img.width = array_activity[id].width;
  img.height = img.width;
  emoji_box.style =
    "left:" +
    array_activity[id].left +
    "px;top:" +
    array_activity[id].top +
    "px;";
  left_input.value = array_activity[id].left;
  top_input.value = array_activity[id].top;
  size_input.value = img.width;
  boxEditEmoji.style = "display:flex!important";
  changePositionEmoji("Left_1");
  changePositionEmoji("Top_1");
  deleteActivity(id);
}
function resetIndex() {
  let emoji_activity = document.getElementsByClassName("emoji-activity")[0];
  let i = 0;
  emoji_activity.innerHTML = "";
  array_activity.forEach((val) => {
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
}
// **************************/Activity****************************
function sizeOfVideo() {
  let canvas = document.getElementsByTagName("canvas")[0];
  let img = document.getElementById("image_capture");
  let slide = document.getElementsByClassName("slider-content")[0];
  let all_post = document.getElementsByClassName("post");
  let camera = document.getElementsByClassName("camera")[0];
  slide.style = "width:" + all_post.length * 310 + "px";
  changeSize(2);
  if (window.innerWidth < 720) {
    width = 320;
    height = 360;
  } else {
    width = 720;
    height = 480;
  }
  camera.style = "width:" + width + "px!important;";
  canvas.width = width;
  canvas.height = height;
  img.width = width;
  img.height = height;
}
// **************************Post****************************
function post_back() {
  post_info.className =
    "post_info d-none flex-column justify-content-center align-items-center";
  gr_emoji.className = "gr_emoji d-flex flex-row";
  gr_emoji.className = "gr_emoji d-flex flex-row";
  camera.className =
    "camera d-flex flex-column align-items-center justify-content-center";
  imageCapture.className = "imageCapture d-none";
  document.getElementsByClassName("slider")[0].className =
    "slider w-100 d-flex flex-column";
}
function post_sent() {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText == "Failed") {
        document.getElementById("failed").style = "display:block";
        document.getElementById("failed").innerHTML =
          "Image(JPG OR JPEG OR PNG OR GIF) And Size less than 10MB";
        setTimeout(() => {
          document.getElementById("failed").style = "display:none;";
        }, 3000);
      } else window.location = "create_post.php";
    }
  };
  xhttp.open("POST", "image.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send(
    "image_data=" + document.getElementById("image_capture").getAttribute("src")
  );
}
function uploadImage() {
  let file = document.getElementById("upload").files[0];
  let reader = new FileReader();
  reader.onload = (e) => {
    image_capture.setAttribute("src", e.target.result);
    post_info.className =
      "post_info d-flex flex-column justify-content-center align-items-center";
    gr_emoji.className = "gr_emoji d-none flex-row";
    gr_emoji.className = "gr_emoji d-none flex-row";
    camera.className =
      "camera d-none flex-column align-items-center justify-content-center";
    imageCapture.className = "imageCapture d-block";
  };
  reader.readAsDataURL(file);
}
// **************************/Post****************************
// **************************Emoji****************************

function selectEmoji() {
  let emoji_box = document.getElementsByClassName("box-emoji")[0];
  let boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  let emoji_image = document.querySelector(".box-emoji img");
  emoji_box.className =
    "box-emoji position-absolute d-flex flex-column justify-content-center";
  emoji_image.setAttribute("src", window.event.target.src);
  boxEditEmoji.style = "display:flex!important";
}
function saveEmoji() {
  let boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  let emoji_box = document.getElementsByClassName("box-emoji")[0];
  let img = document.querySelector(".box-emoji img");
  array_activity[array_activity.length] = new Activity(
    array_activity.length,
    img.width,
    emoji_box.offsetTop,
    emoji_box.offsetLeft,
    img.getAttribute("src")
  );
  emoji_box.className =
    "box-emoji position-absolute d-none flex-column justify-content-center";
  boxEditEmoji.style = "display:none!important";
  resetIndex();
}
function cancelEmoji() {
  let emoji_box = document.getElementsByClassName("box-emoji")[0];
  let boxEditEmoji = document.getElementsByClassName("boxEditEmoji")[0];
  boxEditEmoji.style = "display:none!important";
  emoji_box.className =
    "box-emoji position-absolute d-none flex-column justify-content-center";
}
function changeSize(id) {
  let input_range = document.querySelector("#sizeEmoji input[type='range']");
  let input_text = document.querySelector("#sizeEmoji input[type='text']");
  let img = document.querySelector(".box-emoji img");
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
  let box = document.querySelector(".box-emoji");
  let img = document.querySelector(".box-emoji img");
  if (id == "Left_1" || id == "Left_2") {
    let input_range = document.querySelector("#leftEmoji input[type='range']");
    let input_text = document.querySelector("#leftEmoji input[type='text']");
    if (id == "Left_1") {
      input_text.value = input_range.value;
      box.style =
        "left:" + input_text.value + "px;top:" + box.offsetTop + "px;";
    } else if (id == "Left_2") {
      input_range.value = input_text.value;
      box.style =
        "left:" + input_range.value + "px;top:" + box.offsetTop + "px;";
    }
    if (box.offsetLeft > width - img.width) {
      input_text.value = width - img.width;
      input_range.value = width - img.width;
      box.style =
        "left:" + (width - img.width) + "px;top:" + box.offsetTop + "px;";
    }
    if (box.offsetLeft < 0) {
      input_text.value = 0;
      input_range.value = 0;
      box.style = "left:" + 0 + "px;top:" + box.offsetTop + "px;";
    }
  } else if (id == "Top_1" || id == "Top_2") {
    let input_range = document.querySelector("#topEmoji input[type='range']");
    let input_text = document.querySelector("#topEmoji input[type='text']");
    if (id == "Top_1") {
      input_text.value = input_range.value;
      box.style =
        "left:" + box.offsetLeft + "px;top:" + input_text.value + "px;";
    } else if (id == "Top_2") {
      input_range.value = input_text.value;
      box.style =
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;";
    }
    if (box.offsetTop > height - img.width) {
      input_text.value = height - img.width;
      input_range.value = height - img.width;
      box.style =
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;";
    }
    if (box.offsetTop < 0) {
      input_text.value = 0;
      input_range.value = 0;
      box.style =
        "left:" + box.offsetLeft + "px;top:" + input_range.value + "px;";
    }
  }
}
function addNewEmoji() {
  let file = document.getElementById("fileinput").files[0];
  let reader = new FileReader();
  let emojiBox = document.getElementsByClassName("emojiBox")[0];
  let div = document.createElement("div");
  let img = document.createElement("img");
  reader.onload = (e) => {
    img.src = e.target.result;
    img.className = "emoji";
    img.setAttribute("onClick", "selectEmoji()");
    div.appendChild(img);
    emojiBox.prepend(div);
  };
  reader.readAsDataURL(file);
}

// **************************/Emoji****************************
