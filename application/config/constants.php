<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
/**
 * Custom defines
 */
//ROLES
defined('ID_ROL_SUPER_ADMIN')   or define('ID_ROL_SUPER_ADMIN', 99);
defined('ID_ROL_MANAGER')   or define('ID_ROL_MANAGER', 2);
defined('ID_ROL_ACCOUNTING')   or define('ID_ROL_ACCOUNTING', 3);
defined('ID_ROL_SAFETY')   or define('ID_ROL_SAFETY', 4);
defined('ID_ROL_WORKORDER')   or define('ID_ROL_WORKORDER', 5);
defined('ID_ROL_SUPERVISOR')   or define('ID_ROL_SUPERVISOR', 6);
defined('ID_ROL_BASIC')   or define('ID_ROL_BASIC', 7);
defined('ID_ROL_ENGINEER')   or define('ID_ROL_ENGINEER', 8);
defined('ID_ROL_MECHANIC')   or define('ID_ROL_MECHANIC', 9);
defined('ID_ROL_ACCOUNTING_ASSISTANT')   or define('ID_ROL_ACCOUNTING_ASSISTANT', 10);
//NOTIFICATIONS
defined('ID_NOTIFICATION_CERTIFICATION')   or define('ID_NOTIFICATION_CERTIFICATION', 1);
defined('ID_NOTIFICATION_FLHA')   or define('ID_NOTIFICATION_FLHA', 2);
defined('ID_NOTIFICATION_TOOL_BOX')   or define('ID_NOTIFICATION_TOOL_BOX', 3);
defined('ID_NOTIFICATION_PLANNING')   or define('ID_NOTIFICATION_PLANNING', 4);
defined('ID_NOTIFICATION_MAINTENANCE')   or define('ID_NOTIFICATION_MAINTENANCE', 5);
defined('ID_NOTIFICATION_PAYROLL')   or define('ID_NOTIFICATION_PAYROLL', 6);
defined('ID_NOTIFICATION_TIMESHEET')   or define('ID_NOTIFICATION_TIMESHEET', 7);
defined('ID_NOTIFICATION_DAYOFF')   or define('ID_NOTIFICATION_DAYOFF', 8);
defined('ID_NOTIFICATION_HAULING')   or define('ID_NOTIFICATION_HAULING', 9);
defined('ID_NOTIFICATION_INCIDENT')   or define('ID_NOTIFICATION_INCIDENT', 10);
defined('ID_NOTIFICATION_WORKORDER')   or define('ID_NOTIFICATION_WORKORDER', 11);
defined('ID_NOTIFICATION_INSPECTIONS')   or define('ID_NOTIFICATION_INSPECTIONS', 12);
defined('ID_NOTIFICATION_WORKORDER_CHANGE')   or define('ID_NOTIFICATION_WORKORDER_CHANGE', 13);
defined('ID_NOTIFICATION_NEW_JOB')   or define('ID_NOTIFICATION_NEW_JOB', 14);
defined('ID_NOTIFICATION_HOURS_PAYROLL_CHECK')   or define('ID_NOTIFICATION_HOURS_PAYROLL_CHECK', 15);
//MODULES
defined('ID_MODULE_SERVICE_ORDER')   or define('ID_MODULE_SERVICE_ORDER', 1);
defined('DASHBOARD_MAINTENANCE_LIST')   or define('DASHBOARD_MAINTENANCE_LIST', 2);
defined('INSPECTION_LIST_BY_EQUIPMENT_ID')   or define('INSPECTION_LIST_BY_EQUIPMENT_ID', 3);
//WO STATUS
defined('ON_FIELD')   or define('ON_FIELD', 0);
defined('IN_PROGRESS')   or define('IN_PROGRESS', 1);
defined('REVISED')   or define('REVISED', 2);
defined('SEND_TO_CLIENT')   or define('SEND_TO_CLIENT', 3);
defined('CLOSED')   or define('CLOSED', 4);
defined('ACCOUNTING')   or define('ACCOUNTING', 5);
