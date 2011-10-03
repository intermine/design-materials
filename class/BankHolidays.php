<?php

require_once "db.inc.php";
require_once "class/Messaging.php";

/**
 * User: radek
 * Date: 09/08/11
 * Time: 17:06
 *
 * Used for saving bank holidays into the db
 */

class BankHolidays {

    private $today;
    public $year;
    private $holidays;

    function __construct() {
        $this->today = getdate();
        $this->year = $this->today['year'];
        $this->getHolidays();
    }

    /**
     * Fetch all the holidays for current +/- 1 years
     * @return void
     */
    function getHolidays() {
        if ($this->holidays == NULL) {
            $this->holidays = array();

            // fetch from bankholiday table
            $res = Db::getConnection()->query(sprintf(
                "SELECT * FROM `bankholiday` WHERE day LIKE '%s' OR day LIKE '%s' OR day LIKE '%s' ORDER BY day ASC",
                    $this->year-1 . "-%", $this->year . "-%", $this->year+1 . "-%"));
            // merge the tables together
            while ($row = $res->fetchArray()) {
                $this->holidays[$row['day']] = $row['name'];
            }
        }
        return $this->holidays;
    }

    /**
     * Cancel a bank holiday
     * @param  $date
     * @return void
     */
    function cancel($date) {
        if (!$this->existsAlready($date)) {
            Messaging::warning(sprintf("No such bank holiday <strong>%s</strong>", $date));
        } else {
            // from bankholiday table
            Db::getConnection()->query(sprintf("DELETE FROM `bankholiday` WHERE day='%s'", $date));
            // from planner where begins and ends on the same day and is type 'B'
            Db::getConnection()->query(sprintf(
                "DELETE FROM `planner` WHERE start='%s' AND stop='%s' AND startnoon='0' AND stopnoon='0' AND type='B'",
                $date, $date));
            Messaging::ok(sprintf("Removed bank holiday <strong>%s</strong>", $date));
            // remove from internal
            unset($this->holidays[$date]);
        }
    }

    /**
     * Check if a bank holiday already exists in the database
     * @static
     * @param  $date
     * @return bool
     */
    function existsAlready($date) {
        $res = Db::getConnection()->query("SELECT day ".
                "FROM bankholiday ".
                "WHERE day='".$date."' ");
        return $res->numRows() != 0;
    }

    /**
     * Insert a bank holiday, Validate through existsAlready() first
     * @static
     * @param  $date
     * @param  $name
     * @return void
     */
    function insert($date, $name) {
        # add the entry to bankholiday table
        Db::getConnection()->query("INSERT INTO bankholiday (day, name) ".
                                          "VALUES ('".$date."', '".addslashes($name)."')");

        # now for each worker, add a holiday to them with the "B" flag on this day
        $res = Db::getConnection()->query("SELECT userid FROM worker WHERE deleted='N'");
        while ($row = $res->fetchArray()) {
            Db::getConnection()->query(sprintf(
                    "INSERT INTO planner(userid,start,startnoon,stop,stopnoon,".
                    "nb,type,comment) VALUES ('%s','%s','0','%s','0',".
                    "'0','B','%s')",
                    $row[0], $date, $date, addslashes($name)));
        }
    }

}

