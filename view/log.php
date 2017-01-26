<?php

/**
 * Log (dot) php
 *
 * Dumps log data in a sick ass format
 * @author Austin <insdavm@gmail.com>
 */

/**
 * Auto load any classes we need
 */
spl_autoload_register(function($class) {
    require_once __DIR__ . '/../core/' . $class . '.class.php';
});


/**
 * Define a function to get a fancy time
 *
 * @param int $time UNIX timestamp
 * @return string $fancytime formatted DTG or "xx seconds/minutes/hours ago"
 */
function fancyTime($time)
{
    $diff = time() - $time;

    switch (true) {
	case $diff < 60:
	    return (int) $diff . ' seconds ago';
	    break;
	case $diff >= 60 && $diff < 3600:
	    return (int) ($diff/60) . ' minutes ago';
	    break;
	case $diff >= 3600 && $diff < 86400:
	    return (int) ($diff/3600) . ' hours ago';
	    break;
	case $diff >= 86400:
	default:
	    return (int) ($diff/86400) . ' days ago';
    }
}

/**
 * Fetch our data
 */
$db = DatabaseFactory::getFactory()->getConnection();
$sql = "SELECT * FROM log ORDER BY id DESC";
$stmt = $db->query($sql);
$all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

    <script src="js/sorttable.js"></script>
    <style type="text/css">
     table {
       font-size: 0.8rem;
       border-spacing: 0;
       width: 100%
     }

     table thead {
       cursor: pointer;
       background-color: #DDD;
     }

     table tbody tr:hover {background-color: #EEE;}
     table tbody tr td {padding: 0px 5px;}
     abbr {cursor: help}
    </style>
    <div id="admin">
      <h1>Log Messages</h1>
      <h3>You are logged in as <em><?= Session::get("USERNAME"); ?></em>.</h3>
      <table class="sortable">
	<thead>
	  <tr>
	    <th>ID</th>
	    <th>Type</th>
	    <th>Message</th>
	    <th>Username</th>
	    <th>When</th>
	  </tr>
	</thead>
	<tbody>
	  <?php

	  /**
	   * Loop through the rows and print them as an HTML table's row
	   */
	  foreach ($all_messages as $row) {
	      echo '<tr>';
	      echo '<td>' . $row['id'] . '</td>';
	      echo '<td>' . $row['type'] . '</td>';
	      echo '<td>' . $row['message'] . '</td>';
	      echo '<td>' . $row['username'] . '</td>';
	      echo '<td><abbr title="' . strtoupper(gmdate('d hi\Z MY', $row['time']))  . '">' . fancyTime($row['time']) . '</abbr></td>';
	      echo '</tr>';
	  }
	  
	  ?>
	</tbody>
      </table>
    </div>
