<?php

class Worker
{
    protected $workerID;
    protected $d=array();
    protected $al=array();

    function __construct ($workerID)
    {
        $this->workerID=$workerID;
    }

    function loadWorker ()
    {
       $row=Db::getConnection()->query(
               "SELECT fullname,report_to ".
               "FROM worker ".
               "WHERE userid='".$this->workerID."' ".
               "LIMIT 1")->fetchArray();
       $this->d["fullname"]=$row["fullname"];
       $this->d["report_to"]=$row["report_to"];
    }

    function loadAYear ()
    {
       $row=Db::getConnection()->query(
                   $q="SELECT ayear,allowance".
                   " FROM allowance".
                   " WHERE userid='".$this->workerID."'".
                   " ORDER BY ayear".
                   " LIMIT 1")->fetchArray();
       if (!$row)
           throw new Exception();
       $ay=$this->d["firstAYear"]=$row["ayear"];
       $this->al[$ay]=$row["allowance"];
    }

    function loadAllowance ($ayear)
    {
       $row=Db::getConnection()->query(
                   "SELECT allowance".
                   " FROM allowance".
                   " WHERE userid='".$this->workerID."'".
                   " AND ayear='".$ayear."'".
                   " LIMIT 1")->fetchArray();
       $this->al[$ayear]=$row["allowance"];
    }

    static function createAndSave ($workerID,$fullname,$adminRights,$reportTo,
            $firstAYear,$firstYearAllowance)
    {
        $con=Db::getConnection();
        $con->query("REPLACE INTO worker(userid,fullname,admin,report_to) ".
                "VALUES ('".$workerID."','".mysql_escape_string($fullname).
                "','".$adminRights."','".$reportTo."')");

        $con->query("REPLACE INTO allowance(userid,ayear,allowance) VALUES ('".
                $workerID."','".$firstAYear."','".$firstYearAllowance."')");

        return new Worker($workerID);
    }

    function __get ($name)
    {
        if (($name=="fullname") || ($name=="report_to"))
        {
            if (!isset($this->d[$name]))
                $this->loadWorker();
            return $this->d[$name];
        } elseif ($name=="firstAYear")
        {
            if (!isset($this->d[$name]))
                $this->loadAYear();
            return $this->d[$name];
        } elseif ($name=="id")
            return $this->workerID;
        throw new Exception();
    }

    function __set ($name,$value)
    {
        throw new Exception();
    }

    function getAllowance ($ayear)
    {
        if (!isset($this->al[$ayear]))
            $this->loadAllowance($ayear);
        return isset($this->al[$ayear])? $this->al[$ayear] : 0;
    }

    function delete ()
    {
        Db::getConnection()->query("UPDATE worker ".
                "SET deleted='Y' ".
                "WHERE userid='".$this->workerID."'");
    }
}

class Workers
{
    protected $workers=array();
    protected $_count=0;

    function __construct ()
    {
        $res=Db::getConnection()->query("SELECT userid".
            " FROM worker".
            " WHERE deleted='N'".
            " ORDER BY fullname");
        while ($row=$res->fetchArray()) {
            $this->workers[]=new Worker($row[0]);
        }
        $this->_count=count($this->workers);
    }

    function __get ($name)
    {
        if ($name=="count")
            return $this->_count;
        elseif ($name=="list")
            return $this->workers;
        throw new Exception();
    }

    function __set ($name,$value)
    {
        throw new Exception();
    }
}
?>
