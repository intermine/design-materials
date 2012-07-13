<?php

// Real to Academic Year (Oct, Nov, Dec => year+1) 
function R2AYear ($month,$ryear)
{
    return ($month>9)? $ryear+1 : $ryear;
}


function check_date ($month,$year)
{
    return (db_num_rows(db_query("SELECT day ".
            "FROM bankholiday ".
            "WHERE day like '".R2AYear($month,$year)."-01-0_' ".
            "LIMIT 1"))==1);
}


?>
