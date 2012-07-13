<?php

require_once "worker.inc.php";
require_once "absence.inc.php";

class Planner
{
    protected $d=array();

    function __get ($name)
    {
        if ($name=="finalAYear")
        {
            if (!isset($this->d["finalAYear"]))
            {
                $res=Db::getConnection()->query("SELECT max(day) ".
                        "FROM bankholiday ".
                        "WHERE day like '____-01-0_' ");
                $this->d["finalAYear"]=substr($res->getValue(),0,4);
            }
            return $this->d["finalAYear"];
        }
        elseif ($name=="aYear")
        {
            if (!isset($this->d["aYear"]))
                $this->d["aYear"]=(date("m")>9)? date("Y")+1 : date("Y");
            return $this->d["aYear"];
        }
        throw new Exception();
    }

    function __set ($name,$value)
    {
        throw new Exception();
    }

    function getAbsences (Worker $worker,$ayear)
    {
        $absences=array();

        $beg=($ayear-1)."-10-01";
        $end=$ayear."-09-30";
        $res=Db::getConnection()->query("SELECT id,start,startnoon,stop,".
                "stopnoon,nb,type,comment ".
                "FROM planner ".
                "WHERE userid='".$worker->id."' AND ".
                "((start>='".$beg."' AND start<='".$end."') OR ".
                "(stop>='".$beg."' AND stop<='".$end."')) ".
                "ORDER BY start");
        while ($row=$res->fetchArray())
            $absences[]=new Absence($row["start"],$row["startnoon"],
                $row["stop"],$row["stopnoon"],$row["comment"],$row["type"],
                $row["id"],$row["nb"]);

        return $absences;
    }

    function getPlanner (CalMonth $cal)
    {
        $absences=array();

        $beg=date("Y-m-d",mktime(0,0,0,$cal->month,1,$cal->year));
        $end=date("Y-m-d",mktime(0,0,0,$cal->month+1,0,$cal->year));
        $res=Db::getConnection()->query("SELECT DISTINCT id,p.userid,start,".
                "startnoon,stop,stopnoon,type,comment".
                " FROM planner p,worker w ".
                " WHERE ((start>='".$beg."' AND start<='".$end."')".
                " OR (stop>='".$beg."' AND stop<='".$end."'))".
                " AND p.userid=w.userid AND w.deleted='N'");
        while ($row=$res->fetchArray())
            $absences[]=new Absence($row["start"],$row["startnoon"],
                $row["stop"],$row["stopnoon"],$row["comment"],$row["type"],
                $row["id"],NULL,$row["userid"]);

        return $absences;
    }
}
?>
