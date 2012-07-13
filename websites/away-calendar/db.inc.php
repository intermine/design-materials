<?

require_once "config.inc.php";

class DbResult
{
    protected $result;

    function __construct ($result)
    {
        $this->result=$result;
    }

    function fetchArray ()
    {
        return mysql_fetch_array($this->result);
    }

    function getValue ()
    {
        return mysql_result($this->result,0);
    }

    function numRows ()
    {
        return mysql_num_rows($this->result);
    }
}


class Db
{
    static protected $connection;
    protected $handle;

    protected function __construct ()
    {
        global $cfgUser,$cfgPasswd,$cfgHost,$cfgPort,$cfgDBName;

        $this->handle=mysql_pconnect($cfgHost.(($cfgPort)? ":".$cfgPort : ""),
                $cfgUser,$cfgPasswd);
        mysql_select_db($cfgDBName);
    }

    static function getConnection ()
    {
        if (!isset(self::$connection))
            self::$connection=new Db();
        return self::$connection;
    }

    function closeConnection ()
    {
        if (isset($this->handle))
            mysql_close($this->handle);
    }

    function query ($string)
    {
        return new DbResult(mysql_query($string));
    }
}



// ----------------------------------------------------------------------------

// Execute a query
function db_query($string)
{
    return mysql_query($string);
}


// Fetch data row
function db_fetch_array($res)
{
    return mysql_fetch_array($res);
}


// Fetch 1 result
function db_result($res)
{
    return mysql_result($res,0);
}


// Nb of rows for the query
function db_num_rows($res)
{
    return mysql_num_rows($res);
}


// Nb of fields for the query
function db_num_fields($res)
{
    return mysql_num_fields($res);
}


// Field name for the query
function db_field_name($res,$index)
{
    return mysql_field_name($res,$index);
}


// Field size for a table given
function db_field_len($table, $field)
{
    $r=mysql_query("SELECT ".$field." FROM ".$table." LIMIT 1");
    return mysql_field_len($r,0);
}


//
function db_escape_string($string)
{
    return mysql_escape_string($string);
}

// ----------------------------------------------------------------------------

//
function db_query1_1 ($db)
{
    return mysql_query("SELECT userid,fullname ".
		       "FROM worker ORDER BY fullname");
}


// ----------------------------------------------------------------------------

//
function db_query3_3 ($db,$userid,$start,$startnoon,$stop,$stopnoon,$num_days,$type,$comment)
{
    return mysql_query("INSERT INTO planner(userid,start,startnoon,stop,".
		       "stopnoon,nb,type,comment) ".
	       "VALUES ('".$userid."','".$start."','".$startnoon.
	       "','".$stop."','".$stopnoon."','".$num_days."','".$type.
              "','".$comment."')");
}


// ----------------------------------------------------------------------------

// Load a bank holiday
function db_query_bkhol ($db,$day)
{
    return mysql_query("INSERT INTO bankholiday VALUES('".$day."')");
}


?>
