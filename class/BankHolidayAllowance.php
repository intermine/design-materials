<?php

require_once "db.inc.php";

/**
 * User: radek
 * Date: 30/09/11
 * Time: 16:29
 *
 * Used for fetching and saving bank holiday allowance of peeps
 */

class BankHolidayAllowance {

    private $today;
    public $year;
    private $allowance;

    function __construct() {
        $this->today = getdate();
        $this->year = $this->today['year'];
    }

    /**
     * @return a map between users and allowance
     */
    function getAllowance() {
        if ($this->allowance == NULL) {
            $res = Db::getConnection()->query(sprintf("SELECT * FROM `allowance` WHERE ayear BETWEEN %d AND %d ORDER BY userid ASC",
                                                      $this->year-1, $this->year+1));
            $this->allowance = array();
            while ($row = $res->fetchArray()) {
                if (!$this->allowance[$row['userid']]) {
                    $this->allowance[$row['userid']] = array($this->year-1 => '-', $this->year => '-', $this->year+1 => '-');
                }
                $this->allowance[$row['userid']][$row['ayear']] = (float) $row['allowance'];
            }
        }
        return $this->allowance;
    }
}

