<?php

require_once "worker.inc.php";
require_once "page.inc.php";

$session=Session::getSession();
$session->loadParameter("newworkerid","\w+");

if (!$session->adminRights)
{
    header("Location: index.php");
    exit;
}

if (isset($session->newworkerid))
{
    setcookie("workerid",$session->newworkerid,time()+1800);
    header("Location: index.php");
    exit;
}

$session->warning=FALSE;
Page::init(FALSE);

?>
<center>
<table cellspacing="1" cellpadding="5" id="passwdbox">
<form action="swapuser.php" method="post">
<tr><td align="right">Select a user:</td> <td align="left">
<select name="newworkerid">
<?php

$workers=new Workers();

foreach ($workers->list as $worker)
    echo "<option value=\"".$worker->id."\"".
    (($worker->id==$session->userID)? " selected" : "").">".
    $worker->fullname."</option>\n";

?>
</select></td></tr>
<tr><td colspan="2" align="center">
<input type="submit" value="Enter">
</td></tr>
</form>
</table>
</center>
<?php

Page::close(TRUE);

?>
