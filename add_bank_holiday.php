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

// on post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "class/Messaging.php";
    echo '<ul id="messages">';

    // gimme the parts
    $date = explode('-', $_POST['date']);
    // validate the date
    if (count($date) == 3 && checkdate($date[1], $date[2], $date[0])) {
        require_once('class/NewBankHoliday.php');
        if (!NewBankHoliday::existsAlready($_POST['date'])) {
            NewBankHoliday::insert($_POST['date'], $_POST['name']);
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

<?php

Page::close(TRUE);
