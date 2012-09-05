<?php

class EventLog
{
    static protected $log;

    protected function __construct () {}

    static function getLog ()
    {
        if (!isset(self::$log))
            self::$log=new EventLog();
        return self::$log;
    }

    function getEvents (Worker $worker,$limit=20)
    {
        $log=array();
        $res=Db::getConnection()->query(
                "SELECT w.fullname AS fullname,event,time,text,notified ".
                "FROM eventlog el,worker w ".
                "WHERE w.userid=el.userid AND el.workerid='".$worker->id."' ".
                "ORDER BY time DESC LIMIT ".$limit);
        while ($row=$res->fetchArray())
            $log[]=$row;
        return $log;
    }

    function getPending()
    {
        $report=array();

        // Filter non-notified events (older than 10 minutes)
        $res=Db::getConnection()->query(
                "SELECT w.fullname AS user,workerid,event,time,text ".
                "FROM eventlog el,worker w ".
                "WHERE notified='N' AND w.userid=el.userid AND w.deleted='N' ".
                "AND UNIX_TIMESTAMP(time)<UNIX_TIMESTAMP(NOW())-600 ".
                "ORDER BY workerid,time");
        while ($row=$res->fetchArray())
        {
            $workerid=$row["workerid"];
            if (!isset($report[$workerid]))
                $report[$workerid]=array();
            $report[$workerid][]=array("user" => $row["user"],
                    "time" => $row["time"],
                    "event" => $row["event"],
                    "text" => $row["text"]);
        }
        return $report;
    }

    function clearNotification ($workerID)
    {
         Db::getConnection()->query("UPDATE eventlog ".
                 "SET notified='Y' ".
                 "WHERE workerid='".$workerID."'");
    }

    function saveEvent($newAbsence,$oldAbsence=NULL)
    {
        if (isset($oldAbsence) && isset($oldAbsence->id))
        {
            if (isset($newAbsence))
            {
                $event="CHG";
                $text="Modification:\n  BEFORE: ".$oldAbsence->getText()."\n".
                    "  AFTER: ".$newAbsence->getText();
            } else
            {
                $event="DEL";
                $text="Cancelled ".$oldAbsence->getText();
            }
        }
        else
        {
            $event="ADD";
            $text="Added ".$newAbsence->getText();
        }

        require_once "session.inc.php";
        $session=Session::getSession();
        Db::getConnection()->query(
            "INSERT INTO eventlog(time,userid,workerid,event,text) ".
            "VALUES(NOW(),'".$session->userID."','".$session->workerID."','".
            $event."','".db_escape_string($text)."')");
    }

}

?>
