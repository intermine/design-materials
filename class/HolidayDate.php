<?php
/**
 * Take a string such as 2011-06-21 00:00:00 and split into appropriate parts
 *
 * User: radek
 * Date: 20/06/11
 * Time: 16:58
 */
 
class HolidayDate {

    /** @var original object */
    private $original;

    /** @var hour, day, month, year */
    public $hour;
    public $day;
    public $month;
    public $year;

    # TODO throw an exception on malformed string
    function __construct($string) {
        $this->original = $string;

        # convert into numbers
        $day = explode(" ", $string);
        $day[0] = explode("-", $day[0]);
        $day[1] = explode(":", $day[1]);

        $this->hour = $day[1][0];
        $this->day = $day[0][2];
        $this->month = $day[0][1];
        $this->year = $day[0][0];
    }

    /**
     * Is this a half day?
     * @return bool
     */
    function isHalfDay() {
        return ($this->hour == 12);
    }

    /**
     * Integer representation of half day
     * @return int
     */
    function getHalfDay() {
        return (int)$this->isHalfDay();
    }

    /**
     * Turn into a date for the database
     * @return void
     */
    function toDbDate() {
        return implode("-", array($this->year, $this->month, $this->day));
    }

}

class StartHolidayDate extends HolidayDate {

    function __toString() {
        return sprintf("%s %s", $this->toDbDate(), $this->isHalfDay() ? 'lunch time' : 'morning');
    }

}

class EndHolidayDate extends HolidayDate {

    function __construct($string) {
        # call daddy
        parent::__construct($string);

        # fixup the end time (move to previous day)
        if (!$this->isHalfDay()) {
            if ((int)$this->day < 10) {
                $this->day = sprintf("0%s", $this->day-1);
            } else {
                $this->day -= 1;
            }
        }
    }

    function __toString() {
        return sprintf("%s %s", $this->toDbDate(), $this->isHalfDay() ? 'lunch time' : 'evening');
    }

}
