<?php

require_once "report.inc.php";
require_once "page.inc.php";

$session=Page::init();

# any messages?
require_once "class/Messaging.php";
$messages = Messaging::get();
if ($messages) {
    echo '<ul id="messages">';
    foreach ($messages as $message) {
        printf("<li class='%s'>%s</li>", $message['type'], $message['text']);
    }
    echo '</ul>';
}

$planner=new Planner();
$session->loadParameter("AYear_show","\d{4}",$planner->aYear);
$worker=Session::getSession()->worker;
$report=new Report($worker);

$AYear_begin=$worker->firstAYear;
$AYear_end=$planner->finalAYear;
$AYear_show=$session->AYear_show;

if ($AYear_show<$AYear_begin)
    $AYear_show=$planner->aYear;

if ($AYear_show>$AYear_end)
    $AYear_show=$planner->aYear;

echo "<div align=\"center\">\n";

echo "<p><b>From 01/10/".($AYear_show-1)." to 30/09/".$AYear_show."</b></p>";
echo "<table align=\"center\" cellspacing=\"0\" cellpadding=\"5\" id=\"form\">\n";
echo "<tr><th>Beginning</th><th>End</th><th>Comment</th>\n";
echo "<th>Days taken</th><td colspan=\"2\" class=\"ghost\"></td></tr>\n";

$bgcol=0;

$ry=$report->reportYear[$AYear_show];
foreach ($ry->report as $abs)
{
    if (!AbsenceType::isToBeReported($abs->type))
        continue;
    echo "<tr class=\"coul".$bgcol."\">"; $bgcol=1-$bgcol;
    echo "<td>".Tools::dateYMD2DDMY($abs->start).
        (($abs->startnoon)? " (noon)" : "")."</td>";
    echo "<td>".Tools::dateYMD2DDMY($abs->stop).
        (($abs->stopnoon)? " (noon)" : "")."</td>";

    echo "<td>".substr($abs->comment,0,24).
        ((strlen($abs->comment)>24)? "..." : "")."</td>";
    echo "<td>".Tools::days($abs->numDays,FALSE)."</td>";
    echo "<td><a href=\"edit_planner.php?id=".$abs->id.
        "\" class=\"edit\">Edit</a></td>";
    echo "<td class=\"cancel\"><a href=\"cancel.php?id=".$abs->id.
        "\" class=\"cancel\">Cancel</a></td>";
    echo "</tr>\n";
}


echo "</table>\n";
echo "<p>Allowance: ".$ry->allowance;
echo "<br />";
echo "Total taken: ".$ry->days_taken."<br />";
echo "Remaining: ".$ry->remaining."</p>\n";
echo "<a id=\"insert\" href=\"edit_planner.php\">Insert a new holiday period</a>\n";

echo "<p>Academic year: ";

for ($y=$AYear_begin; $y<=$AYear_end; $y++)
{
    if ($y!=$AYear_begin)
        echo " - ";
    if ($y!=$AYear_show)
        echo "<a href=\"?AYear_show=".$y."\">";
    echo ($y-1)."/".$y;
    if ($y!=$AYear_show)
        echo "</a>";
}
echo "</p>\n";
echo "</div>\n";

Page::close();

?>
