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
		<span id="home"><a href="index.php">Home</a></span>
		<span id="about"><a href="index.php?about">About</a></span>

		<?php if ( Authenticator::loggedIn() ) { ?>
		<span id="contribute"><a href="index.php?contribute">Contribute</a></span>
		<?php } ?>

                <span id="account"><a href="index.php?account"><?= Authenticator::loggedIn() ? ucfirst(strtolower(Session::get('USERNAME'))) . "'s" : "My" ?>&nbsp;Account</a></span>
	    </div>
	</div>
    <div id="feedback"><?= Session::get('FEEDBACK') ? Session::get('FEEDBACK') : "" ?></div>

