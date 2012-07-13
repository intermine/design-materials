<?php
/**
 * A mock Absence object
 *
 * User: radek
 * Date: 16/06/11
 * Time: 15:02
 */
 
class Absence {

    /** @var hold Absence values */
    private $data = array();

    public $numDays = 0;

    /**
     * Setup new Absence object
     * @param  $start
     * @param  $startnoon
     * @param  $stop
     * @param  $stopnoon
     * @param  $comment
     * @param  $type
     * @param null $id
     * @param null $numDays
     * @param null $workerID
     */
    function __construct ($start, $startnoon, $stop, $stopnoon, $comment, $type, $id=NULL, $numDays=NULL, $workerID=NULL) {
        $this->start = $start;
        $this->startnoon = $startnoon;
        $this->stop = $stop;
        $this->stopnoon = $stopnoon;
    }

    /**
     * @return the Absence currently defined
     */
    function saveAbsence() {
        return $this->data;
    }

    /**
     * Will return a worker's current absence that AddEditHoliday expects based on $rows
     * @static
     * @param array $rows as if coming from a database
     * @return array preformatted for AddEditHoliday
     */
    public static function getWorkerAbsence(array $rows) {
        $r = array();
        # populate an array dict of holiday timestamps
        foreach ($rows as $row) {
            # convert them into timestamps of "taken" time taking into account half days
            $from = ($row['startnoon'] == 1) ? strtotime("+12 hours", strtotime($row['start'])) :
                    strtotime($row['start']);
            $to = ($row['stopnoon'] == 1) ? strtotime("+12 hours", strtotime($row['stop'])) :
                    strtotime("+23 hours 59 minutes 59 seconds", strtotime($row['stop'])); # finish before end of next day
            $r[] = array('from' => $from, 'to' => $to);
        }
        
        return $r;
    }

    /**
     * Magic overloading setter, ignore
     * @param  $name
     * @param  $value
     * @return void
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

}
