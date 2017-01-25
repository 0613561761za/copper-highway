<?php

$username = 'austin'; //Session::get("USERNAME");

/*
 * This utilized the X-Accel-Redirect Header from NGINX...
 * Apache uses X-Send-File which works kinda the same
 */
header("X-Accel-Redirect: /copperhighway/public/conf/$username.ovpn");

/*
 * Set the content type so that the browser knows how to render the data
 */
header("Content-Type: application/x-openvpn-profile");
header("Content-Disposition: attachment; filename=$username.ovpn");  

?>
