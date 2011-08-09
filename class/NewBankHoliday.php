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
        $res = Db::getConnection()->query("INSERT INTO bankholiday (day, name) ".
                                          "VALUES ('".$date."', '".$name."')");
    }

}

