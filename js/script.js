var switch_span = document.querySelectorAll(".switch span");
var url =
  location.pathname == "/" || location.pathname == "/index.php"
    ? "./"
    : "../";
setTimeout(function () {
  var success = document.getElementById("success");
  var failed = document.getElementById("failed");
  if (success != null)
    success.className = success.className.replace("d-flex", "d-none");
  if (failed != null)
    failed.className = failed.className.replace("d-flex", "d-none");
}, 3000);
function display_signin() {
  switch_span[1].removeAttribute("id");
  switch_span[0].setAttribute("id", "active");
  document.querySelector(".signin").className = document
    .querySelector(".signin")
    .className.replace("d-none", "d-flex");
  document.querySelector(".signup").className = document
    .querySelector(".signup")
    .className.replace("d-flex", "d-none");
  document.querySelector(".reset_password").className = document
    .querySelector(".reset_password")
    .className.replace("d-flex", "d-none");
}
function display_signup() {
  switch_span[0].removeAttribute("id");
  switch_span[1].setAttribute("id", "active");
  document.querySelector(".signin").className = document
    .querySelector(".signin")
    .className.replace("d-flex", "d-none");
  document.querySelector(".signup").className = document
    .querySelector(".signup")
    .className.replace("d-none", "d-flex");
  document.querySelector(".reset_password").className = document
    .querySelector(".reset_password")
    .className.replace("d-flex", "d-none");
}
function display_reset_Password() {
  switch_span[1].removeAttribute("id");
  switch_span[0].setAttribute("id", "active");
  document.querySelector(".signin").className = document
    .querySelector(".signin")
    .className.replace("d-flex", "d-none");
  document.querySelector(".signup").className = document
    .querySelector(".signup")
    .className.replace("d-flex", "d-none");
  document.querySelector(".reset_password").className = document
    .querySelector(".reset_password")
    .className.replace("d-none", "d-flex");
}
function menu_click() {
  ul = document.querySelector("#second-menu");
  menu_icon = document.querySelector("#menu_icon");
  if (menu_icon.getAttribute("class") == "fas fa-bars") {
    menu_icon.setAttribute("class", "fas fa-times");
    ul.style.display = "block";
  } else {
    menu_icon.setAttribute("class", "fas fa-bars");
    ul.style.display = "none";
  }
}
function slide_click(control) {
  var slide = document.getElementsByClassName("slider-content")[0];
  var count_post = document.getElementsByClassName("post").length;
  if (control == "next") {
    var transform = slide.style.transform;
    var count_post_win = parseInt(window.innerWidth / 310);
    var diff = (count_post - count_post_win) * 310;
    if (
      transform &&
      parseInt(transform.split("translateX(")[1]) - 310 >= -diff
    ) {
      var translateX = parseInt(transform.split("translateX(")[1]);
      slide.setAttribute(
        "style",
        "width:" +
          slide.style.width +
          ";transform:translateX(" +
          (translateX - 310) +
          "px);"
      );
    } else if (!transform && diff - 310 >= 0)
      slide.setAttribute(
        "style",
        "width:" + slide.style.width + ";transform:translateX(" + -310 + "px);"
      );
  } else if (control == "previous") {
    var transform = slide.style.transform;
    if (transform && parseInt(transform.split("translateX(")[1]) + 310 <= 0) {
      var translateX = parseInt(transform.split("translateX(")[1]);
      slide.setAttribute(
        "style",
        "width:" +
          slide.style.width +
          ";transform:translateX(" +
          (translateX + 310) +
          "px);"
      );
    }
  }
}
function change_image() {
  var input = document.querySelector("input[name='fileinput']");
  var img = document.getElementById("Profile_image");
  var filereader = new FileReader();
  filereader.onload = function () {
    img.setAttribute("src", this.result);
  };
  if (input.files[0]) filereader.readAsDataURL(input.files[0]);
}
function notificatioClick() {
  var box = document.querySelector(".notification-box");
  var icon = document.querySelector(".nt-active");
  if (
    box &&
    box.style &&
    (box.style.display == "" || box.style.display == "none")
  ) {
    if (icon) {
      icon.className = "d-none";
      var xhttp = new XMLHttpRequest();
      xhttp.open("GET", url + "post/like_comment.php?not=1");
      xhttp.send();
    }
    box.setAttribute("style", "display:block!important");
  } else if (box) box.style.display = "none";
}
function deletePost(id) {
  var xhttp = new XMLHttpRequest();
  xhttp.open("POST", url + "post/delete_post.php");
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200)
      window.location = window.location.href;
  };
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("postid=" + id);
}
function displayDeleteBox(id) {
  var delete_box = document.querySelector("#" + id + " .delete_box");
  var post_box = document.querySelector("#" + id + " .post_box");
  if (delete_box.style.display == "flex") {
    delete_box.setAttribute("style", "display:none!important;");
    post_box.setAttribute("style", "");
  } else {
    delete_box.setAttribute(
      "style",
      "display:flex!important;height:" + post_box.offsetHeight + "px;"
    );
    post_box.setAttribute("style", "display:none!important;");
  }
}

function findOtherPost(id_post, next) {
  id_next = null;
  var post = document.getElementsByClassName("post");
  for (var i = 0; i < post.length; i++) {
    if (post[i].id == id_post && i + next >= 0 && i + next < post.length)
      id_next = post[i + next].id;
  }
  return id_next;
}
function getPost(id_post) {
  var full_previous_post = document.getElementById("full_previous_post");
  var full_next_post = document.getElementById("full_next_post");
  document
    .getElementsByClassName("full_post")[0]
    .setAttribute(
      "style",
      "display:flex!important;max-height:650px;height:" +
        (window.innerHeight - 60) +
        "px;"
    );
  document
    .getElementById("full_img_post")
    .setAttribute(
      "src",
      document.querySelector("#" + id_post + " .post_box > img").src
    );
  document.getElementById("full_post_owner").innerHTML = document.querySelector(
    "#" + id_post + " #owner"
  ).innerHTML;
  document
    .getElementById("full_post_img_owner")
    .setAttribute(
      "src",
      document.querySelector("#" + id_post + ' img[alt="Profile"]').src
    );
  document.getElementById("full_like_count").innerHTML = document.querySelector(
    "#" + id_post + " #like_txt"
  ).innerHTML;
  document.getElementById(
    "full_comment_count"
  ).innerHTML = document.querySelector(
    "#" + id_post + " #comment_txt"
  ).innerHTML;
  likeBox(document.querySelector("#" + id_post + " .like_txt").style.color);
  getComment(id_post);
  if (findOtherPost(id_post, 1)) {
    full_next_post.setAttribute("style", "display:block!important");
    full_next_post.setAttribute(
      "onclick",
      "getPost('" + findOtherPost(id_post, 1) + "')"
    );
  } else full_next_post.setAttribute("style", "color:#232323!important");
  if (findOtherPost(id_post, -1)) {
    full_previous_post.setAttribute("style", "display:block!important");
    full_previous_post.setAttribute(
      "onclick",
      "getPost('" + findOtherPost(id_post, -1) + "')"
    );
  } else full_previous_post.setAttribute("style", "color:#232323!important");
  document
    .querySelector(".full_post input[type='submit']")
    .setAttribute("onclick", 'fullSentComment("' + id_post + '")');
  document
    .querySelector(".full_post .like_box")
    .setAttribute("onclick", 'like_click("' + id_post + '")');
}
function getComment(id_post) {
  var xhttp = new XMLHttpRequest();
  document.getElementsByClassName("comment_box")[0].innerHTML = "";
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200)
      if (this.responseText !== "error")
        document.getElementsByClassName(
          "comment_box"
        )[0].innerHTML = this.responseText;
  };
  xhttp.open("POST", url + "post/like_comment.php", true);
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
  document
    .querySelector(".full_post .like_txt")
    .setAttribute("style", "color:" + color);
  document
    .querySelector(".full_post .fa-thumbs-up")
    .setAttribute("style", "color:" + color);
  document
    .querySelector(".full_post .like_box")
    .setAttribute(
      "style",
      "padding: 5px;cursor: pointer;border: 1px solid " + color
    );
}
function deleteComment(id_comment, id_post) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) getComment(id_post);
    window.location.reload();
  };
  xhttp.open("POST", url + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("submit=delete&commentid=" + id_comment);
}
function displayDeleteComment(id_comment) {
  var comment_box = document.querySelectorAll("#" + id_comment + " > div");
  if (
    comment_box[0].style.display == "" ||
    comment_box[0].style.display == "none"
  ) {
    comment_box[0].setAttribute("style", "display:flex!important");
    comment_box[1].setAttribute("style", "display:none!important");
    comment_box[2].setAttribute("style", "display:none!important");
  } else {
    comment_box[0].setAttribute("style", "display:none!important");
    comment_box[1].setAttribute("style", "display:block!important");
    comment_box[2].setAttribute("style", "display:flex!important");
  }
}
function hide_show_comment(id) {
  var post = document.getElementById(id);
  var comment = document.querySelector("#" + id + " .comment_text");
  post.setAttribute("style", "");
  if (comment.style.display == "") {
    comment.setAttribute("style", "display:block!important");
    post.setAttribute("style", "height: 325px;");
  } else if (comment.style.display == "block") comment.style.display = "";
}
function like_click(id) {
  var owner = document.querySelector("#" + id + " #owner");
  var like_btn = document.querySelector("#" + id + " .fa-thumbs-up");
  var like_txt_btn = document.querySelector("#" + id + " .like_txt");
  var like_txt = document.querySelector("#" + id + " #like_txt");
  var color = "";
  if (like_btn.style.color == "") color = "color:#167db9!important";
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var response = parseInt(this.responseText.split("-")[1]);
      if (this.responseText !== "error" && this.responseText !== "") {
        like_btn.setAttribute("style", color);
        like_txt_btn.setAttribute("style", color);
        likeBox(like_btn.style.color);
        like_txt.innerHTML = response + " like" + (response > 1 ? "s" : "");
        document.getElementById("full_like_count").innerHTML =
          like_txt.innerHTML;
      }
    }
  };
  xhttp.open("POST", url + "post/like_comment.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("submit=like&postid=" + id + "&owner=" + owner.innerHTML);
}
function Comment_click(id) {
  var owner = document.querySelector("#" + id + " #owner");
  var comment_txt = document.querySelector("#" + id + " .comment_txt");
  var comment_count = document.querySelector("#" + id + " #comment_txt");
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    var response = parseInt(this.responseText.split("-")[1]);
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
  xhttp.open("POST", url + "post/like_comment.php", true);
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
