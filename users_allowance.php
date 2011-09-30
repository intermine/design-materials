<?php
/**
 * User: radek
 * Date: 09/08/11
 * Time: 16:33
 */
 
require_once('planner.inc.php');
require_once('page.inc.php');

$session=Session::getSession();

if (!$session->adminRights)
{
    header("Location: index.php");
    exit;
}

$planner=new Planner();

$session->warning=FALSE;
Page::init(FALSE);

// load the existing allowance of peeps
require_once('class/BankHolidayAllowance.php');
$bha = new BankHolidayAllowance();
?>

<center>
<form method="post">
<table>
    <thead>
        <tr>
            <th>userid</th>
            <th><?php echo $bha->year-1; ?></th>
            <th><?php echo $bha->year; ?></th>
            <th><?php echo $bha->year+1; ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($bha->getAllowance() as $userid => $years) { ?>
    <tr>
        <td><?php echo $userid; ?></td>
        <?php foreach ($years as $year => $allowance) { ?>
            <td><?php echo $allowance; ?></td>
        <?php } ?>
    </tr>
    <?php } ?>
    </tbody>
</table>
</form>
</center>

<?php

Page::close(TRUE);
