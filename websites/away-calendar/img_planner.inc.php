<?php

require_once "planner.inc.php";
require_once "worker.inc.php";
require_once "cal_month.inc.php";


function img_planner (CalMonth $cal)
{
    $WD=array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");

    $planner=new Planner();
    $workers=new Workers();

    // Top-Left
    $x0=0;
    $y0=0;

    // Size of cells
    $sx=$sy=28;

    // Top-Left of the table / Bottom-Right of the headers
    $x1=$x0+72;
    $y1=$y0+32;

    // Bottom-Right
    $x2=$sx*$cal->numDays+$x1;
    $y2=$sy*$workers->count+$y1;

    $im=imagecreatetruecolor($x2+1,$y2+1);
    $colBg=imagecolorallocate($im,240,240,255);
    imagefilledrectangle($im,0,0,$x2,$y1,$colBg);
    imagefilledrectangle($im,0,0,$x1,$y2,$colBg);
    $colBg2=imagecolorallocate($im,255,255,255);
    imagefilledrectangle($im,$x1,$y1,$x2,$y2,$colBg2);
    $colToday=imagecolorallocate($im,255,0,0);
    $colTodayAlpha=imagecolorallocatealpha($im,255,0,0,116);
    $colHorLine=imagecolorallocate ($im,128,128,128);
    $colFrame=imagecolorallocate ($im,0,0,0);
    $colWE=imagecolorallocate($im,216,216,216);
    $col=array();

    $user=array();

    // Table: vertical lines
    imageline($im,$x0,$y1,$x0,$y2,$colFrame);
    for ($i=0;$i<$cal->numDays;$i++)
    {
        imageline($im,$x1+$i*$sx,$y0,$x1+$i*$sx,$y2,$colFrame);
        imagestring($im,2,$x1+($i+0.2)*$sx,$y0+4,$WD[($cal->dayBase+$i)%7],
                $colFrame);
        imagestring($im,2,$x1+($i+0.4-(((1.0+$i)>9)? 0.1 : 0))*$sx,$y0+16,1+$i,
            $colFrame);
    }
    imageline($im,$x2,$y0,$x2,$y2,$colFrame);

    // Week-ends
    for ($i=1;$i<=$cal->numDays;$i++)
    {
        if ($cal->isWeekend($i))
            imagefilledrectangle($im,$x1+($i-1)*$sx+1,$y1,
                    $x1+$i*$sx-1,$y2,$colWE);
    }

    // Workers
    $i=0;
    foreach($workers->list as $worker)
    {
        $user[$worker->id]=$i;
        imagestring($im,2,$x0+4,$y1+($i+.3)*$sy,
                utf8_decode($worker->fullname),$colFrame);
        $i++;
    }

    foreach ($planner->getPlanner($cal) as $abs)
    {
        $idx_beg=$cal->date2Idx($abs->start,$abs->startnoon,FALSE);
        $idx_end=$cal->date2Idx($abs->stop,$abs->stopnoon,TRUE);

        $line=$user[$abs->workerID];

        for ($i=$idx_beg; $i<=$idx_end; $i++)
        {
            $t=$abs->type;

            if (isset($col[$t]))
                $c=$col[$t];
            else
            {
                $rgb=AbsenceType::getColour($t);
                $c=$col[$t]=imagecolorallocate($im,
                            hexdec(substr($rgb,1,2)),
                            hexdec(substr($rgb,3,2)),
                            hexdec(substr($rgb,5,2)));
            }

            $ofs=($i==$idx_beg)? 1 : 0;
            imagefilledrectangle($im,
                    $x1+$i*($sx/2)+$ofs,$y1+$line*$sy,
                    $x1+($i+1)*($sx/2)-1,$y1+($line+1)*$sy-1,
                    $c);
        }

        $session=Session::getSession();
        if ((AbsenceType::isPrivate($t))
                && ($abs->workerID!=$session->workerID)
                && ($abs->workerID!=$session->userID)
                && ($abs->comment))
            $abs->comment="(private)";

        echo "<area shape=\"rect\" coords=\"".
            ($x1+$idx_beg*($sx/2)).",".($y1+$line*$sy).",".
            ($x1+($idx_end+1)*($sx/2)).",".($y1+($line+1)*$sy-1)."\" ".
            "onmouseover=\"return omo('".$t."','".
            addslashes($abs->comment)."');\" ".
            "onmouseout=\"return nd();\">\n";
    }

    // Table: horizontal lines
    imageline($im,$x1,$y0,$x2,$y0,$colFrame);
    for ($i=$workers->count;$i>0;$i--)
        imageline($im,$x0,$y1+$i*$sy,$x2,$y1+$i*$sy,$colHorLine);

    imageline($im,$x0,$y2,$x2,$y2,$colFrame);
    imageline($im,$x0,$y1,$x2,$y1,$colFrame);
    imageline($im,$x2,$y0,$x2,$y2,$colFrame);

    // Today
    if (($cal->month==date("m")) && ($cal->year==date("Y")))
    {
        $i=date("d");
        imagerectangle($im,$x1+($i-1)*$sx,$y0,$x1+$i*$sx,$y2,$colToday);
        imagefilledrectangle($im,$x1+($i-1)*$sx,$y0,$x1+$i*$sx,$y2,
                $colTodayAlpha);
    }

    return $im;
}


function generate_image ($month,$ryear)
{
    if (!check_date($month,$ryear))
    {
        echo "Bad date!!!!";
        exit;
    }

    $cal=new CalMonth($month,$ryear);

    define("IMG_DYNAMIC","img/dynamic/");
    $filename=IMG_DYNAMIC.$ryear."-".$month.".png";
    if (!is_writable(IMG_DYNAMIC))
        echo "Error: Please make '<tt>".IMG_DYNAMIC."</tt>' writable by server!";
    echo "<map name=\"boxes\">\n";
    imagepng(img_planner($cal),$filename);
    echo "</map>\n";

    return $filename;
}

?>
