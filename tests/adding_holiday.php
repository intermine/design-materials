<?php

# simpletest
require_once('simpletest/autorun.php');

# mock absence object
require_once('AbsenceMock.php');

# testing this
require_once('../class/AddEditHoliday.php');

class TestOfAdding extends UnitTestCase {

    private function __adder($db, $start, $stop, $startNoon, $stopNoon) {
        # create new holiday
        $holiday = new AddEditHoliday($start, $stop, $startNoon, $stopNoon, 'Test', 'V', 666);

        # absence for the worker
        $holiday->workerAbsence = Absence::getWorkerAbsence($db);

        # add the holiday
        $holiday->add();

        return $holiday->savedHoliday;
    }

    function atestAddNewHoliday1() {
        $this->assertTrue($this->__adder(array(), "2011-01-01", "2011-01-02", 0, 0) == array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0)
        ));
    }

    function atestAddNewHoliday2() {
        $this->assertTrue($this->__adder(array(), "2011-08-09", "2011-08-09", 0, 0) == array(
            array('start' => '2011-08-09', 'startnoon' => 0, 'stop' => '2011-08-09', 'stopnoon' => 0)
        ));
    }

    function atestAddNewHoliday3() {
        $this->assertTrue($this->__adder(array(), "2011-08-31", "2011-08-31", 0, 0) == array(
            array('start' => '2011-08-31', 'startnoon' => 0, 'stop' => '2011-08-31', 'stopnoon' => 0)
        ));
    }

    function atestAddHolidayOverlap1() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 0)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-02", 0, 0) == array(
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0)
        ));
    }

    function atestAddHolidayOverlap2() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 0)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-03", 0, 0) == array(
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0)
        ));
    }

    function atestAddHolidayOverlap3() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-03", 0, 0) == array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 1, 'stop' => '2011-01-03', 'stopnoon' => 0)
        ));
    }

    function atestAddHolidayOverlap4() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1),
            array('start' => '2011-01-04', 'startnoon' => 0, 'stop' => '2011-01-07', 'stopnoon' => 0)
        );
        $this->assertTrue($this->__adder($taken, "2010-12-30", "2011-01-08", 1, 0) == array(
            array('start' => '2010-12-30', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 1, 'stop' => '2011-01-03', 'stopnoon' => 0),
            array('start' => '2011-01-08', 'startnoon' => 0, 'stop' => '2011-01-08', 'stopnoon' => 0)
        ));
    }

    function atestAddHolidayOverlap5() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-03", 0, 1) == array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 0)
        ));
    }
    
    function atestAddHolidayOverlap6() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-03", 1, 1) == array(
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 1)
        ));
    }
    
    function atestAddHolidayOverlap7() {
        $taken = array(
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-03", 1, 0) == array(
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 1),
            array('start' => '2011-01-03', 'startnoon' => 1, 'stop' => '2011-01-03', 'stopnoon' => 0)
        ));
    }
    
    function atestAddHolidayOverlap8() {
        $taken = array(
            array('start' => '2011-01-05', 'startnoon' => 0, 'stop' => '2011-01-05', 'stopnoon' => 1),
            array('start' => '2011-01-02', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 1),
            array('start' => '2011-01-04', 'startnoon' => 0, 'stop' => '2011-01-04', 'stopnoon' => 1),
            array('start' => '2011-01-03', 'startnoon' => 1, 'stop' => '2011-01-03', 'stopnoon' => 0),
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 0)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-05", 1, 0) == array(
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1),
            array('start' => '2011-01-04', 'startnoon' => 1, 'stop' => '2011-01-04', 'stopnoon' => 0),
            array('start' => '2011-01-05', 'startnoon' => 1, 'stop' => '2011-01-05', 'stopnoon' => 0)
        ));
    }

    function testAddHolidayOverlap9() {
        $taken = array(
            array('start' => '2010-12-31', 'startnoon' => 0, 'stop' => '2010-12-31', 'stopnoon' => 0),
            array('start' => '2011-01-01', 'startnoon' => 0, 'stop' => '2011-01-01', 'stopnoon' => 0),
            array('start' => '2011-01-02', 'startnoon' => 1, 'stop' => '2011-01-02', 'stopnoon' => 0),
            array('start' => '2011-01-02', 'startnoon' => 0, 'stop' => '2011-01-02', 'stopnoon' => 1),
            array('start' => '2011-01-04', 'startnoon' => 0, 'stop' => '2011-01-04', 'stopnoon' => 1),
            array('start' => '2011-01-03', 'startnoon' => 1, 'stop' => '2011-01-03', 'stopnoon' => 0),
            array('start' => '2011-01-01', 'startnoon' => 1, 'stop' => '2011-01-01', 'stopnoon' => 0)
        );
        $this->assertTrue($this->__adder($taken, "2011-01-01", "2011-01-05", 1, 0) == array(
            array('start' => '2011-01-03', 'startnoon' => 0, 'stop' => '2011-01-03', 'stopnoon' => 1),
            array('start' => '2011-01-04', 'startnoon' => 1, 'stop' => '2011-01-05', 'stopnoon' => 0)
        ));
    }

}