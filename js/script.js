let switch_span = document.querySelectorAll(".switch span");
let req_uri =
  location.pathname == "/Camagru/" || location.pathname == "/Camagru/index.php"
    ? "./"
    : "../";
setTimeout(() => {
  let success = document.getElementById("success");
  let failed = document.getElementById("failed");
  if (success != null) success.style = "display:none!important;";
  if (failed != null) failed.style = "display:none!important;";
}, 3000);
function display_signin() {
  switch_span[1].removeAttribute("id");
  switch_span[0].setAttribute("id", "active");
  document.getElementsByClassName("signin")[0].style =
    "display:flex!important;";
  document.getElementsByClassName("signup")[0].style =
    "display:none!important;";
  document.getElementsByClassName("reset_password")[0].style =
    "display:none!important;";
}
function display_signup() {
  switch_span[0].removeAttribute("id");
  switch_span[1].setAttribute("id", "active");
  document.getElementsByClassName("signin")[0].style =
    "display:none!important;";
  document.getElementsByClassName("signup")[0].style =
    "display:flex!important;";
  document.getElementsByClassName("reset_password")[0].style =
    "display:none!important;";
}
function display_reset_Password() {
  switch_span[1].removeAttribute("id");
  switch_span[0].setAttribute("id", "active");
  document.getElementsByClassName("signin")[0].style =
    "display:none!important;";
  document.getElementsByClassName("signup")[0].style =
    "display:none!important;";
  document.getElementsByClassName("reset_password")[0].style =
    "display:flex!important;";
}
function menu_click() {
  ul = document.querySelector("#second-menu");
  menu_icon = document.querySelector("#menu_icon");
  if (menu_icon.getAttribute("class") == "fas fa-bars") {
    menu_icon.setAttribute("class", "fas fa-times");
    ul.style = "display:block!important";
  } else {
    menu_icon.setAttribute("class", "fas fa-bars");
    ul.style = "display:none!important";
  }
}
function slide_click(control) {
  let slide = document.getElementsByClassName("slider-content")[0];
  let count_post = document.getElementsByClassName("post").length;
  if (control == "next") {
    let transform = slide.style.transform;
    let count_post_win = parseInt(window.innerWidth / 310);
    let diff = (count_post - count_post_win) * 310;
    if (
      transform &&
      parseInt(transform.split("translateX(")[1]) - 310 >= -diff
    ) {
      let translateX = parseInt(transform.split("translateX(")[1]);
      slide.style =
        "width:" +
        slide.style.width +
        ";transform:translateX(" +
        (translateX - 310) +
        "px);";
    } else if (!transform && diff - 310 >= 0)
      slide.style =
        "width:" + slide.style.width + ";transform:translateX(" + -310 + "px);";
  } else if (control == "previous") {
    let transform = slide.style.transform;
    if (transform && parseInt(transform.split("translateX(")[1]) + 310 <= 0) {
      let translateX = parseInt(transform.split("translateX(")[1]);
      slide.style =
        "width:" +
        slide.style.width +
        ";transform:translateX(" +
        (translateX + 310) +
        "px);";
    }
  }
}
function change_image() {
  let input = document.querySelector("input[name='fileinput']");
  let img = document.getElementById("Profile_image");
  let filereader = new FileReader();
  filereader.onload = function () {
    img.setAttribute("src", this.result);
  };
  filereader.readAsDataURL(input.files[0]);
}
function notificatioClick() {
  let box = document.querySelector(".notification-box");
  let icon = document.querySelector(".nt-active");
  if (
    box &&
    box.style &&
    (box.style.display == "" || box.style.display == "none")
  ) {
    if (icon) {
      icon.className = "d-none";
      let xhttp = new XMLHttpRequest();
      xhttp.open("GET", req_uri + "post/like_comment.php?not=1");
      xhttp.send();
    }
    box.style = "display:block!important";
  } else if (box) box.style.display = "none";
}
// ---------------------Full_Post------------------------
class Post {
  constructor(
    id,
    owner,
    img,
    img_owner,
    like_count,
    comment_count,
    like_color
  ) {
    this.id = id;
    this.owner = owner;
    this.img = img;
    this.img_owner = img_owner;
    this.like_count = like_count;
    this.comment_count = comment_count;
    this.like_color = like_color;
  }
}
function deletePost(id) {
  let xhttp = new XMLHttpRequest();
  xhttp.open("POST", req_uri + "post/delete_post.php");
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200)
      window.location = window.location.href;
  };
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("postid=" + id);
}
function displayDeleteBox(id) {
  let delete_box = document.querySelector("#" + id + " .delete_box");
  let post_box = document.querySelector("#" + id + " .post_box");
  if (delete_box.style.display == "flex") {
    delete_box.style = "display:none!important;";
    post_box.style = "";
  } else {
    delete_box.style =
      "display:flex!important;height:" + post_box.offsetHeight + "px;";
    post_box.style = "display:none!important;";
  }
}

function getPost(id_post) {
  let all_post = document.getElementsByClassName("post");
  let array_post = new Array(all_post.length);
  let i = 0;
  while (i < all_post.length) {
    let id = all_post[i].id;
    let owner = document.querySelector("#" + id + " #owner").innerHTML;
    let img = document.querySelector("#" + id + " .post_box > img").src;
    let img_owner = document.querySelector("#" + id + ' img[alt="Profile"]')
      .src;
    let like_count = document.querySelector("#" + id + " #like_txt").innerHTML;
    let comment_count = document.querySelector("#" + id + " #comment_txt")
      .innerHTML;
    let like_color = document.querySelector("#" + id + " .like_txt").style
      .color;
    like_count = like_count;
    array_post[i] = new Post(
      id,
      owner,
      img,
      img_owner,
      like_count,
      comment_count,
      like_color
    );
    i++;
  }
  let find = array_post.find((elem) => elem.id == id_post);
  i = array_post.indexOf(find);
  if (i > -1) {
    let post = document.querySelector("#" + id_post);
    document.getElementsByClassName("full_post")[0].style =
      "display:flex!important;max-height:650px;height:" +
      (window.innerHeight - 60) +
      "px;";
    document
      .getElementById("full_img_post")
      .setAttribute("src", array_post[i].img);
    document.getElementById("full_owner").innerHTML = array_post[i].owner;
    document
      .getElementById("full_img_owner")
      .setAttribute("src", array_post[i].img_owner);
    document.getElementById("full_like_count").innerHTML =
      array_post[i].like_count;
    document.getElementById("full_comment_count").innerHTML =
      array_post[i].comment_count;
    likeBox(array_post[i].like_color);
    getComment(id_post);
    if (array_post[i + 1]) {
      document.getElementById("full_next_post").style =
        "display:block!important";
      document
        .getElementById("full_next_post")
        .setAttribute("onclick", "getPost('" + array_post[i + 1].id + "')");
    } else
      document.getElementById("full_next_post").style =
        "color:#232323!important";
    if (array_post[i - 1]) {
      document.getElementById("full_previous_post").style =
        "display:block!important";
      document
        .getElementById("full_previous_post")
        .setAttribute("onclick", "getPost('" + array_post[i - 1].id + "')");
    } else
      document.getElementById("full_previous_post").style =
        "color:#232323!important";
    document
      .querySelector(".full_post input[type='submit']")
      .setAttribute("onclick", 'fullSentComment("' + id_post + '")');
    document
      .querySelector(".full_post .like_box")
      .setAttribute("onclick", 'like_click("' + id_post + '")');
  }
}
function getComment(id_post) {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200)
      if (this.response !== "error")
        document.getElementsByClassName(
          "comment_box"
        )[0].innerHTML = this.response;
  };
  xhttp.open("POST", req_uri + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("all_comment=true&postid=" + id_post);
}
function fullSentComment(id_post) {
  document.querySelector(
    "#" + id_post + " .comment_txt"
  ).value = document.querySelector(".full_post input[name='comment']").value;
  Comment_click(id_post);
  hide_show_comment(id_post);
  document.querySelector(".full_post input[name='comment']").value = "";
  getComment(id_post);
}
function likeBox(color) {
  document.querySelector(".full_post .like_txt").style = "color:" + color;
  document.querySelector(".full_post .fa-thumbs-up").style = "color:" + color;
  document.querySelector(".full_post .like_box").style =
    "padding: 5px;cursor: pointer;border: 1px solid " + color;
}
function deleteComment(id_comment, id_post) {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText.indexOf("<script>") > -1) window.location.reload();
      else getComment(id_post);
    }
    window.location.reload();
  };
  xhttp.open("POST", req_uri + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("submit=delete&commentid=" + id_comment);
}
function displayDeleteComment(id_comment) {
  let comment_box = document.querySelectorAll("#" + id_comment + " > div");
  if (
    comment_box[0].style.display == "" ||
    comment_box[0].style.display == "none"
  ) {
    comment_box[0].style = "display:flex!important";
    comment_box[1].style = "display:none!important";
    comment_box[2].style = "display:none!important";
  } else {
    comment_box[0].style = "display:none!important";
    comment_box[1].style = "display:block!important";
    comment_box[2].style = "display:flex!important";
  }
}
function hide_show_comment(id) {
  let post = document.getElementById(id);
  let comment = document.querySelector("#" + id + " .comment_text");
  post.style = "";
  if (comment.style.display == "") {
    comment.style = "display:block!important";
    post.style = "height: 325px;";
  } else if (comment.style.display == "block") comment.style.display = "";
}
function like_click(id) {
  let owner = document.querySelector("#" + id + " #owner");
  let like_btn = document.querySelector("#" + id + " .fa-thumbs-up");
  let like_txt_btn = document.querySelector("#" + id + " .like_txt");
  let like_txt = document.querySelector("#" + id + " #like_txt");
  let color = "";
  if (like_btn.style.color == "") color = "color:#167db9!important";
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    let response = parseInt(this.response.split("-")[1]);
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText.indexOf("<script>") > -1) window.location.reload();
      else if (this.responseText !== "error") {
        like_btn.style = color;
        like_txt_btn.style = color;
        likeBox(like_btn.style.color);
        like_txt.innerHTML = response + " like" + (response > 1 ? "s" : "");
        document.getElementById("full_like_count").innerHTML =
          like_txt.innerHTML;
      }
    }
  };
  xhttp.open("POST", req_uri + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("submit=like&postid=" + id + "&owner=" + owner.innerHTML);
}
function Comment_click(id) {
  let owner = document.querySelector("#" + id + " #owner");
  let comment_txt = document.querySelector("#" + id + " .comment_txt");
  let comment_count = document.querySelector("#" + id + " #comment_txt");
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    let response = parseInt(this.response.split("-")[1]);
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText.indexOf("<script>") > -1) window.location.reload();
      else if (this.responseText !== "error") {
        getComment(id);
        comment_count.innerHTML =
          response + " Comment" + (response > 1 ? "s" : "");
        document.getElementById("full_comment_count").innerHTML =
          comment_count.innerHTML;
      }
    }
  };
  xhttp.open("POST", req_uri + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send(
    "submit=comment&postid=" +
      id +
      "&owner=" +
      owner.innerHTML +
      "&content=" +
      comment_txt.value
  );
  comment_txt.value = "";
  hide_show_comment(id);
}
// ---------------------/Full_Post------------------------
