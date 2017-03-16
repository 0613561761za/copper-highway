
<?php if ( @!$d ) { $d = new UserHome(); } ?>
<div class="content">
    <h3 class="gray">Welcome back, <?= $d->username ?>!</h3>
    <div class="subnav">
    
                       <a href="<?= $_SERVER["PHP_SELF"]; ?>?logout">Logout</a> &mdash; <a href="<?= $_SERVER["PHP_SELF"]; ?>?change-password">Change&nbsp;Password</a> &mdash; <a href="<?= $_SERVER["PHP_SELF"]; ?>?delete-account">Delete&nbsp;Account</a>

    </div>
                       
	<?php if ( $d->approved == 1 ) { ?>
	    
	    <?php if ( !$d->has_conf ) { ?>
		
		<h5>Create your Certificate</h5>
		<p class="note"><span class="italic">Tip: </span>The password you enter here will be used to encrypt your private key.  You'll need to enter it whenever you connect to the VPN service.  This password is separate from and has no bearing on your copperhighway.org password account.</p>
		<div>
		    <form action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
			<input type="password" name="password" placeholder="new password" autocomplete="off" required >
			<input type="password" name="password-repeat" placeholder="new password (confirm)" autocomplete="off" required>		
			<input type="hidden" name="referrer" value="create-cert">
			<input type="submit" value="Create Certificate!">
			<input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
		    </form>
		</div>

	    <?php } else if ( $d->has_conf ) { ?>

		<h4>You're Good to Go</h4>

		    <ol>
			<li>Download your <a href="<?= $_SERVER["PHP_SELF"] . '?download-configuration' ?>">configuration file</a></li>
			<li><?= $d->getOpenVPNLink() ?></li>
			<li>Import <span class="bold"><?= $d->username; ?>.ovpn</span> into your OpenVPN client</li>
			<li>Enter your certificate password and click connect</li>
		    </ol>

	    <?php } ?>
	    
	<?php } else { ?>

	    <h5 class="gray">Pending Vetting</h5>
	    <p>Your account has been sent to our vetting team for approval.  You'll receive an e-mail when you're account has been verified.</p>
	    <p>
		After your account has been verified, you'll be able to login and create your VPN configuration file for use with any OpenVPN client.
		For additional information on how this VPN service works, check out our <a href="<?= $_SERVER["PHP_SELF"]; ?>?getting-started">user guide</a>.
	    </p>

	<?php } ?>

	<p></p>
	
	<div>
	    Need <a href="<?= $_SERVER["PHP_SELF"]; ?>?getting-started">help</a>?

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" id="paypal">
	    <input type="hidden" name="cmd" value="_s-xclick">
	    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
							 ">
	    <input type="image" src="images/donate.png" border="0" name="submit" alt="Donate">
	    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>

	</div>
	
	<div class="subnav">
	    <?php if ( $d->admin === 1 ) { ?>
		<a href="<?= $_SERVER["PHP_SELF"]; ?>?admin-console">Admin Console</a> &mdash;
		<a href="<?= $_SERVER["PHP_SELF"]; ?>?log">Software Log</a> &mdash;
		<a href="<?= $_SERVER["PHP_SELF"]; ?>?goaccess" target="_blank">Server Log</a>
	    <?php } ?>
	</div>

</div>
