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

    public $badger_conf_color;
    public $badger_conf_text;
    public $badger_cert_color;
    public $badger_cert_text;

    public $admin = 0;
    
    public function __construct()
    {
        /* get some details about the user */
        $this->username = Session::get("USERNAME");
        $data = DatabaseFactory::quickQuery("SELECT uid, last_logon, approved, conf_path, cert_revoked FROM users WHERE username='$this->username'");
        $data = $data->fetch(PDO::FETCH_ASSOC);
        $this->last_logon = gmdate('M d, Y', $data['last_logon']);
        $this->approved = $data['approved'];
        $this->conf_path = $data['conf_path'];
        $this->cert_revoked = $data['cert_revoked'];

        /* determine if user is an admin */
        if ( $data['uid'] == 1 ) {
            $this->admin = 1;
        }

        /* configuration badge */
        if ( empty($this->conf_path) ) {
            $this->badger_conf_color = 'red';
            $this->badger_conf_text = 'Not Found';
        } else if ( !empty($this->conf_path) ) {
            $this->badger_conf_color = 'green';
            $this->badger_conf_text = '<a href="' . $_SERVER["PHP_SELF"] . '?download_configuration">Download</a>';
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

    /* 
     * This function prints data to the screen!!
     * 
     * Use appropriately
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