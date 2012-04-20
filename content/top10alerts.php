<?
  include("settings.php");

  $secPeriods = array("1800", "3600", "10800", "43200",
			"86400", "604800", "2592000", "31536000");
  $namePeriods = array("30 minutes", "1 hour", "3 hours", "12 hours",
			"1 day", "7 days", "1 month", "1 year");
  $formPeriod = isset($_GET["formPeriod"]) ? $_GET["formPeriod"] : 3600;
  if (!in_array($formPeriod, $secPeriods)) {
    # Set default period - 1h
    $formPeriod = "3600"; 
  }
  $timeNow = time();
  $timePast = $timeNow - $formPeriod;
  mysql_connect($dbHost,$dbUser, $dbPass);
  @mysql_select_db($dbName) or die("Unable to connect to database");

  $dbQuery = "SELECT COUNT(alert.rule_id) AS count, description FROM alert,signature WHERE alert.rule_id = signature.rule_id AND timestamp BETWEEN $timePast AND $timeNow GROUP BY alert.rule_id ORDER BY count DESC LIMIT 10";
  $dbResult = mysql_query($dbQuery);
  $num=mysql_numrows($dbResult);
  $i=0;
  echo '<table class="sample"><tr><th>Count</th><th>Alert</th></tr>';
  while ($i < $num) {
    $count=mysql_result($dbResult,$i,"count");
    $description=mysql_result($dbResult,$i,"description");
	echo '<tr><td>' . $count . '</td><td>' . $description . '</td></tr>';
    $i++;
  }
  echo '</table>';

  mysql_close();

?>
