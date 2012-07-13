<?php

require_once "session.inc.php";
require_once "tools.inc.php";
require_once "absence.inc.php";
require_once "event_log.inc.php";

$session=Session::getSession();
$session->loadParameter("id","\d+");

if (isset($session->id))
{
    try
    {
        $absence=Absence::loadAbsence($session->workerID,$session->id);
    }
    catch (Exception $e)
    {
        header("Location: form.php");
        exit;
    }

    $absence->cancel();

    EventLog::getLog()->saveEvent(NULL,$absence);
}

header("Location: form.php");

?>
