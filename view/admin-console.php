<?php if ( @!$d ) { $d = new UserHome(); }  ?>
<div id="admin">

    <h5>Admin Console</h5>
    
    <p>You're currently logged in as <?= $d->username ?>.</p>

    <fieldset class="inline">
	<legend>Approve</legend>
	<form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
	    <input type="text" name="uid" placeholder="UID" required>
	    <input type="hidden" name="referrer" value="approve-user">
	    <input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
	    <input type="submit" value="Execute">
	</form>
    </fieldset>

    <fieldset class="inline">
	<legend>Revoke</legend>
	<form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
	    <input type="text" name="uid" placeholder="UID" required>
	    <input type="hidden" name="referrer" value="revoke-user">
	    <input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
	    <input type="submit" value="Execute">
	</form>
    </fieldset>
    
    <fieldset>
	<legend>Manually Update Records</legend>
	<p class="note italic">Tip: If you manually update the revoked/approved fields, it will NOT send emails or actually revoke the certificate.  You'll have to manually revoke the cert if you don't use the form above.</p>
    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
	<label for="uid">For UID </label><input type="text" name="uid" pattern="[0-9]+" class="short" required>
	<label for="field"> set the field </label>
	<select name="field" required>
	    <option value="" selected></option>
	    <option value="clearance">clearance</option>
	    <option value="approved">approved</option>
	    <option value="cert_revoked">revoked</option>
	</select>
	<label for="value"> to </label>
	<input type="text" name="value" pattern="[0-2]+" title="Numbers 0, 1 or 2 only, please">
	<input type="hidden" name="referrer" value="update-record">
	<input type="hidden" name="csrf" value="<?= CSRF::makeToken() ?>">
	<input type="submit" value="Execute">
    </form>
    </fieldset>
    
    <?php $d->getUserList() ?>
    
</div>
