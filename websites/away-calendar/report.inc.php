<?php

require_once "planner.inc.php";
require_once "tools.inc.php";
require_once "absence.inc.php";

class ReportYear
{
    public $report=array();
    protected $d=array();
    protected $dn=array("dtsf","allowance","carried_forward","days_taken",
            "remaining","future");

    function __construct ($aYear)
    {
        $this->d["aYear"]=$aYear;
    }

    function addRow (Absence $abs)
    {
        $this->report[]=$abs;
    }

    function __set ($name,$value)
    {
        if (in_array($name,$this->dn))
        {
            $this->d[$name]=$value;
        }
        else
            throw new Exception();
    }

    function __get ($name)
    {
        if ($name=="future")
        {
            if (isset($this->d[$name]))
                return $this->d[$name];
        }
        elseif (in_array($name,$this->dn))
        {
            if (isset($this->d[$name]))
                return Tools::days($this->d[$name]);
        }
        elseif ($name=="aYear")
            return $this->d["aYear"];
        throw new Exception();
    }
}

class Report
{
    protected $worker;
    protected $_reportYear;

    function __construct (Worker $worker)
    {
        $this->worker=$worker;
            
    }

    protected function generate_report()
    {
        $today=date("Y-m-d");
        $carried_forward=0;

        $planner=new Planner();

        for ($y=$this->worker->firstAYear; $y<=$planner->finalAYear; $y++)
        {
            $this->_reportYear[$y]=$ry=new ReportYear($y);

            $allowance=$this->worker->getAllowance($y);
            $days_taken=0;
            $dtsf=0; # Days Taken So Far

            foreach($planner->getAbsences($this->worker,$y) as $abs)
            {
                // If holiday across September-October boundary 
                // => counted only for the new Academic Year
                if ((substr($abs->start,5,2)<10) 
                        && (substr($abs->stop,5,2)>=10)
                        && (substr($abs->start,0,4)==$y))
                    $abs->numDays=0;
                $ry->addRow($abs);
                $days_taken+=$abs->numDays;
                if ($abs->start<=$today)
                    $dtsf+=$abs->numDays;
            }

            if ($y==$planner->aYear)
                $ry->dtsf=$dtsf;
            $ry->allowance=$allowance;
            if ($carried_forward)
                $ry->carried_forward=$carried_forward;
            $ry->days_taken=$days_taken;
            $carried_forward+=$allowance-$days_taken;
            //$ry->remaining=$carried_forward;
            $ry->remaining=$allowance-$days_taken;
            $ry->future=$y>$planner->aYear;
        }
        krsort($this->_reportYear);
    }

    function __get ($name)
    {
        if ($name=="reportYear")
        {
            if (!isset($this->_reportYear))
                $this->generate_report();
            return $this->_reportYear;
        }
        elseif ($name=="workerName")
            return $this->worker->fullname;
        throw new Exception();
    }

    function __set ($name,$value)
    {
        throw new Exception();
    }
}

?>
