<?php

require_once('planner.inc.php');
require_once('page.inc.php');

$session=Session::getSession();

if (!$session->adminRights)
{
    header("Location: index.php");
    exit;
}

$session->loadParameter("auWorkerID","\w+");
$session->loadParameter("auFullname","[\w\s]+");
$session->loadParameter("auNumDays","\d+(\.\d+)?",30);
$session->loadParameter("auReportTo","[\w\@\.]+");
$session->loadParameter("auAdminRights","[YN]","N");

$planner=new Planner();

$session->warning=FALSE;
Page::init(FALSE);

// trick to avoid testing all parameters
try
{
    Worker::createAndSave(strtolower($session->auWorkerID),$session->auFullname,
            $session->auAdminRights,$session->auReportTo,$planner->aYear,
                $session->auNumDays);

    foreach (BankHoliday::listForAYear($planner->aYear) as $bh => $name)
    {
        $absence=new Absence($bh,'N',$bh,'N',$name,'B',NULL,
                AbsenceType::getDayCount('B'),strtolower($session->auWorkerID));
        $absence->saveAbsence();
    }
    echo "<p> ".$session->auFullname." [".strtolower($session->auWorkerID).
        "] has been created!</p>\n";
    Page::close();
    exit;
}
catch (Exception $e)
{
}


?>
<center>
<table cellspacing="1" cellpadding="5" id="userbox">
<form action="adduser.php" method="post">
<tr><td align="right">User ID:</td> <td align="left">
<input type="text" name="auWorkerID" size="16" maxlength="16" value="<?php
 echo (isset($session->auWorkerID)? $session->auWorkerID : ""); ?>"/></td></tr>
<tr><td align="right">Full Name:</td> <td align="left">
<input type="text" name="auFullname" size="32" maxlength="32" value="<?php
 echo (isset($session->auFullname)? $session->auFullname : ""); ?>"/></td></tr>
<tr><td align="right">Entitlement (first year):</td> <td align="left">
<input type="text" name="auNumDays" size="4" maxlength="4" value="<?php
echo $session->auNumDays; ?>"/></td></tr>
<tr><td align="right">Report to (email address):</td> <td align="left">
<input type="text" name="auReportTo" size="30" maxlength="128" value="<?php
echo (isset($session->auReportTo)? $session->auReportTo : ""); ?>"/></td></tr>
<tr><td align="right">Admin rights:</td> <td align="left">
<?php
echo "Y<input type=\"radio\" name=\"auAdminRights\" value=\"Y\"".
    (($session->auAdminRights=='Y')? " CHECKED" : "")."/> "; 
echo "N<input type=\"radio\" name=\"auAdminRights\" value=\"N\"".
    (($session->auAdminRights=='Y')? "" : " CHECKED")."/> ";
?></td></tr>
<tr><td colspan="2" align="center">
<input type="submit" value="Enter">
</td></tr>
</form>
</table>
</center>
<?php

Page::close(TRUE);

?>
