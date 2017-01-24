<?php if ( @!$d ) { $d = new UserHome(); } ?>
<div class="content">
    <h5>Welcome back, <?= $d->username ?>!</h5>

    <fieldset>
	<legend>Account tools</legend>
	<a href="<?= $_SERVER["PHP_SELF"]; ?>?logout">Logout</a> | <a href="<?= $_SERVER["PHP_SELF"]; ?>?change-password">Change Password</a> | <a href="<?= $_SERVER["PHP_SELF"]; ?>?delete-account">Delete Account</a> 
    </fieldset>
    
    <fieldset>
	<legend>VPN Dashboard</legend>

	<?php if ( $d->approved == 1 ) { ?>
	    
	    <div class="badger"><span>Configuration </span><span class="<?= $d->badger_conf_color ?>"><?= $d->badger_conf_text ?></span></div>
	    <div class="badger"><span>Certificate </span><span class="<?= $d->badger_cert_color ?>"><?= $d->badger_cert_text ?></span></div>
	    <div class="badger"><span>Portland Server </span><span class="green">Normal</span></div>

	    <p></p>

	    <h6>Quick Start</h6>
	    <p class="note"><span class="italic">Tip: </span>You'll need to enter this password everytime you connect to the VPN service, so make sure it's something you'll remember.</p>
	    <div>
		<form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
		    <input type="password" name="password" placeholder="password" required autofocus>
		    <input type="password" name="password-repeat" placeholder="password (confirm)" required>		
		    <input type="hidden" name="referrer" value="create-cert">
		    <input type="submit" value="Create Certificate!">
		    <input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
		</form>
	    </div>
	    
	<?php } else { ?>

	    <h6>Pending Vetting</h6>
	    <p>Sorry, we're still waiting on your account to be verified.  An actual human has to do this so please be patient and check back regularly for updates.</p>

	<?php } ?>
	
    </fieldset>
    <p>Need <a href="<?= $_SERVER["PHP_SELF"]; ?>?getting-started">help</a>?
    <?php if ( $d->admin === 1 ) { ?>
	<a href="<?= $_SERVER["PHP_SELF"]; ?>?admin-console">Admin Console</a>
    <?php } ?>
    </p>
</div>
