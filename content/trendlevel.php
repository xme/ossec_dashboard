<?
  include("settings.inc");

  $secPeriods = array("1800", "3600", "10800", "43200",
			"86400", "604800", "2592000", "31536000");
  $namePeriods = array("30 minutes", "1 hour", "3 hours", "12 hours",
			"1 day", "7 days", "1 month", "1 year");
  $formPeriod = $_GET["formPeriod"];
  if (!in_array($formPeriod, $secPeriods)) {
    # Set default period - 1h
    $formPeriod = "3600"; 
  }
  $timeNow = time();
  $timePast = $timeNow - $formPeriod;
  mysql_connect($dbHost,$dbUser, $dbPass);
  @mysql_select_db($dbName) or die("Unable to connect to database");

  $dbQuery = "SELECT AVG(level) AS average FROM alert,signature WHERE alert.rule_id = signature.rule_id AND timestamp BETWEEN $timePast AND $timeNow";
  $dbResult = mysql_query($dbQuery);
  $currentAverage=round(mysql_result($dbResult,0,"average"), 2);

  $timeNow = $timePast;
  $timePast = $timePast - $formPeriod;
  $dbQuery = "SELECT AVG(level) AS average FROM alert,signature WHERE alert.rule_id = signature.rule_id AND timestamp BETWEEN $timePast AND $timeNow";
  $dbResult = mysql_query($dbQuery);
  $oldAverage=round(mysql_result($dbResult,0,"average"), 2);

  echo '<table class="sample" width="100%"><tr valign="middle"><tr>';
  echo '<td>';
  if ($oldAverage < $currentAverage) {
	echo '<img src="content/red-arrow.gif">';
  } elseif ($oldAverage > $currentAverage) {
	echo '<img src="content/green-arrow.gif">';
  } else {
	echo '<img src="content/yellow-arrow.gif">';
  }
  echo '</td><td>';
  echo 'Current Alert Level: ' . $currentAverage . '<br>Previous Period: ' . $oldAverage ;
  echo '</td></tr></table>';
  mysql_close();
?>
