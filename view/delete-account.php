<div class="content center">
    <h3>Delete Account</h3>
    
    <p><span class="bold">&#9888; Warning</span><br />This will delete your CopperHighway.org web account, your VPN profile, and will REVOKE your VPN certificate!  You will no longer be able to logon to CopperHighway.org or connect to the VPN service.</p>
    
    <form name="delete-account" action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
	<input type="text" name="username" placeholder="username" autofocus required><br />
	<input type="password" name="password" placeholder="password" required><br />
        <input type="submit" value="Delete Forever" onClick="return confirm('Are you absolutely sure?')">
        <input type="hidden" name="referrer" value="delete-account">
	<input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
    </form>
</div>
