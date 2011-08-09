<?php

require_once "page.inc.php";

$session=Page::init(FALSE);

?>
<div id="index">
<div id="menuleft">
<a href="planner.php">See the planner</a>
<a href="form.php">Add/Edit/Cancel leaving</a>
<a href="report.php">Yearly Report</a>
<a href="view_log.php">View the log of changes</a>
</div>
<div id="menuright">
<?php

if ($session->adminRights)
{
    echo "<a href=\"adduser.php\">Add a new user</a>";
    echo "<a href=\"deluser.php\">Delete a user</a>";
    echo "<a href=\"swapuser.php\">Connect as...</a>";
    echo "<a href=\"add_bank_holiday.php\">Add a bank holiday</a>";
}

?>
</div>
</div>
<?php

Page::close();

?>

