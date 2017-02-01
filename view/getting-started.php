<div class="content">

    <h3>Getting Started</h3>
    <ol>
	<li><a href="#how-it-works">How it works</a></li>
	<li><a href="#creating-a-certificate">Creating a certificate</a></li>
	<li><a href="#software">Downloading the client software</a></li>
	<li><a href="#connecting">Connecting</a></li>
    </ol>
    <p></p>
    <h5 id="how-it-works">How it Works</h5>
    <p>A VPN works by creating a secure connection between your computer and the VPN server.  While you're connected, your computer routes all of its internet traffic through the VPN server (this includes traffic from your web browser, your iTunes software, or your Torrent client, for example).</p>
    <p>Without a VPN, your network traffic is clearly visible to anybody connected to the same local network that you are (like the wired ethernet connection in a hotel or the Wifi in a restaurant).  While you're connected to a VPN, this same traffic is transformed into encrypted nonsense, and an observer would only be able to figure out the address and location of the VPN server, but not the websites or services you're connecting to inside the VPN's encrypted tunnel.</p>
    <p>Many tutorials or articles on VPNs will distract you by focusing on the original use for VPNs, which is to enable access to private network resources.  We're use this VPN purely for the security it provides.</p>

    <h5 id="creating-a-certificate">Creating a certificate</h5>
    <p>Copper Highway authenticates its users by using X509 Public Key Infrastructure (PKI), meaning each user is authenticated with a unique certificate that only he or she has a copy of.  In order to connect to the VPN service on your computer, phone, or tablet, you need two things:
	<ol>
	    <li>Client software (<span class="italic">see <a href="#software">Downloading the client software</a></span>)</li>
	    <li>A certificate</li>
	</ol>
    </p>
    <p>Creating a certificate for your use with Copper Highway is easy to do from your <a href="<?= $_SERVER["PHP_SELF"]; ?>?account">account page</a>.  If you've already created a certificate, you'll see an option to download your configuration file.  Your configuration file will work on all of your devices.</p>
    <p>If you're an advanced user and you'd prefer to generate your own private key, you can send us a Certificate Signing Request (CSR) and in return we'll send you your certificate.  If you're an OSX or Linux user, just install <code>openssl</code> and run the following:</p>
    <code class="block">$ openssl req -newkey rsa:2048 -nodes -keyout username.key -out username.csr</code>
    <p class="note">(just make sure you replace "username" with your Copper Highway username)</p>

    <h5 id="software">Downloading the Software</h5>
    
    <h6>Desktop</h6>
    <p>Windows XP or later:
	<ul>
	    <li><a href="https://swupdate.openvpn.org/community/releases/openvpn-install-2.4.0-I601.exe" target="_blank">OpenVPN-GUI Download<img src="images/new_window.png"></a></li>
	    <li><a href="https://openvpn.net/index.php/access-server/docs/admin-guides-sp-859543150/howto-connect-client-configuration/395-how-to-install-the-openvpn-client-on-windows.html" target="_blank">How-to install OpenVPN Client<img src="images/new_window.png"></a></li>
	</ul>
    </p>
    <p>OSX: <a href="https://tunnelblick.net/downloads.html" target="_blank">TunnelBlick<img src="images/new_window.png"></a></p>
    <p>Linux: Check your package manager or have a look at the <a href="https://community.openvpn.net/openvpn/wiki/OpenvpnSoftwareRepos" target="_blank">OpenVPN Debian and Ubuntu Repos<img src="images/new_window.png"></a></p>

    <h6>Mobile</h6>
    <p>iOS: <a href="https://itunes.apple.com/us/app/openvpn-connect/id590379981?mt=8" target="_blank">OpenVPN Connect (App Store)<img src="images/new_window.png"></a></p>
    <p>Android: <a href="https://play.google.com/store/apps/details?id=net.openvpn.openvpn&hl=en" target="_blank">OpenVPN Connect (Google Play)<img src="images/new_window.png"></a></p>
    
    <h5 id="connecting">Connecting</h5>
    <p>Once you've created your unified configuration file and downloaded it to your computer, you should store it in a safe place on your hard drive or phone's SD card.  Then, simply open up your client software (OpenVPN Connect, Tunnelblick, etc.) and find the "Import" menu item.  Find your configuration file (<span class="italic">something.ovpn</span>) and select it.  Once it's loaded into the client software, you just have to click "Connect"!</p>
    
</div>
