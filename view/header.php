<!DOCTYPE html>
<html lang="en">
    <head>
	<title>Copper Highway</title>
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet"  href="css/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
    </head>
    <body>
	<div id="top-nav">
	    <div id="logo">
		<div id="title" class="bold">Copper Highway</div>
	    </div>
	    <div id="navigation">
		<span id="home"><img src="images/home.png"><a href="index.php">Home</a></span>
		<span id="about"><img src="images/info.png"><a href="index.php?about">About</a></span>

		<?php if ( Authenticator::loggedIn() ) { ?>
		    <span id="contribute"><img src="images/git.png"><a href="index.php?contribute">Contribute</a></span>
		<?php } ?>

                <span id="account"><img src="images/shield.png"><a href="index.php?account"><?= Authenticator::loggedIn() ? "Dashboard" : "Login" ?></a></span>
	    </div>
	</div>
    <div id="feedback"><?= Session::get('FEEDBACK') ? Session::get('FEEDBACK') : "" ?></div>

