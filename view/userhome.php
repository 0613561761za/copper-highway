<?php if ( @!$d ) { $d = new UserHome(); } ?>
<div class="content">
    <h5>Welcome back, <?= $d->username ?>!</h5>

    <fieldset>
	<legend>Account tools</legend>
	<a href="<?= $_SERVER["PHP_SELF"]; ?>?logout">Logout</a> | <a href="<?= $_SERVER["PHP_SELF"]; ?>?change-password">Change Password</a> | <a href="<?= $_SERVER["PHP_SELF"]; ?>?delete-account">Delete Account</a> | 

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display:inline;">
	    <input type="hidden" name="cmd" value="_s-xclick">
	    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
							 ">
	    <input type="image" style="background-color: rgba(0,0,0,0);border:none;display:inline;vertical-align:middle;" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>

    </fieldset>

    <fieldset>
	<legend>VPN Dashboard</legend>

	<?php if ( $d->approved == 1 ) { ?>
	    
	    <div class="badger"><span>Configuration </span><span class="<?= $d->badger_conf_color ?>"><?= $d->badger_conf_text ?></span></div>
	    <div class="badger"><span>Certificate </span><span class="<?= $d->badger_cert_color ?>"><?= $d->badger_cert_text ?></span></div>
            <div class="badger"><span>Portland&nbsp;Server </span><span class="green">Normal</span></div>

	    <p></p>

	    <?php if ( !$d->has_conf ) { ?>
		
		<h6>Create your Certificate</h6>
		<p class="note"><span class="italic">Tip: </span>You'll need to enter this password everytime you connect to the VPN service, so make sure it's something you'll remember.</p>
		<div>
		    <form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
			<input type="password" name="password" placeholder="password" autocomplete="off" required >
			<input type="password" name="password-repeat" placeholder="password (confirm)" autocomplete="off" required>		
			<input type="hidden" name="referrer" value="create-cert">
			<input type="submit" value="Create Certificate!">
			<input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
		    </form>
		</div>

	    <?php } else if ( $d->has_conf ) { ?>

		<h6>Get Connected!</h6>

		    <ol>
			<li>Download your <a href="<?= $_SERVER["PHP_SELF"] . '?download-configuration' ?>">configuration file</a></li>
			<li><?= $d->getOpenVPNLink() ?></li>
			<li>Open the OpenVPN app, and import your .ovpn configuration file.</li>
			<li>Click connect, enter your certificate password</li>
			<li>Surf securely!</li>
		    </ol>
		    <p></p>
		    <p><span class="italic">Remember: </span>A VPN can't protect you if it's not on!</p>

	    <?php } ?>
	    
	<?php } else { ?>

	    <h6>Pending Vetting</h6>
	    <p>Sorry, we're still waiting on your account to be verified.  An actual human has to do this so please be patient and check back regularly for updates.</p>

	<?php } ?>
	
    </fieldset>
    <p>Need <a href="<?= $_SERVER["PHP_SELF"]; ?>?getting-started">help</a>?
	<?php if ( $d->admin === 1 ) { ?>
	    <a href="<?= $_SERVER["PHP_SELF"]; ?>?admin-console">Admin Console</a> |
	    <a href="<?= $_SERVER["PHP_SELF"]; ?>?log">Software Log</a> |
	    <a href="<?= $_SERVER["PHP_SELF"]; ?>?goaccess" target="_blank">Server Log</a>
	<?php } ?>
    </p>
</div>
