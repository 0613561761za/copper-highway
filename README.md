## Copper Highway
#### A Co-Op VPN Service

Copper Highway is a SOF-only VPN service that uses a custom web application interface for the automated creation of user certificates and configuration files.  Copper Highway is an OpenVPN-based service.

##### Requirements
* OpenVPN Server
* NGINX
* PHP 5.6+
* SQLite3 w/ PDO Drivers
* GoAccess
* MaxMind's GeoIPLite2
* EasyRSA3
* OpenSSL

##### Special Permissions

###### Web Root
```bash 
$ cd copperhighway
$ chown -R ubuntu:www-data *
$ find . -type f -exec chmod 644 {} +
$ find . -type d -exec chmod 755 {} +
$ chmod 775 model/ ovpn/
$ chmod 664 model/data
$ chown root:www-data ovpn/make_unified.sh
$ chmod u+s ovpn/make_unified.sh
```

###### OpenVPN

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

-----

CopperHighway is written and maintained by [Austin](mailto:austin@copperhighway.org).