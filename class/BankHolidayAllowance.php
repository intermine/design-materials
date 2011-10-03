<?php
/**
 * User: radek
 * Date: 30/09/11
 * Time: 16:29
 *
 * Used for fetching and saving bank holiday allowance of peeps
 */

require_once "db.inc.php";
require_once "class/Messaging.php";

class BankHolidayAllowance {

    private $today;
    public $year;
    private $allowance;

    function __construct() {
        $this->today = getdate();
        $this->year = $this->today['year'];
        $this->getAllowance();
    }

    /**
     * @return a map between users and allowance
     */
    function getAllowance() {
        if ($this->allowance == NULL) {
            // fetch all active users (not 'deleted')
            $res = Db::getConnection()->query("SELECT * FROM `worker` WHERE deleted='N' ORDER BY fullname ASC");

            $this->allowance = array();
            while ($row = $res->fetchArray()) {
                if (!$this->allowance[$row['userid']]) {
                    $this->allowance[$row['userid']] = array('name' => $row['fullname'],
                                                             'allowance' => array(
                                                                 $this->year-1 => 0,
                                                                 $this->year => 0,
                                                                 $this->year+1 => 0));
                }
            }

            // fetch from allowance table
            $res = Db::getConnection()->query(sprintf("SELECT * FROM `allowance` WHERE ayear BETWEEN %d AND %d ORDER BY userid ASC",
                                                      $this->year-1, $this->year+1));
            // merge the tables together
            while ($row = $res->fetchArray()) {
                if ($this->allowance[$row['userid']]) {
                    $this->allowance[$row['userid']]['allowance'][$row['ayear']] = (float) $row['allowance'];
                }
            }
        }
        return $this->allowance;
    }

    /**
     * Update the allowance per person/year
     * @param  $post
     * @return void
     */
    function updateAllowance($post) {
        // traverse all userid's
        foreach ($post as $userid => $map) {
            foreach ($map as $year => $allowance) {
                $allowance = (float) $allowance;
                // does the allowance differ from what we have?
                if ($this->allowance[$userid]['allowance'][$year] != $allowance) {
                    // db updates
                    if (Db::getConnection()->query(sprintf(
                                                       "SELECT allowance FROM allowance WHERE userid='%s' AND ayear=%d",
                                                       $userid, $year))->numRows() != 0) {
                        // update
                        Db::getConnection()->query(sprintf(
                        "UPDATE `allowance` SET allowance=%f WHERE userid='%s' AND ayear=%d", $allowance, $userid, $year));
                    } else {
                        // add
                        Db::getConnection()->query(sprintf(
                        "INSERT INTO `allowance` (userid,ayear,allowance) VALUES ('%s',%d,%f)", $userid, $year, $allowance));
                    }
                    Messaging::ok(
                        sprintf("Allowance for %s in %d set to %s", $this->allowance[$userid]['name'], $year, round($allowance, 1))
                    );

                    // internal update
                    $this->allowance[$userid]['allowance'][$year] = $allowance;
                }
            }
        }
    }

}

