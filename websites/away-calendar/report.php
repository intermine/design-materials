<?php

require_once "report.inc.php";
require_once "absence.inc.php";
require_once "page.inc.php";

$session=Session::getSession();
$session->headCode="<style>@media print {a.back {display: none;} } </style>\n";
$session->warning=FALSE;

Page::init();

$report=new Report($session->worker);

echo "<p class=\"log_title\">Holiday report for ".$report->workerName."</p>\n";

echo "<div align=\"center\">\n";

$hr=FALSE;
foreach ($report->reportYear as $y => $ry)
{
    if ($ry->future)
        continue;

    if ($hr)
        echo "<hr width=\"20%\">\n";
    $hr=TRUE;

    echo "<p><b>From 01/10/".($y-1)." to 30/09/".$y."</b></p>";
    echo "<table cellspacing=\"0\" cellpadding=\"5\" class=\"report\">\n<tr>\n";
    echo "<th>Beginning</th><th>End</th><th>Comment</th>\n";
    echo "<th>Days taken</th>\n";
    echo "</tr>\n";

    $bgcol=0;

    foreach ($ry->report as $abs)
    {
        if ((!AbsenceType::isToBeReported($abs->type)) && ($abs->numDays==0))
            continue;
        echo "<tr class=\"coul".$bgcol."\">"; $bgcol=1-$bgcol;
        echo "<td>".Tools::dateYMD2DDMY($abs->start).
            (($abs->startnoon)? " (noon)" : "")."</td>";
        echo "<td>".Tools::dateYMD2DDMY($abs->stop).
            (($abs->stopnoon)? " (noon)" : "")."</td>";

        echo "<td>".(($abs->comment!="")? $abs->comment : "&nbsp;")."</td>";
        echo "<td align=\"center\">".Tools::days($abs->numDays,FALSE)."</td>";
        echo "</tr>\n";
    }

    echo "</table>\n";

    try
    {
        echo "<p>Taken so far: ".$ry->dtsf."</p>\n";
    } catch (Exception $e) {}
    echo "<p>Allowance: ".$ry->allowance;
    echo "<br />";
    echo "Total taken: ".$ry->days_taken."<br />";
    echo "Remaining: ".$ry->remaining."</p>\n";
}

echo "</div>\n";

Page::close();
?>
