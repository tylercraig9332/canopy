<?php

  /**
   * Main command class for Calendar module
   *
   * @author Matthew McNaney <mcnaney at gmail dot com>
   * @version $Id$
   */

PHPWS_Core::initModClass('calendar', 'View.php');
PHPWS_Core::requireConfig('calendar');

if (!defined('CALENDAR_MONTH_LISTING')) {
    define('CALENDAR_MONTH_LISTING', '%B');
 }

define('MINI_CAL_NO_SHOW', 1);
define('MINI_CAL_SHOW_FRONT', 2);
define('MINI_CAL_SHOW_ALWAYS', 3);


class PHPWS_Calendar {
    var $today        = 0;
    var $month        = 0;
    var $day          = 0;
    var $year         = 0;
    var $request_date = 0;
    var $current_view = DEFAULT_CALENDAR_VIEW;

    // object for controlling user requests
    var $user     = NULL;

    // object controlling administrative requests
    var $admin    = NULL;

    // view object for displaying calendars
    var $view         = NULL;

    function PHPWS_Calendar() {
        // using server time
        $this->loadToday();
        $this->loadRequestDate();
        $this->loadCurrentView();
    }

    function loadCurrentView()
    {
        if (isset($_REQUEST['view'])) {
            $this->current_view = $_REQUEST['view'];
        } else {
            if (isset($_REQUEST['id'])) {
                $this->current_view = 'event';
            }
        }
    }

    /**
     * Loads todays unix time and date info 
     */
    function loadToday()
    {
        $atime = PHPWS_Time::getTimeArray();
        $this->today        = &$atime['u'];
        $this->request_date = $this->today;
        $this->month        = &$atime['m'];
        $this->day          = &$atime['d'];
        $this->year         = &$atime['y'];
    }

    /**
     * Loads the date requested by user
     */
    function loadRequestDate()
    {
        $change = FALSE;

        if (isset($_REQUEST['y'])) {
            $this->year = (int)$_REQUEST['y'];
            $change = TRUE;
        } elseif (isset($_REQUEST['year'])) {
            $this->year = (int)$_REQUEST['year'];
            $change = TRUE;
        }

        if (isset($_REQUEST['m'])) {
            $this->month = (int)$_REQUEST['m'];
            $change = TRUE;
        } elseif (isset($_REQUEST['month'])) {
            $this->month = (int)$_REQUEST['month'];
            $change = TRUE;
        }


        if (isset($_REQUEST['d'])) {
            $this->day = (int)$_REQUEST['d'];
            $change = TRUE;
        } elseif (isset($_REQUEST['day'])) {
            $this->day = (int)$_REQUEST['day'];
            $change = TRUE;
        }


        if ($change) {
            $this->request_date = PHPWS_Time::convertServerTime(mktime(0,0,0, $this->month, $this->day, $this->year));
            if ($this->request_date < mktime(0,0,0,1,1,1970)) {
                $this->loadToday();
            } else {
                $this->month = (int)date('m', $this->request_date);
                $this->day   = (int)date('d', $this->request_date);
                $this->year  = (int)date('Y', $this->request_date);
            }
        }
    }

    function loadSchedule($personal=FALSE)
    {

        if ($personal) {
            $this->schedule = & new Calendar_Schedule;
            $db = & new PHPWS_DB('calendar_schedule');
            $db->addWhere('user_id', Current_User::getId());
            $result = $db->loadObject($this->schedule);
            $this->schedule->calendar = & $this;
            return $result;
        } else {
            PHPWS_Core::initModClass('calendar', 'Schedule.php');
            if (isset($_REQUEST['schedule_id'])) {
                $this->schedule = & new Calendar_Schedule($_REQUEST['schedule_id']);
            } else {
                $this->schedule = & new Calendar_Schedule;
            }
            $this->schedule->calendar = & $this;
            return TRUE;
        }
    }

    /**
     * Directs the user (non-admin) functions for calendar
     */
    function user()
    {
        $content = $title = NULL;
        /*
            PHPWS_Core::initModClass('calendar', 'User.php');
            $this->user = & new Calendar_User;
            $this->user->calendar = & $this;
            $this->user->main();
        */

        if (isset($_REQUEST['schedule_id'])) {
            $this->loadSchedule();
            if (!$this->schedule->allowView()) {
                Current_User::disallow();
            }
        }

        if (isset($_REQUEST['uop'])) {
            $command = $_REQUEST['uop'];
        } else {
            $command = 'view';
        }

        switch ($command) {
            
        case 'view':
            $content = $this->view();
            break;
        }

        $template['CONTENT'] = $content;
        $template['TITLE']   = $title;
        $final = PHPWS_Template::process($template, 'calendar', 'user_main.tpl');
        Layout::add($final);
    }


    function view()
    {
        $this->loadView();

        switch ($this->current_view) {
        case 'day':
            $content = $this->view->day();
            break;
            
        case 'full':
        case 'month_grid':
            $content = $this->view->month_grid();
            break;

        case 'month_list':
            $content = $this->view->month_list();
            break;

        case 'week':
            $content = $this->view->week();
            break;

        case 'event':
            $event_id = (int)$_REQUEST['id'];

            if (isset($_REQUEST['js'])) {
                $content = $this->view->event($event_id, true);
                Layout::nakedDisplay($content);
                return;
            } else {
                $content = $this->view->event($event_id);
            }
            break;
        default:
            $content = _('Incorrect option');
            break;
        }

        return $content;
    }


    /**
     * Directs the administrative functions for calendar
     */
    function admin()
    {
        PHPWS_Core::initModClass('calendar', 'Admin.php');
        $Calendar->admin = & new Calendar_Admin;
        $Calendar->admin->calendar = & $this;
        $Calendar->admin->main();
    }


    function checkDate($date)
    {
        if ( empty($date) || $date < gmmktime(0,0,0, 1, 1, 1970)) {
            $date = & $this->today;
        }

        return $date;
    }

    function loadView()
    {
        $this->view = & new Calendar_View;
        $this->view->calendar = & $this;
    }


    function getPublicCalendars()
    {
        $db = & new PHPWS_DB('calendar_schedule');
        $db->addWhere('public_schedule', 1);
        $db->addColumn('id');
        return $db->select('col');
    }

    function getEvents($start_search=NULL, $end_search=NULL, $schedules=NULL) {
        PHPWS_Core::initModClass('calendar', 'Event.php');
        if (!isset($start_search)) {
            $start_search = mktime(0,0,0,1,1,1970);
        } 

        if (!isset($end_search)) {
            // if this line is a problem, you need to upgrade
            $end_search = mktime(0,0,0,1,1,2050);
        }

        $db = & new PHPWS_DB('calendar_events');
        $db->setDistinct(TRUE);

        if (!empty($schedules)) {
            $db->addWhere('calendar_schedule_to_event.schedule_id', $schedules, NULL, NULL, 'schedule');
            $db->addWhere('id', 'calendar_schedule_to_event.event_id', NULL, NULL, 'schedule');
        }

        $db->addWhere('start_time', $start_search, '>=', NULL, 'start');
        $db->addWhere('start_time', $end_search,   '<',  'AND', 'start');

        $db->addWhere('end_time', $end_search,   '<=', 'NULL', 'end');
        $db->addWhere('end_time', $start_search, '>', 'AND', 'end');

        $db->setGroupConj('end', 'OR');

        $db->groupIn('start', 'end');

        $db->addOrder('start_time');
        $db->addOrder('end_time desc');
        $db->setIndexBy('id');

        $result = $db->getObjects('Calendar_Event');

        if (PEAR::isError($result)) {
            PHPWS_Error::log($result);
            return NULL;
        }

        return $result;
    }

    function &getDay()
    {
        require_once 'Calendar/Day.php';
        $oDay = & new Calendar_Day($this->year, $this->month, $this->day);
        $oDay->build();
        return $oDay;
    }
        

    function &getWeek()
    {
        require_once 'Calendar/Week.php';

        $oWeek = & new Calendar_Week($this->year, $this->month, $this->day, CALENDAR_START_DAY);
        $oWeek->build();
        return $oWeek;
        
    }
    
    function &getMonth()
    {
        require_once 'Calendar/Month/Weekdays.php';
        $oMonth = & new Calendar_Month_Weekdays($this->year, $this->month, PHPWS_Settings::get('calendar', 'starting_day'));
        $oMonth->build();
        return $oMonth;
    }

    // Checks the user cookie for a hour format
    function userHourFormat()
    {
        $hour_format = PHPWS_Cookie::read('calendar', 'hour_format');

        if (empty($hour_format)) {
            return PHPWS_Settings::get('calendar', 'default_hour_format');
        } else {
            return $hour_format;
        }
    }

    function getMonthArray()
    {
        for ($i=1; $i < 13; $i++) {
            if ($i < 10) {
                $value = '0' . $i;
            } else {
                $value = &$i;
            }
            $months[$value] = strftime(CALENDAR_MONTH_LISTING, mktime(0,0,0,$i));
        }

        return $months;
    }

    function getDayArray()
    {
        for ($i=1; $i < 32; $i++) {
            if ($i < 10) {
                $value = '0' . $i;
            } else {
                $value = &$i;
            }

            $days[$value] = $i;
        }

        return $days;
    }

    function getYearArray()
    {
        $year_start = (int)date('Y') - 2;
        $year_end = $year_start + 11;
        
        for ($i = $year_start; $i < $year_end; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

}

?>