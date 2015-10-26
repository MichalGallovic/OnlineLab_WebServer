<?php 
require_once('config.php');
?>
<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ONLINE LABORATORY MANAGER | login</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="Author" content="" />
	<meta name="Generator" content="" />
	<meta name="Copyright" content="" />
	<meta name="Robots" content="ALL,FOLLOW" />
	<meta name="Resource-Type" content="document" />
	<meta http-equiv="Content-Language" content="sk" />
	<meta http-equiv="Cache-Control" content="Public" />
	
	<link href="css/default.css" rel="stylesheet" type="text/css" />
	<style  type="text/css" />
		#login{
			margin-left:auto;
			margin-right:auto;
			margin-top:7em;
			margin-bottom:7em;
			width:320px;
		}
		
		form{
			font-weight: normal;
			-moz-border-radius: 3px;
			-khtml-border-radius: 3px;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			background: #fff;
			border: 1px solid #e5e5e5;
			-moz-box-shadow: rgba(200, 200, 200, 0.7) 0px 4px 10px -1px;
			-webkit-box-shadow: rgba(200, 200, 200, 0.7) 0px 4px 10px -1px;
			-khtml-box-shadow: rgba(200, 200, 200, 0.7) 0px 4px 10px -1px;
			box-shadow: rgba(200, 200, 200, 0.7) 0px 4px 10px -1px;
			padding: 26px 24px 46px;
		}
		
		form .input{
			font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
			font-weight: 200;
			font-size: 24px;
			width: 97%;
			padding: 3px;
			margin-top: 2px;
			margin-right: 6px;
			margin-bottom: 16px;
			border: 1px solid #e5e5e5;
			background: #fbfbfb;
			outline: none;
			-moz-box-shadow: inset 1px 1px 2px rgba(200, 200, 200, 0.2);
			-webkit-box-shadow: inset 1px 1px 2px rgba(200, 200, 200, 0.2);
			box-shadow: inset 1px 1px 2px rgba(200, 200, 200, 0.2);
			color:#555555;
		}
		
		h1 {}
		h1 a{
			background:url(images/logo.png) top center no-repeat;
			overflow:hidden;
			text-indent:-9999px;
			display:block;
			height:128px;
			padding-bottom:15px;
		}
		
		label{ color: #777777;font-size: 14px;}
		.button_login{
			background:url(images/login.jpg) top left no-repeat;
			border:none;
			width:34px;
			height:32px;
			cursor:pointer;
		}
		
	</style>
</head>

<body>
	<div id="login">
		<h1><a href="" title="ONLINE LABORATORY MANAGER">ONLINE LABORATORY MANAGER</a></h1>
		<form id="" enctype="multipart/form-data" method="post" name=""  action=""  >
			<p>
				<label><?= $fields['username']; ?><br>
				<input type="text"   value="" class="input" id="user_login" name="login"></label>
			</p>
			<p>
				<label><?= $fields['username']; ?><br>
				<input type="password"   value="" class="input" id="user_pass" name="pass"></label>
			</p>
			<p>
				<input type="checkbox" name="autologin" id="autologin" value="1">
				<label for="autologin" >Remeber me</label>
			</p>
			<p class="t_center">
				<input type="submit"  value="" class="button_login" id="" name="	submit">
				<input type="hidden" value="1" name="testcookie">
				<input type="hidden" value="1" name="login-atempt">
			</p>
		</form>
	</div>
</body>

</html>
