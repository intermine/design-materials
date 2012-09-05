#!/usr/bin/php
<?php

include("config.inc.php");
include("db.inc.php");
include "tools.inc.php";
include "absence.inc.php";


$AYear=2008;
// Hilde
$b=Tools::dateYMD2Timestamp("2007-10-01")+30000;
$e=Tools::dateYMD2Timestamp("2008-09-30");

for ($i=$b; $i<$e; $i+=(86400*7))
{
    $a=date("Y-m-d",(BankHoliday::isBankHoliday($i))? $i+86400 : $i);
    $b=date("Y-m-d",(BankHoliday::isBankHoliday($i+86400))? $i : $i+86400);
    if ($a>$b)
        continue;
    Db::getConnection()->query("REPLACE INTO planner(userid,start,startnoon,stop,".
            "stopnoon,nb,type,comment) ".
            "VALUES ('hj240','".$a."','0','".$b.
            "','0','0','O','part-time')");
    echo mysql_error();
}
Db::getConnection()->query("UPDATE allowance set allowance='20' WHERE userid='hj240' AND ayear='".$AYear."'");
echo mysql_error();

// Rachel
$b=Tools::dateYMD2Timestamp("2007-10-03")+30000;
$e=Tools::dateYMD2Timestamp("2008-09-26");

for ($i=$b; $i<$e; $i+=(86400*7))
{
    $a=date("Y-m-d",(BankHoliday::isBankHoliday($i))? $i+86400 : $i);
    $b=date("Y-m-d",(BankHoliday::isBankHoliday($i+2*86400))? $i+86400 : $i+2*86400);
    if ($a>$b)
        continue;
    Db::getConnection()->query("REPLACE INTO planner(userid,start,startnoon,stop,".
            "stopnoon,nb,type,comment) ".
            "VALUES ('rcl30','".$a."','1','".$b.
            "','0','0','O','part-time')");
    echo mysql_error();
}
Db::getConnection()->query("UPDATE allowance set allowance='16.5' WHERE userid='rcl30' AND ayear='".$AYear."'");
echo mysql_error();



?>
