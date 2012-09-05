<?php

require_once "session.inc.php";

class Page
{
    static protected function back_button ()
    {
        echo "<a href=\"./\" class=\"back\">Back to the main menu</a>\n";
    }

    static function init ($backButton=TRUE)
    {
        $session=Session::getSession();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Away: Holiday planner</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link type="text/css" rel="stylesheet" href="away.css"/>
<link rel="shortcut icon" href="favicon.ico"/>
<style type="text/css" media="print">
#warning
{
    display: none;
}
</style>
<?=$session->headCode?>
</head>
<body bgcolor="#F4EEFF">
<?php

        if (($session->warning) && ($session->userID!=$session->workerID))
        {
            echo "<center><span id=\"warning\">You are connected as ".
                $session->worker->fullname."</span></center>\n";
        }

        if ($backButton)
            self::back_button();
        else
            $session->backButton=FALSE;

        return $session;
    }


    static function close ($backButton=NULL)
    {
        if (!isset($backButton))
            $backButton=Session::getSession()->backButton;
        if ($backButton)
            self::back_button();
    }
}

?>
