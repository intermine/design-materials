<?php

require_once "away_tools.inc.php";
require_once "page.inc.php";
require_once "img_planner.inc.php";

function find_month ($shift)
{
    $session=Session::getSession();

    $month=$session->Month+$shift;
    if ($month)
    {
        if ($month<13)
            $ryear=$session->RYear;
        else
        {
            $month=1; $ryear=$session->RYear+1;
        }
    } else
    {
        $month=12; $ryear=$session->RYear-1;
    }

    if (!check_date($month,$ryear))
        return FALSE;

    $r="<a href=\"planner.php?Month=".$month."&RYear=".$ryear.
        "\" class=\"planner\">";
    if ($shift<0)
        $r.="&lt;&lt;&lt; ";
    $r.=date("F",mktime(0,0,0,$month,1,$ryear));
    if ($shift>0)
        $r.=" &gt;&gt;&gt;";
    $r.="</a>";
    return $r;
}
$session=Session::getSession();
$session->warning=FALSE;
Page::init(FALSE);

$session->loadParameter("Month","\d?\d",date("m"));
$session->loadParameter("RYear","\d{4}",date("Y"));
$Timebase=mktime(0,0,0,$session->Month,1,$session->RYear);

?>
<script type="text/javascript" src="js/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript">
var types=new Array();
<?php
    foreach(AbsenceType::getTitles() as $type => $title)
    {
        echo "types['".$type."']='".$title."';\n";
    }
?>

function omo(type,comment)
{
var s='<ul id="overlib-map">';
    return overlib('&nbsp;'+comment+'&nbsp;', CAPTION, types[type], WIDTH,-1);
}</script>
<div id="overdiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php


if (!check_date($session->Month,$session->RYear))
{
    echo "Bad date!!!!";
    exit;
}

echo "<table align=\"center\" id=\"planner\">\n<tr>\n<td class=\"prev\">";
echo ($prev=find_month(-1))? $prev : "&nbsp;";
echo "</td>\n";
echo "<td align=\"center\" class=\"title\">".date("F Y",$Timebase).
    "</td>\n<td class=\"next\">";
echo ($next=find_month(1))? $next : "&nbsp;";
echo "</td>\n</tr>\n<tr>\n<td colspan=\"3\">\n";

$name=generate_image($session->Month,$session->RYear);

echo "<img src=\"".$name."\" usemap=\"#boxes\" border=\"0\"/>\n";
echo "</td>\n</tr>\n</table>\n";

Page::close(TRUE);

?>
