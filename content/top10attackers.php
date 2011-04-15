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

  $dbQuery = "select count(alert.rule_id) as count, src_ip from alert,signature where alert.rule_id = signature.rule_id and timestamp between $timePast and $timeNow and src_ip != 0 group by alert.rule_id order by count desc limit 10";
  $dbResult = mysql_query($dbQuery);
  $num=mysql_numrows($dbResult);
  $i=0;
  echo '<table class="sample"><tr><th>Count</th><th>Attacker</th></tr>';
  while ($i < $num) {
    $count=mysql_result($dbResult,$i,"count");
    $srcip=long2ip(mysql_result($dbResult,$i,"src_ip"));
	echo '<tr><td>' . $count . '</td><td><a href="http://whois.domaintools.com/' . $srcip . '">' . $srcip
 . '</a></td></tr>';
    $i++;
  }
  echo '</table>';

  mysql_close();
?>
