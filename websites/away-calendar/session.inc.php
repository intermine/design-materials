<?php

require_once "db.inc.php";
require_once "worker.inc.php";

class Session
{
    static protected $session;
    protected $d=array();
    /*protected $dn=array("worker","userID","adminRights","backButton","warning",
            "headCode");*/

    static function getSession()
    {
        try
        {
            if (!isset(self::$session))
                self::$session=new Session();
        }
        catch (Exception $e)
        {
            echo "<div id=\"index\">";
            echo "<p class=\"denied\">Access Denied</p>\n";
            echo "</div>";
            exit;
        }
        return self::$session;
    }

    function __construct ()
    {
        if (!isset($_COOKIE["REMOTE_USER"]))
            throw new Exception();

        $userID=$_COOKIE["REMOTE_USER"];

        if (isset(self::$session))
            throw new Exception();

        $res=Db::getConnection()->query(
                "SELECT * ".
                "FROM worker ".
                "WHERE userid='".$userID."' AND deleted='N' ".
                "LIMIT 1");
        if ($res->numRows()!=1)
            throw new Exception();

        self::$session=$this;

        $this->d["userID"]=$userID;
        $this->loadAdminRights();

        $this->d["worker"]=new Worker(
            (($this->d["adminRights"]) && isset($_COOKIE["workerid"]))?
            $_COOKIE["workerid"] : $userID);

        setcookie("workerid",$this->workerID,time()+3600);

        $this->d["backButton"]=TRUE;
        $this->d["warning"]=TRUE;
        $this->d["headCode"]="";
    }

    function loadAdminRights ()
    {
        $this->d["adminRights"]=(DB::getConnection()->query("SELECT admin ".
                    "FROM worker ".
                    "WHERE userid='".$this->d["userID"]."' ".
                    "LIMIT 1")->getValue()=='Y');
    }

    function __get ($name)
    {
        if (isset($this->d[$name]))
            return $this->d[$name];
        if ($name=="workerID")
            return $this->d["worker"]->id;
        throw new Exception();
    }

    function __set ($name,$value)
    {
        if (in_array($name,array("backButton","warning","headCode")))
            $this->d[$name]=$value;
        else
            throw new Exception();
    }

    function __isset ($name)
    {
        return isset($this->d[$name]);
    }

    function loadParameter ($name,$filter,$default=NULL)
    {
        setlocale(LC_CTYPE,"fr_FR");
        if (isset($_REQUEST[$name]))
            if (preg_match("/^".$filter."$/u",$_REQUEST[$name])==1)
                return $this->d[$name]=$_REQUEST[$name];
        if (isset($default))
            $this->d[$name]=$default;
    }
}

?>
