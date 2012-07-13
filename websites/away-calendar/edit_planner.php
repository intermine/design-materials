<?php

require_once "event_log.inc.php";
require_once "planner.inc.php";
require_once "absence.inc.php";
require_once "page.inc.php";

$session=Session::getSession();
$session->loadParameter("id","\d+");
$session->loadParameter("start_ym","\d{4}-\d{2}");
$session->loadParameter("start_d","\d?\d");
$session->loadParameter("start_noon","[01]",0);
$session->loadParameter("stop_ym","\d{4}-\d{2}");
$session->loadParameter("stop_d","\d?\d");
$session->loadParameter("stop_noon","[01]",0);
$session->loadParameter("type","\w");
$session->loadParameter("comment","[\w\s\',\-\+\"\!\&\*\(\)]*","");

function display_calendar ($name,$ref)
{
    $planner=new Planner();
    $fay=Session::getSession()->worker->firstAYear;
    $ayb=$planner->aYear-1;
    if ($ayb<$fay)
        $ayb=$fay;
    $Month=array("January","February","March","April","May","June","July",
            "August","September","October","November","December");

    if (isset($ref) && $ref)
    {
        $d_ref=substr($ref,8,2);
        $ym_ref=substr($ref,0,7);
    } else
    {
        $d_ref=date("d");
        $ym_ref=date("Y-m");
    }

    echo "<select id=\"".$name."_d\" name=\"".$name.
        "_d\" onchange=\"checkDates()\">\n";
    for ($i=1;$i<=31;$i++)
        printf("<option value=\"%u\"%s>%u</option>\n",$i,
            (($i==$d_ref)? " selected=\"selected\"" : ""),$i);
    echo "</select>\n";

    echo "<select id=\"".$name."_ym\" name=\"".$name.
        "_ym\" onchange=\"checkDates()\">\n";
    for ($y=$ayb;$y<=$planner->finalAYear;$y++)
    {
        for ($m=10;$m<=12;$m++)
        {
            $ym=($y-1)."-".$m;
            printf("<option value=\"%s\"%s>%s %u</option>\n",$ym,
                    (($ym==$ym_ref)? " selected=\"selected\"" : ""),
                    $Month[$m-1],$y-1);
        }
        
        for ($m=1;$m<=9;$m++)
        {
            $ym=$y."-0".$m;
            printf("<option value=\"%s\"%s>%s %u</option>\n",$ym,
                    (($ym==$ym_ref)? " selected=\"selected\"" : ""),
                    $Month[$m-1],$y);
        }
    }
    echo "</select>\n";
    echo "<script type=\"text/javascript\">addCalendar('".$name."',".$ayb.",".
        $planner->finalAYear.");</script>";
}

if (isset($session->id))
{
    try
    {
        $oldAbsence=Absence::loadAbsence($session->workerID,$session->id);
    }
    catch (Exception $e)
    {
        header("Location: form.php");
        exit;
    }
}
else
    $oldAbsence=new Absence("",0,"",0,"","V",NULL,0);

// trick to avoid testing all parameters
try
{
    $start=sprintf("%s-%02d",$session->start_ym,$session->start_d);
    $stop=sprintf("%s-%02d",$session->stop_ym,$session->stop_d);
    $start_noon=$session->start_noon;
    $stop_noon=$session->stop_noon;

    if ($start<=$stop) {
        # class includes
        require_once "class/Messaging.php";
        require_once "class/AddEditHoliday.php";

        # the h00liday
        $holiday = new AddEditHoliday($start, $stop, $start_noon, $stop_noon,
            $session->comment,
            $session->type,
            $session->workerID, true);

        # editing/adding a new h00liday
        if (isset($session->id)) {
            $holiday->edit($session->id, $oldAbsence);
        } else {
            $holiday->add($oldAbsence);
        }

        # notify of the newly saved holidays
        $newHoliday = $holiday->savedHoliday;
        if ($newHoliday) {
            foreach ($holiday->savedHoliday as $newHoliday) {
                Messaging::ok(
                    sprintf("Absence from %s %s till %s %s <b>saved</b>.",
                           $newHoliday['start'], ($newHoliday['startnoon']) ? 'lunch time' : 'morning',
                           $newHoliday['stop'], ($newHoliday['stopnoon']) ? 'lunch time' : 'afternoon')
                );
            }
        } else {
            Messaging::warning("<b>No</b> absence saved.");
        }
        
        # redirect to the holidays listing
        header("Location: form.php");
        exit;
    }
}
catch (Exception $e)
{
}

$session->headCode=
'<script type="text/javascript" src="js/CalendarPopup.js"></script>'.
'<script type="text/javascript" src="js/calendar.js"></script>';

Page::init(FALSE);

echo "<center>\n";
echo "<table id=\"edit-planner\">\n";
echo "<form action=\"edit_planner.php\" method=\"post\">\n";

if (isset($oldAbsence->id))
     echo "<input type=\"hidden\" name=\"id\" value=\"".$oldAbsence->id."\"/>\n";

echo "<tr><td class=\"label\">Beginning:</td><td class=\"text\">";

display_calendar("start",$oldAbsence->start);

echo " Noon: ";
echo "<input type=\"checkbox\" name=\"start_noon\" value=\"1\"".
     (($oldAbsence->startnoon=='1')? " checked=\"checked\"" : "")."/> ";
echo "</td></tr>\n";

echo "<tr><td class=\"label\">End:</td><td class=\"text\">";

display_calendar("stop",$oldAbsence->stop);

echo " Noon: ";
echo "<input type=\"checkbox\" name=\"stop_noon\" value=\"1\"".
     (($oldAbsence->stopnoon=='1')? " checked=\"checked\"" : "")."/> ";
echo "</td></tr>\n";

echo "<tr><td class=\"label\">Type: </td><td class=\"text\">";
foreach (AbsenceType::getTitles() as $type => $title)
{
    if ((!$session->adminRights) && ($type=='B'))
        continue;
    echo "<input type=\"radio\" name=\"type\" value=\"".$type."\"".
        (($oldAbsence->type==$type)? " checked=\"checked\"" : "")."/>".
        $title."<br />";
}
echo "</td></tr>\n";

echo "<tr><td class=\"label\">Comment:</td><td class=\"text\">";
echo "<input type=\"text\" name=\"comment\" size=\"45\" maxlength=\"128\" ".
    "value=\"".$oldAbsence->comment."\"/></td></tr>\n";

echo "<tr><td colspan=\"2\" class=\"submit\"><input type=\"submit\"/></td></tr>";

echo "</form>\n";
echo "</table>\n";
echo "</center>\n";
echo "<br /><a href=\"form.php\" class=\"back\">Back to the menu</a>\n";

Page::close();

?>
