<div class="content center">
    <h3>Change Password</h3>
    
    <p class="note italic">Your password will be changed immediately.  Use it the next time you logon.</p>
    
    <form name="change-password" action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
	<input type="password" name="password" placeholder="password" autofocus required><br />
	<input type="password" name="password-repeat" placeholder="password (confirm)" required><br />
        <input type="submit" value="Change">
        <input type="hidden" name="referrer" value="change-password">
	<input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
    </form>
</div>
