<?php

require_once "worker.inc.php";
require_once "page.inc.php";

$session=Session::getSession();
$session->loadParameter("oldworkerid","\w+");

if (!$session->adminRights)
{
    header("Location: index.php");
    exit;
}

if (isset($session->oldworkerid))
{
    $worker=new Worker($session->oldworkerid);
    $worker->delete();

    if ($session->WorkerID==$session->oldworkerid)
        setcookie("workerid",$session->UserID,time()+1800);

    Page::init();
    echo "User deleted";
    Page::close();
    exit;
}

$session->warning=FALSE;
Page::init(FALSE);

?>
<center>
<table cellspacing="1" cellpadding="5" id="passwdbox">
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
<tr><td align="right">Select a user:</td> <td align="left">
<select name="oldworkerid">
<?php

$workers=new Workers();

foreach ($workers->list as $worker)
    echo "<option value=\"".$worker->id."\"".
    (($worker->id==$session->workerID)? " selected" : "").">".
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
