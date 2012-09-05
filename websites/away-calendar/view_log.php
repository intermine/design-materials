<?php

require_once "event_log.inc.php";
require_once "tools.inc.php";
require_once "page.inc.php";

$session=Page::init();

$worker=$session->worker;

echo "<p class=\"log_title\">Log for ".$worker->fullname."</p>\n";

?>
<table border="0" cellspacing="0" cellpadding="6" align="center" id="log">
<tr><th>By</th><th>On</th><th>&nbsp;</th><th>Description</th><th>Boss<br />notified?</th></tr>
<?php

$bgcol=0;

foreach (EventLog::getLog()->getEvents($worker) as $row)
{
    echo "<tr class=\"coul".$bgcol."\">"; $bgcol=1-$bgcol;
    echo sprintf("<td>%s</td><td>%s</td>",$row["fullname"],
            Tools::dateYMDHM2HumanDateTime($row["time"]));
    echo "<td class=\"icon\"><img src=\"img/".$row["event"].".png\"/></td>";
    echo sprintf("<td class=\"action\">%s</td>",nl2br($row["text"]));
    if ($row["notified"]=='Y')
        echo "<td><img src=\"img/notified.png\"/></td>";
    else
        echo "<td>&nbsp;</td>";
    echo "</tr>\n";
}

echo "</table>\n";

Page::close();
?>
