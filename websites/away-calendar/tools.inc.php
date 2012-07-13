<?php

//
function img ($src,$w,$h,$alt,$border=-1,$align="")
{
    return "<img src=\"".$src."\" width=\"".$w."\" height=\"".$h."\" alt=\"".
        $alt."\"".(($border!=-1)? " border=\"".$border."\"" : "").
        (($align!="")? " align=\"".$align."\"" : "")."/>";
}


//
function sql2timestamp ($date)
{
    preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg);
    return mktime(0,0,0,$reg[2],$reg[3],$reg[1]);
}


//
function sql2human_date ($date)
{
    if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg))
        return $reg[3]."/".$reg[2]."/".$reg[1];
    else
        return "";
}


function sql2human_datetime ($date)
{
    if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d:\d\d)/",$date,$reg))
        return $reg[3]."/".$reg[2]."/".$reg[1]." ".$reg[4];
    else
        return "";
}


//
function human2sql ($date)
{
    if (preg_match("/(\d\d?)\/(\d\d?)\/(\d\d\d\d)/",$date,$reg))
        return date("Y-m-d",mktime(0,0,0,$reg[2],$reg[1],$reg[3]));
    else
        if (preg_match("/(\d\d?)\/(\d\d?)\/(\d\d)/",$date,$reg))
            return date("Y-m-d",mktime(0,0,0,$reg[2],$reg[1],2000+$reg[3]));
        else
            return 0;
}


class Tools
{
    static function dateYMD2Timestamp ($date)
    {
        preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg);
        return mktime(0,0,0,$reg[2],$reg[3],$reg[1]);
    }

    static function dateYMD2DDMY ($date)
    {
        preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg);
        return date("D d/m/Y",mktime(0,0,0,$reg[2],$reg[3],$reg[1]));
    }

    static function days ($nb,$Text=TRUE)
    {
        $nb=preg_replace("/\.0$/","",preg_replace("/(\.\d)0$/","$1",$nb));
        return $nb.($Text? (" day".(($nb>1)? "s" : "")) : "");
    }

    static function dateYMDHM2HumanDateTime ($date)
    {
        if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d:\d\d)/",$date,$reg))
            return $reg[3]."/".$reg[2]."/".$reg[1]." ".$reg[4];
        else
            return "";
    }

    static function dateYMD2HumanDate ($date)
    {
        if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/",$date,$reg))
            return $reg[3]."/".$reg[2]."/".$reg[1];
        else
            return "";
    }
}


?>
