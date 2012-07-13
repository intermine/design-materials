#!//usr/bin/php
<?php

require_once "db.inc.php";
require_once "tools.inc.php";
require_once "away_tools.inc.php";
require_once "event_log.inc.php";
require_once "worker.inc.php";
require_once "report.inc.php";

$eventLog=EventLog::getLog();
$pending=$eventLog->getPending();

if (count($pending)==0)
    exit(1);

$con=Db::getConnection();

foreach ($pending as $workerID => $events)
{
    $worker=new Worker($workerID);
    $email="AWAY (Holiday Planner)\n".
           "----------------------\n\n".
           "New events for ".$worker->fullname.":\n\n";
    foreach ($events as $event)
    {
        $email.="On ".substr($event["time"],8,2)."/".
            substr($event["time"],5,2)."/".substr($event["time"],0,4)." at ".
            substr($event["time"],11,2).":".substr($event["time"],14,2).
            " by ".$event["user"]." [".$event["event"]."] :\n".
            $event["text"]."\n\n".
            "----------------------------------------------------------".
            "\n";
    }

    $report=new Report($worker);
    $planner=new Planner();
    $ry=$report->reportYear[$planner->aYear];

    $email.="\nSummary (period from 01/10/".($planner->aYear-1).
        " to 30/09/".$planner->aYear."):\n";
    try
    {
        $email.="   Taken so far: ".$ry->dtsf."\n";
    } catch (Exception $e) {}
    $email.="   Allowance: ".$ry->allowance;
    try
    {
        $email.=" (".(($ry->carried_forward[0]!='-')? "+" : "").
            $ry->carried_forward." carried forward)";
    } catch (Exception $e) {}
    $email.="\n";
    $email.="   Total taken: ".$ry->days_taken."\n";
    $email.="   Remaining: ".$ry->remaining."\n";

    $report_to=$worker->report_to;
    if (isset($forceReportTo) && ($forceReportTo!=""))
        $report_to=$forceReportTo;
    mail($report_to,"AWAY: ".str_replace("ç","c",$worker->fullname),$email,
            "From: webmaster@flymine.org\r\nContent-Type: text/plain; charset=utf-8");

    $eventLog->clearNotification($workerID);
}

?>
