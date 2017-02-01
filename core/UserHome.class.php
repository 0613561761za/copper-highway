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

spl_autoload_register(function($class) {
    require __DIR__ . "/../core/" . $class . ".class.php";
});

class UserHome
{
    public $username;
    public $last_logon;
    public $approved = 0;
    public $conf_path;
    public $cert_revoked;
    public $has_conf = FALSE;

    public $badger_conf_color;
    public $badger_conf_text;
    public $badger_cert_color;
    public $badger_cert_text;

    public $admin = 0;
    
    public function __construct()
    {
        /* get some details about the user */
        $this->username = Session::get("USERNAME");
        $data = DatabaseFactory::quickQuery("SELECT uid, last_logon, approved, conf_path, cert_revoked, clearance FROM users WHERE username='$this->username'");
        $data = $data->fetch(PDO::FETCH_ASSOC);
        $this->last_logon = gmdate('M d, Y', $data['last_logon']);
        $this->approved = $data['approved'];
        $this->conf_path = $data['conf_path'];
        $this->cert_revoked = $data['cert_revoked'];

        /* determine if user is an admin */
        if ( $data['clearance'] == 2 ) {
            $this->admin = 1;
        }

        /* configuration badge */
        if ( empty($this->conf_path) ) {
            $this->badger_conf_color = 'red';
            $this->badger_conf_text = 'Not Found';
        } else if ( !empty($this->conf_path) ) {
            $this->badger_conf_color = 'green';
            $this->badger_conf_text = '<a href="' . $_SERVER["PHP_SELF"] . '?download-configuration">Download</a>';
            $this->has_conf = TRUE;
        }

        /* certificate badge */
        if ( !empty($this->cert_revoked) && $this->revoked == 1 ) {
            $this->badger_cert_color = 'red';
            $this->badger_cert_text = 'Revoked';
        } else if ( empty($this->cert_revoked) && !empty($this->conf_path) ) {
            $this->badger_cert_color = 'green';
            $this->badger_cert_text = 'Valid';
        } else {
            $this->badger_cert_color = 'amber';
            $this->badger_cert_text = 'Not Found';
        }
    }

    public function getOpenVPNLink()
    {
        $u = $_SERVER["HTTP_USER_AGENT"];
        
        if ( preg_match('/windows|win/i', $u) ) {
            return 'Download <a href="https://swupdate.openvpn.org/community/releases/openvpn-install-2.4.0-I601.exe" target="_blank">OpenVPN GUI for Windows <img src="images/new_window.png"></a>';
        } else if ( preg_match('/mac/i', $u) && !preg_match('/iphone|ipod|ipad/i', $u) ) {
            return 'Download <a href="https://tunnelblick.net/release/Tunnelblick_3.6.10_build_4760.dmg" target="_blank">OpenVPN for OSX <img src="images/new_window.png"></a>';
        } else if ( preg_match('/linux|ubuntu/i', $u) && !preg_match('/android/i', $u) && !preg_match('/tizen/i', $u) ) {
            return 'Run <code>sudo apt install network-manager-openvpn</code> <span class="italic">or</span> search <a href="https://www.google.com/#q=OpenVPN+client+linux" target="_blank">Google: OpenVPN client linux <img src="images/new_window.png"></a>';
        } else if ( preg_match('/iphone|ipod|ipad/i', $u) ) {
            return 'Download <a href="https://itunes.apple.com/us/app/openvpn-connect/id590379981?mt=8" target="_blank">OpenVPN for iOS <img src="images/new_window.png"></a>';
        } else if ( preg_match('/android/i', $u) ) {
            return 'Download <a href="https://play.google.com/store/apps/details?id=net.openvpn.openvpn&hl=en" target="_blank">OpenVPN for Android <img src="images/new_window.png"></a>';
        } else {
            return 'Search <a href="https://www.google.com/#q=OpenVPN+Client" target="_blank">Google: OpenVPN Client <img src="images/new_window.png"></a> (we couldn\'t detect your OS).';
        }
    }

    /* 
     * I'm not sure why this is here.... it really should
     * be in it's own class like AdminConsole.class.php.
     */
    public function getUserList()
    {
        $sql = "SELECT * FROM users ORDER BY account_creation_date ASC";
        $stmt = DatabaseFactory::quickQuery($sql);
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo <<<EOT
<table id="admin">
<thead>
<tr>
<th>UID</th>
<th>First</th>
<th>Last</th>
<th>Username</th>
<th>Account Created</th>
<th>Ref Code</th>
<th>Last Logon</th>
<th>Clearance</th>
<th>Conf File</th>
<th>Approved</th>
<th>Revoked</th>
</tr>
</thead>
<tbody>
EOT;
        
        foreach ( $stmt as $index=>$row ) {
            echo "<tr>\n";
            echo "<td>" . $row["uid"] . "</td>\n";
            echo "<td>" . $row["first_name"] . "</td>\n";
            echo "<td>" . $row["last_name"] . "</td>\n";
            echo "<td>" . $row["username"] . "</td>\n";
            echo "<td>" . gmdate('d M y H:i', $row["account_creation_date"]) . "</td>\n";
            echo "<td>" . $row["ref_code"] . "</td>\n";
            echo "<td>" . gmdate('d M y H:i', $row["last_logon"]) . "</td>\n";
            echo "<td>" . $row["clearance"] . "</td>\n";
            echo "<td>" . $row["conf_path"] . "</td>\n";
            echo "<td>" . $row["approved"] . "</td>\n";
            echo "<td>" . $row["cert_revoked"] . "</td>\n";
            echo "</tr>\n";
        }

        echo "</tbody>\n</table>\n";
    }
}

/* EOF */