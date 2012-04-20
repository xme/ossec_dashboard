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

  $timeEnd = $timeNow - (10 * $formPeriod);
  $stringStart = date('D, j M Y H:i:s', $timeNow);
  $stringEnd = date('D, j M Y H:i:s', $timeEnd);
  echo '<p class="quiet large">' . $stringEnd . ' to ' . $stringStart . '</p>';

  $max=0;
  $count = array();
  for ($i=0; $i < 10; $i++)
  {
    $timeStart = $timeNow - ($formPeriod * $i);
    $timePast = $timeStart - $formPeriod;
    mysql_connect($dbHost,$dbUser, $dbPass);
    mysql_select_db($dbName) or die("Unable to connect to database");

    $dbQuery = "select count(timestamp) as count from alert where timestamp between $timePast and $timeStart";
    $dbResult = mysql_query($dbQuery);
    $count[$i]=mysql_result($dbResult,0,"count");
    if ($count[$i] > $max) { $max = $count[$i]; }
  }
  $scale = 180 / $max;
  echo '<table class="sample" width="100%"><tr valign="bottom">';
  for ($i=9; $i >= 0; $i--)
  {
    $height=$count[$i] * $scale;
    echo '<td valign="bottom" align="center"><img width="20" height="' .  $height . '" src="1x1-orange.gif"><br>' . $count[$i] . '</td>';
  }
  echo "</tr></table>";

  mysql_close();
?>
