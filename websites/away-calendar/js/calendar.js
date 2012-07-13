
var cal=new Array();

function createCalendar (name,startAYear,stopAYear)
{
    cal[name]=new Array();
    cal[name]['day']=document.getElementById(name+'_d');
    cal[name]['yearmonth']=document.getElementById(name+'_ym');
    cal[name]['pop']=new CalendarPopup('mydiv_'+name);
    cal[name]['pop'].setReturnFunction('returnCal_'+name);
    cal[name]['pop'].setWeekStartDay(1);
    cal[name]['pop'].addDisabledDates(null,(startAYear-1)+'-09-30');
    cal[name]['pop'].addDisabledDates(stopAYear+'-10-01',null);
}

function addCalendar (name,startAYear,stopAYear)
{
    document.writeln('<img src="calendar.jpg" onclick="onclickCal(\''+name+'\')" id="anchor_'+name+'">');
    document.writeln('<div id=\"mydiv_'+name+'\" class=\"cpDiv\"></div>');
    createCalendar(name,startAYear,stopAYear);
}

function mergeDate(ym,d)
{
    return ym+'-'+((d>=10)? '' : '0')+d;
}

function onclickCal (name)
{
    cal[name]['pop'].showCalendar('anchor_'+name,
            cal[name]['yearmonth'].value+'-'+cal[name]['day'].value);
}

function checkDates ()
{
    if (mergeDate(cal['start']['yearmonth'].value,cal['start']['day'].value)>
        mergeDate(cal['stop']['yearmonth'].value,cal['stop']['day'].value))
    {
        cal['stop']['day'].value=cal['start']['day'].value;
        cal['stop']['yearmonth'].value=cal['start']['yearmonth'].value;
    }
}

function returnCal_start(y,m,d)
{
    cal['start']['day'].value=d;
    cal['start']['yearmonth'].value=y+'-'+((m>=10)? '' : '0')+m;
    checkDates();
}

function returnCal_stop(y,m,d)
{
    cal['stop']['day'].value=d;
    cal['stop']['yearmonth'].value=y+'-'+((m>=10)? '' : '0')+m;
    checkDates();
}
