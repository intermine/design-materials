<?php

require_once "db.inc.php";

/**
 * User: radek
 * Date: 09/08/11
 * Time: 17:06
 *
 * Used for saving bank holidays into the db
 */

class NewBankHoliday {

    /**
     * Check if a bank holiday already exists in the database
     * @static
     * @param  $date
     * @return bool
     */
    static function existsAlready($date) {
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
    static function insert($date, $name) {
        # add the entry to bankholiday table
        Db::getConnection()->query("INSERT INTO bankholiday (day, name) ".
                                          "VALUES ('".$date."', '".$name."')");

        # now for each worker, add a holiday to them with the "B" flag on this day
        $res = Db::getConnection()->query("SELECT userid FROM worker WHERE deleted='N'");
        while ($row = $res->fetchArray()) {
            Db::getConnection()->query(sprintf(
                    "INSERT INTO planner(userid,start,startnoon,stop,stopnoon,".
                    "nb,type,comment) VALUES ('%s','%s','0','%s','0',".
                    "'1','B','%s')",
                    $row[0], $date, $date, $name));
        }
    }

}
