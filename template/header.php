<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/css/style.css" type="text/css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<title>Digital Flowers</title>
	<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<header>
	<div class="header_container">
		<div class="logo">
			<img src="/img/logo-xs.png">
		</div>
		<nav>
			<?php
				global $api;
				echo $api->getMenu();
			?>
			<!--
			<a href="/">HOME</a>
			<a href="/about/">ABOUT US</a>
			<a href="/contact/">CONTACT US</a>
			-->
		</nav>
		<div class="phone">
			<div class="phone_left">
				<i class="fas fa-phone"></i>
			</div>
			<div class="phone_right">
				<div class="phone_tip">Call us:</div>
				<div class="phone_num">(123) 456 78 90</div>
			</div>
		</div>
	</div>
</header>
<div class="content">
