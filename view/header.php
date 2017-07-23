<!DOCTYPE html>
<html lang="en">
    <head>
	<title>Copper Highway</title>
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/modal.css">
	<script src="js/modal.js" type="text/javascript"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
    </head>
    <body>
	<div id="top-nav">
	    <div id="logo">
		<div id="title" class="bold">
		    Copper Highway
		</div>
	    </div>
	    <div id="navigation">
		<span id="home">
		    <a href="index.php" class="<?= Session::get('active_page') == "home" ? "active" : ""; ?>">
			HOME
		    </a>
		</span>
		<span id="about">
		    <a href="index.php?about" class="<?= Session::get('active_page') == "about" ? "active" : ""; ?>">
			ABOUT
		    </a>
		</span>
		<span id="account">
		    <a href="index.php?account" class="<?= Session::get('active_page') == "account" ? "active" : ""; ?>">
			<?= Authenticator::loggedIn() ? "DASHBOARD" : "LOGIN" ?>
		    </a>
		</span>
	    </div>
	</div>
	<div id="feedback"><?= Session::get('FEEDBACK') ? Session::get('FEEDBACK') : "" ?></div>
	
