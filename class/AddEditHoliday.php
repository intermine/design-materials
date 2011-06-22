<?php
/**
 * Determines what holiday to add/edit given pre-existing holidays
 *
 * User: radek
 * Date: 16/06/11
 * Time: 12:45
 */

include_once "HolidayDate.php";

class HolidayException extends Exception { }

class AddEditHoliday {

    /** @var start of the holiday period */
    private $start;
    /** @var end of the holiday period */
    private $stop;
    /** @var does the start of the holiday period start at noon? */
    private $start_noon;
    /** @var does the end of the holiday period end at noon? */
    private $stop_noon;
    /** @var comment on the absence */
    private $comment;
    /** @var type of the absence */
    private $type;

    /** @var they are just numbers to us... */
    private $workerID;
    /** @var the absence the worker has taken */
    public $workerAbsence;

    /** @var number of days holiday taken */
    public $days_taken;

    /** @var holds record of all holidays saved */
    public $savedHoliday = array();

    /** @var holds a reference to an old Absence we are editing (for logger purposes) */
    private $oldAbsence;

    /**
     * Setup the vars we will be dealing with and do a simple check on validity
     * @param  $start
     * @param  $stop
     * @param  $start_noon
     * @param  $stop_noon
     * @param  $comment
     * @param  $type
     * @param  $workerID
     */
    function __construct($start, $stop, $start_noon, $stop_noon, $comment, $type, $workerID) {
        # time travel not allowed
        if ($start > $stop) {
            throw new HolidayException("No time travelling for you!");
        }

        if (($start == $stop) && ($start_noon == $stop_noon)) {
            $start_noon = $stop_noon = 0;
        }

        # save them values
        $this->start = $start;
        $this->stop = $stop;
        $this->start_noon = $start_noon;
        $this->stop_noon = $stop_noon;
        $this->comment = $comment;
        $this->type = $type;
        $this->workerID = $workerID;
    }

    /**
     * Add a new holiday
     * @param null $oldAbsence
     * @return void
     */
    function add($oldAbsence=null) {
        # get absence for the worker
        if (!isset($this->workerAbsence)) $this->workerAbsence = Absence::getWorkerAbsence($this->workerID);

        $this->oldAbsence = $oldAbsence;

        # run this thing
        $this->__run();
    }

    /**
     * Edit an existing holiday
     * @param  $holidayID
     * @param null $oldAbsence
     * @return void
     */
    function edit($holidayID, $oldAbsence=null) {
        # get absence for the worker minus this holiday
        if (!isset($this->workerAbsence)) $this->workerAbsence = Absence::getWorkerAbsence($this->workerID, $holidayID);

        $this->oldAbsence = $oldAbsence;

        # delete the old holiday
        if (isset($this->oldAbsence)) {
            $this->oldAbsence->cancel();
        }

        # run this thing
        $this->__run();

        # TODO if we do not save anything, save the original thing back... I know
    }

    # TODO change the internal structure to date?
    protected function __run() {
        # convert dates into timestamps
        $date = $begin = ($this->start_noon == 1) ? strtotime("+12 hours", strtotime($this->start)) : strtotime($this->start);
        $end = ($this->stop_noon == 1) ? strtotime("+12 hours", strtotime($this->stop)) :
                strtotime("+1 day", strtotime($this->stop)); # finish before end of next day

        $newHoliday = array();
        # traverse each day between the start and the end day and check it is unclaimed
        while($date < $end) {
            $ok = true;
            # traverse all our absence
            foreach ($this->workerAbsence as $holiday) {
                # is this day already in a holiday?
                if ($date >= $holiday['from'] && $date < $holiday['to']) {
                    $ok = false;

                    # insert interrupt if not one there already and not the beginning
                    if (end($newHoliday) != '-' && count($newHoliday) != 0) {
                        # but first the ending time on the day minus 1
                        array_push($newHoliday, date("Y-m-d H:i:s", $date));
                        array_push($newHoliday, '-');
                    }

                    # continue traversing the dates
                    break;
                }
            }

            # is this part of the day ok after checking all holiday?
            if ($ok) {
                array_push($newHoliday, date("Y-m-d H:i:s", $date));
            }

            # move 12 hours within the date to check for next range
            $date = strtotime("+12 hours", $date);
        }

        # do not forget the end
        if (end($newHoliday) != '-') {
            if (end($newHoliday) != date("Y-m-d H:i:s", $end)) {
                array_push($newHoliday, date("Y-m-d H:i:s", $end));
            }
            # end it right here
            array_push($newHoliday, '-');
        }

        # traverse new holiday dates to save
        $size = count($newHoliday);
        if ($size > 2) {

            $begin = $newHoliday[0];
            for ($i = 0; $i < $size; $i++) {
                $h = $newHoliday[$i];

                # have we found an interrupt?
                if ($h == '-') {

                    # convert into objects
                    $startTime = new StartHolidayDate($begin);
                    $endTime = new EndHolidayDate($newHoliday[$i-1]);
                    
                    # finally, save
                    $newAbsence = new Absence(
                        $startTime->toDbDate(),
                        $startTime->getHalfDay(),
                        $endTime->toDbDate(),
                        $endTime->getHalfDay(),
                        $this->comment, $this->type,
                        NULL,NULL,
                        $this->workerID);
                    
                    $result = $newAbsence->saveAbsence();
                    if ($result) {
                        # push so we can unit test the result
                        array_push($this->savedHoliday, $result);
                    }
                    $this->days_taken += $newAbsence->numDays;

                    # log
                    if (isset($this->oldAbsence)) EventLog::getLog()->saveEvent($newAbsence, $this->oldAbsence);

                    # a new beginning?
                    if ($size > $i + 1) {
                        $begin = $newHoliday[$i+1];
                    }
                }
            }
        }
    }

}
