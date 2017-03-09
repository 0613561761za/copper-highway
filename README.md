# Copper Highway
*A Co-Op VPN Service*

Copper Highway is an invite-only VPN service that uses this web application interface for the automated and easy creation of user certificates and configuration files.  Copper Highway is an OpenVPN-based service, meaning users connect to the VPN using an OpenVPN-compatible app (OpenVPN Connect, TunnelBlick, etc) and their configuration file (which contains the requisite certificates and the user's private key).

*This is just the web application interface*.  The actual VPN is an OpenVPN Server instance, configured specifically to work with the web application.

![Screenshot](https://github.com/insdavm/copper-highway/raw/master/public/images/screenshot.png)

### Requirements
* OpenVPN Server
* NGINX
* PHP 5.6+
* SQLite3 w/ PDO Drivers
* GoAccess
* MaxMind's GeoIPLite2
* EasyRSA3
* OpenSSL

### Special Permissions

##### Web Root
```bash 
$ cd copperhighway
$ chown -R www-data:www-data *
$ find . -type f -exec chmod 644 {} +
$ find . -type d -exec chmod 755 {} +
$ chmod 700 model/ ovpn/
$ chmod 600 model/data
$ chown www-data:www-data ovpn/make_unified.sh
$ chmod u+s ovpn/make_unified.sh
```

##### OpenVPN

Server.conf:

```conf
user www-data
group www-data
```

EasyRSA Directory:

```bash
$ cd /etc/openvpn/easy-rsa/easyrsa3
$ sudo chown -R www-data:www-data
```

### Contributors

CopperHighway is written and currently maintained by [Austin](github.com/insdavm).  If you'd like to contribute to the web app, do it here on GitHub.  If you'd like to contribute in other ways (managing the server, pentesting, etc), e-mail me at [austin@copperhighway.org](mailto:austin@copperhighway.org).
