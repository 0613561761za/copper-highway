<?php

/**
 * Copper Highway
 * 
 * A VPN service co-op
 * 
 * @author Austin <austin@copperhighway.org>
 * @version 1.0
 * @date 2017.01.21
 */

$username = Session::get("USERNAME");
header("X-Accel-Redirect: /conf/$username.ovpn");
header("Content-Type: application/x-openvpn-profile");
header("Content-Disposition: attachment; filename=$username.ovpn");  

?>
