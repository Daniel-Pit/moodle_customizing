<?php

function add_navbar()
{
    global $PAGE;
    $PAGE->navbar->add('Site Pages', null);
    $title = get_string('certification');
    $cburl = new \moodle_url('/certification/index.php');
    \navigation_node::override_active_url(new \moodle_url('/certification/index.php'));
    $PAGE->navbar->add($title, $cburl);
}

function get_user_fullname($userid)
{
    global $DB;

    // warning: will return UNCONFIRMED USERS
    $user = $DB->get_records_sql("SELECT id, username, firstname, lastname FROM {user} WHERE id = $userid");
    if (!$user) return "";
    $fullName = $user[$userid]->firstname . " " . $user[$userid]->lastname;
    return $fullName;
}

function get_username($userid)
{
    global $DB;

    // warning: will return UNCONFIRMED USERS
    $user = $DB->get_records_sql("SELECT id, username FROM {user} WHERE id = $userid");
    if (!$user) return "";
    return $user[$userid]->username;
}

function get_attempts_listing($userid, $sort = 'course', $dir = 'ASC')
{
    global $DB, $CFG;

    $sql = "SELECT cm.id AS cmid,cm.course AS courseid, c.fullname AS coursefullname,c.shortname AS courseshortname, m.id AS quizid, m.name AS quizname,m.sumgrades,m.grade, m.decimalpoints, md.name AS modulename , cw.section AS sectionid, cw.name AS sectionname
              FROM mdl_course_modules cm
                   JOIN mdl_modules md ON md.id = cm.module
                   JOIN mdl_quiz m ON m.id = cm.instance
                   JOIN mdl_course c ON c.id = cm.course
                   JOIN (SELECT DISTINCT e.courseid
                      FROM mdl_enrol e
                      JOIN mdl_user_enrolments ue ON (ue.enrolid = e.id AND ue.userid = $userid)
                   ) en ON (en.courseid = cm.course)
                   LEFT JOIN mdl_course_sections cw ON cw.id = cm.section
             WHERE md.name = 'quiz'";

    $quizs = $DB->get_records_sql($sql);
    if (!$quizs) return NULL;
    $table = array();
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    foreach ($quizs as $quiz) {
        $row = array();
        
        $row[]
            = "<a href=\"$CFG->wwwroot/course/view.php?id=$quiz->courseid\">$quiz->courseshortname</a>";  //'course'
        $topic = $quiz->sectionname == '' ? $quiz->quizname : $quiz->sectionname;
        $row[]
            = "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id=$quiz->cmid\">$topic</a>"; //'topic'
        $sql = "SELECT * FROM mdl_quiz_attempts WHERE quiz = $quiz->quizid AND userid=$userid ORDER BY sumgrades DESC";
        $quiz_attempts = $DB->get_records_sql($sql);
        if (!$quiz_attempts) {
            $row[] = ''; //'state'
            $row[] = ''; //'startedon'
            $row[] = ''; //'state'
            $row[] = ''; //'timetaken'
            $row[] = ''; //'grade'
            $row[] = ''; //'review'
        } else {
            $quiz_attempts = array_values($quiz_attempts);
            $row[] = $calendartype->timestamp_to_date_string($quiz_attempts[0]->timestart, '', 99, true, true);; //'startedon'
            $row[] = $quiz_attempts[0]->state; //'state'
            if ($quiz_attempts[0]->timefinish == 0) {
                $row[] = ''; //'timetaken'
                $row[] = ''; //'grade'
                $row[] = ''; //'review'
            } else {
                $row[] = format_time($quiz_attempts[0]->timestart - $quiz_attempts[0]->timefinish); //'timetaken'
                $row[] = format_float($quiz_attempts[0]->sumgrades / $quiz->sumgrades * 100, 2); //'grade'
                $attemptid = $quiz_attempts[0]->id;
                $row[] = "<a href=\"$CFG->wwwroot/mod/quiz/review.php?attempt=$attemptid&amp;cmid=$quiz->cmid\">Review</a>"; //'review'
            }
        }
        $table[] = $row;
    }

    return $table;
}

function get_admin_certificates_listing($userid, $onlyvisible = false, $sort = 'course', $dir = 'ASC')
{
    global $DB, $CFG, $OUTPUT;

    $sql = "SELECT c.id, c.fullname FROM mdl_course c
	    JOIN mdl_role_assignments ra ON  ra.roleid=5 AND userid=$userid
	    JOIN mdl_context con ON  con.id=ra.contextid AND contextlevel=50
	    WHERE con.instanceid=c.id AND EXISTS(SELECT id FROM mdl_quiz WHERE course=c.id)";
    $courses = $DB->get_records_sql($sql);

    $table = array();
    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    foreach ($courses as $course) {
        $row = array();

        if ($onlyvisible) {
            $badge = $DB->get_record('custom_certificate', ['user' => $userid, 'course' => $course->id, 'visible' => 1]);
        } else {
            $badge = $DB->get_record('custom_certificate', ['user' => $userid, 'course' => $course->id]);
        }
        if (empty($badge)) continue;

        $courseaddinfo = $DB->get_record('custom_course_certificate_info', ['courseid' => $course->id]);

        $row[] = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</a>";  //'course'
        $row[] = $badge->token; //'tokennumber'

        if ($badge->issuedate == 0) {
            $row[] = ''; //'issuedate'
            $row[] = ''; //'issuer'
            $row[] = ''; //'badge'
        } else {
            $row[] = date("F d, Y", $badge->issuedate); //'issuedate'
            $row[] = $courseaddinfo->issuername . "/" . $courseaddinfo->issuerposition; //'issuer'
            $row[] = "<a href=\"./badge.php?userid=$userid&courseid=$course->id\">View</a>";  //'badge view'
        }
        $table[] = $row;
    }

    return $table;
}
function get_user_certificates_listing($userid, $onlyvisible = false, $sort = 'course', $dir = 'ASC')
{
    global $DB, $CFG, $OUTPUT;

    $sql="select cc.*, c.fullname from mdl_custom_certificate cc join mdl_course c on cc.course=c.id where cc.user=$userid";
    $courses = $DB->get_records_sql($sql);

    $table = array();
    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    foreach ($courses as $course) {
        $row = array();
        $row[] = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->course\">$course->fullname</a>";  //'course'
        

        if ($course->issuedate == 0) {
            $row[] = 'No badge'; //'badge'
        } else {
            $row[] = "<a href=\"$CFG->wwwroot/certification/badge.php?userid=$userid&courseid=$course->course\">View</a>";  //'badge view'
        }

        $hide = $OUTPUT->pix_icon('t/show', 'Show Course');
        $show = $OUTPUT->pix_icon('t/hide', 'Hide Course');
        $row[] = "<a  href='javascript:void(0);' data='$course->id'  class='action-show' state='" . ($course->visible ? 1 : 0) . "' show='$show' hide='$hide'>" . ($course->visible ? $show : $hide)  . "</a>";
       
        if ($course->issuedate == 0){
            $row[] = ''; //'share'
        } else {
            $row[] = "<a href=\"" . $CFG->wwwroot . "/badges/$course->token\" target='_blank'>" . $OUTPUT->pix_icon('i/badge', 'Share') . "</a>";  //'share'
        }
        $table[] = $row;
    }

    return $table;
}
function get_certificate_courses($userid)
{
    global $DB;
    $sql = "SELECT cc.*,c.fullname FROM mdl_custom_certificate cc JOIN mdl_course c ON c.id=cc.course WHERE USER=$userid AND del=0";
    return $DB->get_records_sql($sql);
}

function get_certificate_info($userid, $courseid)
{
    global $DB;

    $sql = "SELECT a.*, b.qualification,c.fullname,c.summary, d.firstname ,d.lastname FROM mdl_custom_certificate a 
	JOIN mdl_custom_course_certificate_info b ON b.courseid=a.course
	JOIN mdl_course c ON a.course=c.id
	JOIN mdl_user d ON a.user=d.id
	WHERE a.user=$userid AND a.course=$courseid";
    return $DB->get_record_sql($sql);
}

function get_certificate_count($userid, $onlyvisible = false)
{
    global $DB;
    if ($onlyvisible) {
        return $DB->count_records('custom_certificate', ['user' => $userid, 'visible' => 1]) - $DB->count_records('custom_certificate', ['user' => $userid, 'issuedate' => 0, 'visible' => 1]);
    } else {
        return $DB->count_records('custom_certificate', ['user' => $userid]) - $DB->count_records('custom_certificate', ['user' => $userid, 'issuedate' => 0]);
    }
}

function get_course_info($id)
{
    global $DB;

    return $DB->get_record('course', ['id' => $id]);
}

function get_course_count($userid, $onlyvisible = false)
{
    global $DB;

    
    if ($onlyvisible) {
        return $DB->count_records('custom_certificate',  ['user' => $userid, 'visible' => 1]);
    } else {
        return $DB->count_records('custom_certificate',  ['user' => $userid]);
    }
}

function checking_issued_certificate($userid)
{
    global $DB;
    //regarding the user, ger course where role=5 (student) and context=50(course) and exist quiz in course
    $sql = "SELECT c.id FROM mdl_course c
	    JOIN mdl_role_assignments ra ON  ra.roleid=5 AND userid=$userid
	    JOIN mdl_context con ON  con.id=ra.contextid AND contextlevel=50
	    WHERE con.instanceid=c.id AND EXISTS(SELECT id FROM mdl_quiz WHERE course=c.id)";

    $courses = $DB->get_records_sql($sql);

    //issue certificate with checking quiz_attempts
    $undelcourseid = array();
    foreach ($courses as $course) {
        issue_certificate($userid, $course->id);
        $undelcourse[] = $course->id;
    }
    //delete unrole course
    $courses = $DB->get_records('custom_certificate', ['user' => $userid]);
    $allcourseid = array();
    foreach ($courses as $course) {
        $allcourseid[] = $course->id;
    }
    $delcourseid = array_diff($allcourseid, $undelcourseid);
    $delcourseid = array_values($delcourseid);

    foreach ($delcourseid as $v) {
        $cc = $DB->get_record('custom_certificate', ['user' => $userid, 'course' => $v]);
        if (!$cc) continue;
        $cc->del = 1;
        $DB->update_record('custom_certificate', $cc);
    }
}

function exist_certificate($userid, $courseid)
{
    global $DB;
    return $DB->record_exists('custom_certificate', ['user' => $userid, 'course' => $courseid]);
}

function has_certificate($userid, $courseid)
{
    global $DB;
    $quizs = $DB->get_records('quiz', ['course' => $courseid]);
    $date = 0;
    foreach ($quizs as $quiz) {
        $quiz_attempts = $DB->get_records('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $userid], 'sumgrades DESC'); //,
        if (!$quiz_attempts) {
            return 0;
        } else {

            $quiz_attempts = array_values($quiz_attempts);
            if ($quiz_attempts[0]->sumgrades / $quiz->sumgrades * 100 < 60) {
                return 0;
            }
            if ($quiz_attempts[0]->timefinish > $date) $date = $quiz_attempts[0]->timefinish;
        }
    }
    return $date;
}

function get_certificate_issuedate($userid, $courseid)
{

    $date = has_certificate($userid, $courseid);

    if ($date) {
        return date("F d, Y", $date);
    }
    return '';
}

function issue_certificate($userid, $courseid)
{
    global $DB;

    $issuedate = has_certificate($userid, $courseid);
    $infos = $DB->get_record('custom_course_certificate_info', ['courseid' => $courseid]);
    if (!$infos) {
        $issuername = '';
        $issuerposition = '';
    } else {
        $issuername = $infos->issuername;
        $issuerposition = $infos->issuerposition;
    }
    $custom_certificate = $DB->get_record('custom_certificate', ['user' => $userid, 'course' => $courseid]);
    if (empty($custom_certificate)) {
        $token = encode_certificate_token($userid, $courseid);

        $custom_certificate = new stdClass();
        $custom_certificate->user = $userid;
        $custom_certificate->course = $courseid;
        $custom_certificate->token = $token;
        $custom_certificate->issuedate = $issuedate;
        $custom_certificate->issuername = $issuername;
        $custom_certificate->issuerposition = $issuerposition;
        $custom_certificate->del = 0;
        $custom_certificate->visible = 1;
        
        return $DB->insert_record('custom_certificate', $custom_certificate);
    } else {
        $custom_certificate->issuedate = $issuedate;
        $custom_certificate->issuername = $issuername;
        $custom_certificate->issuerposition = $issuerposition;
        if ($custom_certificate->del) {
            $custom_certificate->visible = 1;
        }
        $custom_certificate->del = 0;
        return $DB->update_record('custom_certificate', $custom_certificate);
    }
}

function encode_certificate_token($userid, $courseid)
{
    global $DB;
    $context = $DB->get_record('context', ['contextlevel' => 50, 'instanceid' => $courseid]);
    if (!$context) return null;
    $ra = $DB->get_record('role_assignments', ['contextid' => $context->id, 'userid' => $userid, 'roleid' => 5]);
    if (!$ra) return null;

    $issuedate = date_create();
    date_timestamp_set($issuedate, $ra->timemodified);
    $basedate = new DateTime("2010-01-01");
    $diffdays = $basedate->diff($issuedate)->format('%a');
    //dec 15 digit,2187year-5digit.userid-7.courseid-3 
    $dec = sprintf("%05d", $diffdays) . sprintf("%07d", $userid) . sprintf("%03d", $courseid);
    //hex 12 digit
    $token = sprintf("%012X", intval($dec, 10));
    return $token;
}

function get_course_qualification($courseid)
{

    global $DB;
    $sql = "SELECT * FROM mdl_custom_course_certificate_info WHERE courseid=$courseid ";
    $results = $DB->get_records_sql($sql);
    if (!$results) return '';
    $results = array_values($results);
    return $results[0]->qualification;
}

function get_course_issuername($courseid)
{
    global $DB;
    $sql = "SELECT * FROM mdl_custom_course_certificate_info WHERE courseid=$courseid ";
    $results = $DB->get_records_sql($sql);
    if (!$results) return '';
    $results = array_values($results);
    return $results[0]->issuername;
}

function get_course_issuerposition($courseid)
{
    global $DB;
    $sql = "SELECT * FROM mdl_custom_course_certificate_info WHERE courseid=$courseid ";
    $results = $DB->get_records_sql($sql);
    if (!$results) return '';
    $results = array_values($results);
    return $results[0]->issuerposition;
}

function update_course_certificate_info($data)
{
    global $DB;

    $DB->delete_records('custom_course_certificate_info', ['courseid' => $data->id]);

    $sql = "insert  into `mdl_custom_course_certificate_info` (`courseid`,`qualification`,`issuername`,`issuerposition`) values ($data->id,'$data->qualification','$data->issuername','$data->issuerposition')";
    $DB->execute($sql);
}

function set_course_visibility($id, $visible)
{
    global $DB;

    $sql= "UPDATE mdl_custom_certificate SET visible=$visible WHERE id = $id";

    return   $DB->execute($sql);
}
