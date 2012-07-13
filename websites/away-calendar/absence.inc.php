<?php

require_once "db.inc.php";
require_once "tools.inc.php";

class AbsenceType
{
    /**
       Title / Value in days / Colour / Public / Reported
    */
    static protected $type=array(
            "B" => array("Bank Holiday",0,"#FFA0A0",TRUE,TRUE),
            "C" => array("Conference/Seminar/Meeting",0,"#A060D0",TRUE,TRUE),
            "V" => array("Holiday/Vacation",1,"#B0B0FF",FALSE,TRUE),
            "O" => array("Other",0,"#B0FFB0",TRUE,TRUE));

    static function getTitles ()
    {
        $r=array();
        foreach (self::$type as $type => $data)
            $r[$type]=$data[0];
        return $r;
    }

    static function getTitle ($type)
    {
        return self::$type[$type][0];
    }

    static function getColour ($type)
    {
        return self::$type[$type][2];
    }

    static function isPrivate ($type)
    {
        return !self::$type[$type][3];
    }

    static function isToBeReported ($type)
    {
        return self::$type[$type][4];
    }

    static function getDayCount ($type)
    {
        return self::$type[$type][1];
    }
}

class BankHoliday
{
    static function isBankHoliday ($ts)
    {
        $res=Db::getConnection()->query("SELECT day ".
                "FROM bankholiday ".
                "WHERE day='".date("Y-m-d",$ts)."' ");
        return $res->numRows()!=0;
    }

    static function listForAYear ($aYear)
    {
        $r=array();
        $res=Db::getConnection()->query("SELECT day,name ".
                "FROM bankholiday ".
                "WHERE day>='".($aYear-1)."-10-01'");
        while ($row=$res->fetchArray())
            $r[$row["day"]]=$row["name"];
        return $r;
    }

}

class Absence
{
    protected $d=array();
    protected $dn=array("id","userid","start","startnoon","stop","stopnoon",
            "numDays","comment","type","workerID");

    function __construct ($start,$startnoon,$stop,$stopnoon,$comment,$type,
            $id=NULL,$numDays=NULL,$workerID=NULL)
    {
        $this->d["start"]=$start;
        $this->d["startnoon"]=$startnoon;
        $this->d["stop"]=$stop;
        $this->d["stopnoon"]=$stopnoon;
        $this->d["comment"]="".$comment;
        $this->d["type"]=$type;
        $this->d["id"]=$id;
        $this->d["numDays"]=isset($numDays)? $numDays : 
                $this->compute_num_days($start,$startnoon,$stop,$stopnoon,$type);
        $this->d["workerID"]=$workerID;
    }

    /**
     * Get all absence days for a given worker so we can check if they have been claimed already
     * @static
     * @param  $workerID
     * @param null $absenceID if specified we are editing an existing 
     * @return array
     */
    static function getWorkerAbsence($workerID, $absenceID=null) {
        # who you gonna call? db...
        if (isset($absenceID)) {
            $rows = Db::getConnection()->query(
                "SELECT start,startnoon,stop,stopnoon,userid ".
                "FROM planner ".
                "WHERE userid='$workerID' AND id<>'$absenceID'"); # do not select us as we are editing us
        } else {
            $rows = Db::getConnection()->query(
                "SELECT start,startnoon,stop,stopnoon,userid ".
                "FROM planner ".
                "WHERE userid='$workerID'");
        }

        # populate an array dict of holiday timestamps
        while ($row=$rows->fetchArray()) {
            # convert them into timestamps of "taken" time taking into account half days
            $from = ($row['startnoon'] == 1) ? strtotime("+12 hours", strtotime($row['start'])) :
                    strtotime($row['start']);
            $to = ($row['stopnoon'] == 1) ? strtotime("+12 hours", strtotime($row['stop'])) :
                    strtotime("+23 hours 59 minutes 59 seconds", strtotime($row['stop'])); # finish before end of next day
            $r[] = array('from' => $from, 'to' => $to);
        }
        return $r;
    }

    static function loadAbsence ($workerID,$id)
    {
        $row=Db::getConnection()->query(
                "SELECT start,startnoon,stop,stopnoon,nb,type,comment ".
            "FROM planner ".
            "WHERE userid='".$workerID."' AND id='".$id."' ".
            "LIMIT 1")->fetchArray();
        return new Absence($row["start"],$row["startnoon"],$row["stop"],
                $row["stopnoon"],$row["comment"],$row["type"],$id,$row["nb"],
                $workerID);
    }

    function saveAbsence ()
    {
        $r = Db::getConnection()->query(sprintf(
                    "%s INTO planner(id,userid,start,startnoon,stop,stopnoon,".
                    "nb,type,comment) VALUES (%s,'%s','%s','%d','%s','%d',".
                    "'%f','%s','%s')",
                    isset($this->d["id"])? "REPLACE" : "INSERT",
                    isset($this->d["id"])? "'".$this->d["id"]."'" : "NULL",
                    $this->d["workerID"],
                    $this->d["start"],$this->d["startnoon"],
                    $this->d["stop"],$this->d["stopnoon"],
                    $this->d["numDays"],$this->d["type"],
                    mysql_escape_string($this->d["comment"])));

        # TODO check if success!
        return array('start' => $this->d['start'], 'startnoon' => $this->d['startnoon'],
                     'stop' => $this->d['stop'], 'stopnoon' => $this->d['stopnoon']);
    }

    function cancel ()
    {
        $row=Db::getConnection()->query(
                "DELETE FROM planner WHERE id='".$this->d["id"]."'");
    }
        
    protected static function isWorkingDay ($ts)
    {
        // Saturday or Sunday
        $day=date("w",$ts);
        if (($day==0) || ($day==6))
            return FALSE;

        if (BankHoliday::isBankHoliday($ts))
            return FALSE;
            
        return TRUE;
    }

    static function compute_num_days ($start,$startnoon,$stop,$stopnoon,$type)
    {
        if (AbsenceType::getDayCount($type)==0)
            return 0;

        $start=Tools::dateYMD2Timestamp($start);
        $stop=Tools::dateYMD2Timestamp($stop);

        $numDays=0;

        // Withdraw .5 day if starts at noon
        if (($startnoon) && (self::isWorkingDay($start)))
            $numDays-=.5;

        // Withdraw .5 day if stops at noon
        if (($stopnoon) && (self::isWorkingDay($stop)))
            $numDays-=.5;

        for ($i=$start; $i<=$stop; $i+=86400)
            if (self::isWorkingDay($i))
                $numDays++;

        return $numDays;
    }

    function getText ()
    {
        return AbsenceType::getTitle($this->d["type"]).
            " (".$this->d["comment"].") ".
            "from ".Tools::dateYMD2HumanDate($this->d["start"]).
            ($this->d["startnoon"]? " noon" : "").
            " to ".Tools::dateYMD2HumanDate($this->d["stop"]).
            ($this->d["stopnoon"]? " noon" : "").
            " (".Tools::days($this->d["numDays"]).")";
    }

    function getID() {
        return $this->d['id'];
    }

    function __get ($name)
    {
        if (in_array($name,$this->dn))
            if (isset($this->d[$name]))
                return $this->d[$name];
        throw new Exception();
    }

    function __set ($name,$value)
    {
        if (($name=="numDays") || ($name=="comment"))
            $this->d[$name]=$value;
        else
            throw new Exception();
    }

    function __isset ($name)
    {
        return isset($this->d[$name]);
    }
}





?>
