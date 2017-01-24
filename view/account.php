<div class="content center">
    <h3>My Account</h3>
    <form name="login" action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
	<input type="text" name="username" placeholder="username" autofocus required><br />
	<input type="password" name="password" placeholder="password" required><br />
	<input type="submit" value="Login">
        <input type="hidden" name="referrer" value="account">
        <input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
    </form>
    
    <p><a href="<?= $_SERVER["PHP_SELF"]; ?>?forgot-password">Forgot&nbsp;Password</a> | <a href="<?= $_SERVER["PHP_SELF"]; ?>?create-account">Create&nbsp;an&nbsp;Account</a></p>

</div>
