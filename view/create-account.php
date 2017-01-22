<div class="content center">
    <h3>Create an Account</h3>
    
    <p class="left note">Please use your real name&mdash;we'll use it to validate you.  Enter an e-mail address that you can access quickly, as you'll need it if you forget your password. <span class="italic">Do not use a work e-mail address.</span>  Your username will be used to generate your VPN client certificate, so choose something that doesn't personally identify you.</p>
    
    <form name="create-account" action="<?= $_SERVER["PHP_SELF"]; ?>" method="POST">
	<input type="text" name="first-name" placeholder="First Name" autofocus required><br />
	<input type="text" name="last-name" placeholder="Last Name" autofocus required><br />
	<input type="text" name="username" placeholder="Username" required><br />
	<input type="email" name="email" placeholder="your@email.com" required><br />
	<input type="password" name="password" placeholder="Password" required><br />
	<input type="password" name="password-repeat" placeholder="Password (confirm)" required><br />
	<input type="text" name="ref-code" placeholder="Ref Code (Optional)"><br />
	<input type="submit" value="Register">
    <input type="hidden" name="referrer" value="create-account">
	<!-- CSRF!! -->
    </form>
    
    <p><a href="<?= $_SERVER["PHP_SELF"]; ?>?account">My Account</a></p>

</div>
