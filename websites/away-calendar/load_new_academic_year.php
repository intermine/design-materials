#!/usr/bin/php
<?php

include("config.inc.php");
include("db.inc.php");
include "planner.inc.php";

function load_bank_holiday ($day,$month,$year,$comment)
{
    global $db;

    $d=$year."-".$month."-".$day;
    echo $d."   ".$comment."\n";

    $con=Db::getConnection();
    $con->query("INSERT INTO bankholiday VALUES('".$d."','".$comment."')");
    $res=$con->query("SELECT userid,fullname ".
                   "FROM worker WHERE deleted='N' ORDER BY fullname");
    while ($row=$res->fetchArray())
    {
        $con->query("INSERT INTO planner(userid,start,startnoon,stop,".
                       "stopnoon,nb,type,comment) ".
                       "VALUES ('".$row["userid"]."','".$d."','0','".$d.
                       "','0','0','B','".$comment."')");
       echo mysql_error();
    }
}

function load_allowance ($ayear,$allowance)
{
    global $db;

    $con=Db::getConnection();
    $res=$con->query("SELECT userid,fullname ".
                   "FROM worker WHERE deleted='N' ORDER BY fullname");
    while ($row=$res->fetchArray())
    {
        $con->query("INSERT INTO allowance(userid,ayear,allowance) ".
                       "VALUES ('".$row["userid"]."','".$ayear."','".
                       $allowance."')");
       echo mysql_error();
    }
}

// http://www.dti.gov.uk/er/bankhol.htm

// AYear:2004  (2003/04)

#load_bank_holiday(25,12,2003); // Christmas Day
#load_bank_holiday(26,12,2003); // Boxing Day
#load_bank_holiday(1,1,2004);   // New Year's Day
#load_bank_holiday(9,4,2004);   // Good Friday
#load_bank_holiday(12,4,2004);  // Easter Monday
#load_bank_holiday(3,5,2004);   // Early May Bank Holiday
#load_bank_holiday(31,5,2004);  // Spring Bank Holiday
#load_bank_holiday(30,8,2004);  // Summer Bank Holiday


// AYear:2005  (2004/05)

#load_bank_holiday(27,12,2004,"Boxing Day"); // Boxing Day
#load_bank_holiday(28,12,2004,"Christmas Day"); // Christmas Day
#load_bank_holiday(3,1,2005,"New Year\'s Day");   // New Year's Day
#load_bank_holiday(25,3,2005,"Good Friday");   // Good Friday
#load_bank_holiday(28,3,2005,"Easter Monday");  // Easter Monday
#load_bank_holiday(2,5,2005,"Early May Bank Holiday");   // Early May Bank Holiday
#load_bank_holiday(30,5,2005,"Spring Bank Holiday");  // Spring Bank Holiday
#load_bank_holiday(29,8,2005,"Summer Bank Holiday");  // Summer Bank Holiday

// AYear:2006  (2005/06)

#load_bank_holiday(26,12,2005,"Boxing Day"); // Boxing Day
#load_bank_holiday(27,12,2005,"Christmas Day"); // Christmas Day
#load_bank_holiday(2,1,2006,"New Year\'s Day");   // New Year's Day
#load_bank_holiday(14,4,2006,"Good Friday");   // Good Friday
#load_bank_holiday(17,4,2006,"Easter Monday");  // Easter Monday
#load_bank_holiday(1,5,2006,"Early May Bank Holiday");   // Early May Bank Holiday
#load_bank_holiday(29,5,2006,"Spring Bank Holiday");  // Spring Bank Holiday
#load_bank_holiday(28,8,2006,"Summer Bank Holiday");  // Summer Bank Holiday

// AYear:2007  (2006/07)

#load_bank_holiday(25,12,2006,"Christmas Day"); // Christmas Day
#load_bank_holiday(26,12,2006,"Boxing Day"); // Boxing Day
#load_bank_holiday(1,1,2007,"New Year\'s Day");   // New Year's Day
#load_bank_holiday(6,4,2007,"Good Friday");   // Good Friday
#load_bank_holiday(9,4,2007,"Easter Monday");  // Easter Monday
#load_bank_holiday(7,5,2007,"Early May Bank Holiday");   // Early May Bank Holiday
#load_bank_holiday(27,5,2007,"Spring Bank Holiday");  // Spring Bank Holiday
#load_bank_holiday(26,8,2007,"Summer Bank Holiday");  // Summer Bank Holiday

// Generic loader...

$year=date("Y");
echo "loading ".$year."-".($year+1)."...";

$planner=new Planner();

if ($planner->finalAYear>$year)
{
    echo "   already done!\n";
    exit;
}

load_allowance($year+1,33);

echo "\n----\n";
load_bank_holiday(25,12,$year,"Christmas Day");

load_bank_holiday(26,12,$year,"Boxing Day");

// Year +1
$year++;

load_bank_holiday(1,1,$year,"New Year\'s Day");

$e=easter_date($year);
load_bank_holiday(date("j",$e-172800),date("n",$e-172800),$year,"Good Friday");
load_bank_holiday(date("j",$e+86400),date("n",$e+86400),$year,"Easter Monday");

$d=7-(date("N",mktime(12,0,0,5,7,$year))-1)%7;
load_bank_holiday($d,5,$year,"Early May Bank Holiday");

$d=31-(date("N",mktime(12,0,0,5,31,$year))-1)%7;
load_bank_holiday($d,5,$year,"Spring Bank Holiday");

$d=31-(date("N",mktime(12,0,0,8,31,$year))-1)%7;
load_bank_holiday($d,8,$year,"Summer Bank Holiday");

echo "----\n";


?>
