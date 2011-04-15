<?
  include("settings.inc");
  include("geoipcity.inc");
  include("geoipregionvars.php");
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

  $gi = geoip_open("content/GeoLiteCity.dat",GEOIP_STANDARD);

  $dbQuery = "select src_ip from alert,signature where src_ip != 0 and timestamp between $timePast and $timeNow and alert.rule_id = signature.rule_id";
  $dbResult = mysql_query($dbQuery);
  $num=mysql_numrows($dbResult);
  $i=0;
  while ($i < $num) {
    $srcip=long2ip(mysql_result($dbResult,$i,"src_ip"));
    $record = geoip_record_by_addr($gi,$srcip);
    $country[$record->country_name]++;
    $i++;
  }
  arsort($country);
  $i=0;
  echo '<table class="sample"><tr><th>Count</th><th>Country</th></tr>';
  foreach($country as $key => $val)
  {
	$i++;
	echo '<tr><td>' . $val . '</td><td>' . $key . '</td></tr>';
	if ($i > 9) break;
  }
  echo '</table>';
  mysql_close();
?>
