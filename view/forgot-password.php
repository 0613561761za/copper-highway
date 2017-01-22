<div class="content center">
    <h3>Forgot Password</h3>
    
    <p class="note italic">A temporary password will be e-mailed to you.</p>
    
    <form name="forgot-password" action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
	<input type="text" name="username" placeholder="username" required>
	<input type="email" name="email" placeholder="your@email.com" required>
        <input type="submit" value="Request Reset">
        <input type="hidden" name="referrer" value="forgot-password">
	<!-- CSRF!! -->
    </form>
    
    <p><a href="<?= $_SERVER["PHP_SELF"]; ?>?account">My Account</a></p>

</div>
