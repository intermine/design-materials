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

// on post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bha->updateAllowance($_POST);

    # any messages?
    require_once "class/Messaging.php";
    $messages = Messaging::get();
    if ($messages) {
        echo '<ul id="messages">';
        foreach ($messages as $message) {
            printf("<li class='%s'>%s</li>", $message['type'], $message['text']);
        }
        echo '</ul>';
    }
}

?>

<center>
<form method="post">
<table id="edit-planner">
    <thead>
        <tr>
            <td></td>
            <th><?php echo $bha->year-1; ?></th>
            <th><?php echo $bha->year; ?></th>
            <th><?php echo $bha->year+1; ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($bha->getAllowance() as $userid => $map) { ?>
    <tr>
        <td><?php echo $map['name']; ?></td>
        <?php foreach ($map['allowance'] as $year => $allowance) { ?>
            <td><input type="text" name="<?php echo $userid.'['.$year.']'; ?>" value="<?php echo $allowance; ?>" /></td>
        <?php } ?>
    </tr>
    <?php } ?>
    </tbody>
</table>
<input type="submit" value="Save">
</form>
</center>

<?php

Page::close(TRUE);