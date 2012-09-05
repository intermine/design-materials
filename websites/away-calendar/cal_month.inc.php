<?php

class CalMonth
{
    protected $d=array();

    function __construct ($month, $ryear)
    {
        $timebase=mktime(0,0,0,$month,1,$ryear);
        $this->d["month"]=$month;
        $this->d["year"]=$ryear;
        $this->d["numDays"]=date("t",$timebase);
        $this->d["dayBase"]=date("w",$timebase);
    }

    function isWeekend ($day)
    {
        $w=($this->d["dayBase"]+$day-1)%7;
        return (($w==0) || ($w==6));
    }

    function date2Idx ($date,$noon,$isEnd)
    {
        preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg);

        // Start before the 1st day of the current month
        if (($reg[1]<$this->d["year"]) || 
                (($reg[1]==$this->d["year"]) && ($reg[2]<$this->d["month"])))
            return 0;

        // End after the last day of the current month
        if (($reg[2]>$this->d["month"]) || ($reg[1]>$this->d["year"]))
            return $this->numDays*2+1;

        $idx=($reg[3]-1)*2;
        if ($noon xor $isEnd) // $noon and start || !$noon and end
            $idx++;

        return $idx;
    }

    function __get ($name)
    {
        if (isset($this->d[$name]))
            return $this->d[$name];
        throw new Exception();
    }

    function __set ($name, $value)
    {
        throw new Exception();
    }
}

?>
