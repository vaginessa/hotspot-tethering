<?php
if ($_POST['logout']=='logout') {
setcookie('user_name', '', time()-2592000,'/');
setcookie('pass_word', '', time()-2592000,'/');
die(0);
}
if ($_POST['username'] && $_POST['password']) {
setcookie('user_name', hash('sha512', $_POST['username']), time()+2592000,'/');
setcookie('pass_word', hash('sha512', $_POST['password']), time()+2592000,'/');
die(0);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* reset */
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, dl, dt, dd, ol, nav ul, nav li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video,input {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
	font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans", "wenquanyi micro hei", "Hiragino Sans GB", "Hiragino Sans GB W3", FontAwesome, sans-serif;}
article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
	display: block;
}
ol, ul {
	list-style: none;
	margin: 0px;
	padding: 0px;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after, q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}
/* start editing from here */
a {
	text-decoration: none;
}
img {
	max-width: 100%;
}
/*end reset*/
/****-----start-body----****/
body {
	background-color: #0091e6;
}
.main {
	margin: 5em auto 0;
	width: 22%;
}
h1 {
	font-size: 2.4em;
	padding-bottom: 28px;
	color: #fff;
	text-align: center;
}
h2 {
	font-size: 1.5em;
	padding-bottom: 28px;
	color: #0091e6;
	text-align: center;
}
.login {
	padding: 2em 0;
}
.inset {
	position: relative;
	background: #fff;
	padding: 2.5em;
	border-radius: 0.3em;
	-webkit-border-radius: 0.3em;
	-o-border-radius: 0.3em;
	-moz-border-radius: 0.3em;
	box-shadow: 0px 0px 15px #545454;
}
form span {
	display: block;
	font-size: 1.0em;
	color: #333;
	font-weight: 400;
}
input[type="text"], input[type="password"] {
	padding: 9px;
	width: 93.4%;
	font-size: 1.1em;
	margin: 3px 0px 25px;
	color: #666666;
	background: #f0f0f0;
	border: none;
	font-weight:400;
	outline: none;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-ms-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
	transition: all 0.3s ease-out;
	border-radius: 0.2em;
	-webkit-border-radius: 0.2em;
	-o-border-radius: 0.2em;
	-moz-border-radius: 0.2em;
}
input[type="text"]:hover, input[type="Password"]:hover, #active {
	border: none;
	outline: none;
}
.sign {
	padding: 10px 0 0;
	text-align: center;
}
.submit {
	margin-right:11px;
	background:#0091e6;
	border: none;
	outline: none;
	padding:8px 30px;
	cursor: pointer;
	color: #FFF;
	font-size: 0.9em;
	border-radius: .3em;
	-webkit-border-radius: .3em;
	-moz-border-radius: .3em;
	-o-border-radius: .3em;
	transition: 0.5s all;
	-webkit-transition: 0.5s all;
	-moz-transition: 0.5s all;
	-o-transition: 0.5s all;
	font-weight: 600;
	margin: 0 auto;
}
.submit:hover {
	background: #C5C5C5;
}
.copy-right {
	text-align: center;
	width: 97%;
	margin: 10em auto 0em;
}
.copy-right p {
	color: #FFF;
	font-size: 1em;
}
.copy-right p a {
	font-size: 1em;
	font-weight: 600;
	color: #1567A5;
	-webkit-transition: all 0.3s ease-out;
	-moz-transition: all 0.3s ease-out;
	-ms-transition: all 0.3s ease-out;
	-o-transition: all 0.3s ease-out;
	transition: all 0.3s ease-out;
}
.copy-right p a:hover {
	color: #fff;
}
/*----start-responsive design-----*/
@media (max-width:1440px) {
.main {
	width: 25%;
}
h1 {
	font-size: 2.2em;
}
.copy-right {
	margin: 5em auto 2em;
}
}
@media (max-width:1080px) {
.main {
	width: 31%;
	margin: 5em auto 0;
}
.copy-right {
	width: 97%;
	margin: 3em auto 1em;
}
}
@media (max-width:1024px) {
.main {
	width: 31%;
	margin: 5em auto 0;
}
.copy-right {
	width: 97%;
	margin: 3em auto 1em;
}
}
@media (max-width:991px) {
h1 {
	font-size: 2em;
}
}
@media (max-width:800px) {
.main {
	width: 39%;
}
}
@media (max-width:768px) {
.main {
	width: 42%;
	margin: 11em auto 0;
}
}
@media (max-width:640px) {
.main {
	width: 48%;
}
h1 {
	font-size: 2.1em;
}
}
@media (max-width:600px) {
h1 {
	font-size: 2em;
}
}
@media (max-width:480px) {
.main {
	width: 70%;
}
}
@media (max-width:320px) {
.main {
	width: 95%;
	margin: 1em auto 0;
}
.copy-right {
	margin: 1em auto 0;
}
.inset {
	padding: 2em;
}
h1 {
	font-size: 1.9em;
}
.login {
	padding: 2em 0 0;
}
}
</style>
</head>
<body>
<div class="main">
<div class="login">
<h1>管理系统</h1>
<div class="inset">
<div>
<h2>管理登录</h2>
<span><label>用户名</label></span>
<span><input type="text" class="textbox" id="username" name="username"></span>
</div>
<div>
<span><label>密码</label></span>
<span><input type="password" class="password" id="password" name="password"></span>
</div>
<div class="sign">
<input type="button" value="登录" onclick="login()" class="submit" />
</div>
</div>
</div>
</div>
<div class="copy-right">
<a href="" id="footer" style="color:#eee;font-size:16px"></a>
</div>
<script type="text/javascript">
function login(){ 
var a=document.getElementById("username").value;
var b=document.getElementById("password").value;
var xhttp=new XMLHttpRequest();
xhttp.onreadystatechange=function(){ 
if(this.readyState==4&&this.status==200){ 
window.location.href="./";
}
};
xhttp.open("POST","",true);
xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xhttp.send("username="+a+"&password="+b+"&number="+Math.random());
}
</script>
<script src="../js/footer.js"></script>
</body>
</html>