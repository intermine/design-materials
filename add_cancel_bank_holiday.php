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

// get the bank holidays
require_once('class/BankHolidays.php');
$bh = new BankHolidays();

// on get cancel
if ($_GET['cancel']) {
    $bh->cancel($_GET['cancel']);
}

// on post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo '<ul id="messages">';

    // gimme the parts
    $date = explode('-', $_POST['date']);
    // validate the date
    if (count($date) == 3 && checkdate($date[1], $date[2], $date[0])) {
        if (!$bh->existsAlready($_POST['date'])) {
            $bh->insert($_POST['date'], $_POST['name']);
            printf("<li class='%s'>Bank holiday %s <b>saved</b>.</li>", 'ok', $_POST['date']);
        } else {
            printf("<li class='%s'>Bank holiday %s already exists.</li>", 'warning', $_POST['date']);
        }
    } else {
        printf("<li class='%s'>%s is not a valid date.</li>", 'alert', $_POST['date']);
    }
    
    echo '</ul>';
}

?>

<center>
<form method="post">
<table cellspacing="1" cellpadding="5" id="userbox">
    <tr>
        <td align="right">Date:</td>
        <td align="left">
            <input type="text" name="date" size="10" maxlength="10" value="YYYY-MM-DD"/>
        </td>
    </tr>
    <tr>
        <td align="right">Name:</td>
        <td align="left">
            <input type="text" name="name"/>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <input type="submit" value="Insert">
        </td>
    </tr>
</table>
</form>
</center>

<center>
<table id="form" cellspacing="0" cellpadding="5">
    <thead>
        <tr><th colspan="4">Bank holidays between <?php echo $bh->year-1; ?> and <?php echo $bh->year+1; ?></th></tr>
    </thead>
    <tbody>
    <?php foreach ($bh->getHolidays() as $day => $name) { $switch = !$switch; ?>
    <tr class="coul<?php echo ($switch) ? 0 : 1; ?>">
        <td><?php echo $day; ?></td>
        <td><?php echo $name; ?></td>
        <td class="cancel"><a href="add_cancel_bank_holiday.php?cancel=<?php echo $day; ?>" class="cancel">Cancel</a></td>
    </tr>
    <?php } ?>
    </tbody>
</table>
</center>

<?php

Page::close(TRUE);
