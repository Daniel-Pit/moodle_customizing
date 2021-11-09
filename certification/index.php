<?php

require_once('../config.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/user/filters/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

require_login();

$sort         = optional_param('sort', 'name', PARAM_ALPHANUM);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
$ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
$lu           = optional_param('lu', '2', PARAM_INT);            // show local users


require_once('./lib.php');

add_navbar();

$PAGE->set_url('/certification/index.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagetype('certification');
$PAGE->set_pagelayout('standard');


// create the user filter form
$ufiltering = new user_filtering();
echo $OUTPUT->header();


// Carry on with the user listing
$context = context_system::instance();
// These columns are always shown in the users list.
$requiredcolumns = array('grade', 'certificate');
// Extra columns containing the extra user fields, excluding the required columns (city and country, to be specific).
$extracolumns = get_extra_user_fields($context, $requiredcolumns);
// Get all user name fields as an array.
$allusernamefields = get_all_user_name_fields(false, null, null, null, true);
$columns = array_merge($allusernamefields, $extracolumns, $requiredcolumns);

foreach ($columns as $column) {
    $string[$column] = get_user_field_name($column);
    if ($sort != $column) {
        $columnicon = "";
        // if ($column == "name") {
        //     $columndir = "DESC";
        // } else {
        $columndir = "ASC";
        // }
    } else {
        $columndir = $dir == "ASC" ? "DESC" : "ASC";
        // if ($column == "name") {
        //     $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
        // } else {
        $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
        // }
        $columnicon = $OUTPUT->pix_icon(
            't/' . $columnicon,
            get_string(strtolower($columndir)),
            'core',
            ['class' => 'iconsort']
        );
    }
    $$column = "<a href=\"index.php?sort=$column&amp;dir=$columndir\">" . $string[$column] . "</a>$columnicon";
}

// We need to check that alternativefullnameformat is not set to '' or language.
// We don't need to check the fullnamedisplay setting here as the fullname function call further down has
// the override parameter set to true.
$fullnamesetting = $CFG->alternativefullnameformat;
// If we are using language or it is empty, then retrieve the default user names of just 'firstname' and 'lastname'.
if ($fullnamesetting == 'language' || empty($fullnamesetting)) {
    // Set $a variables to return 'firstname' and 'lastname'.
    $a = new stdClass();
    $a->firstname = 'firstname';
    $a->lastname = 'lastname';
    // Getting the fullname display will ensure that the order in the language file is maintained.
    $fullnamesetting = get_string('fullnamedisplay', null, $a);
}

// Order in string will ensure that the name columns are in the correct order.
$usernames = order_in_string($allusernamefields, $fullnamesetting);
$fullnamedisplay = array();
foreach ($usernames as $name) {
    // Use the link from $$column for sorting on the user's name.
    $fullnamedisplay[] = ${$name};
}
// All of the names are in one column. Put them into a string and separate them with a /.
$fullnamedisplay = implode(' / ', $fullnamedisplay);
// If $sort = name then it is the default for the setting and we should use the first name to sort by.
if ($sort == "name") {
    // Use the first item in the array.
    $sort = reset($usernames);
}

list($extrasql, $params) = $ufiltering->get_sql_filter();

// $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '',
//         $extrasql, $params, $context);
$users = get_users_listing(
    $sort,
    $dir,
    $page * $perpage,
    $perpage,
    '',
    '',
    '',
    $extrasql,
    $params,
    $context
);
$usercount = get_users(false);
$usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

if ($extrasql !== '') {
    echo $OUTPUT->heading("$usersearchcount / $usercount " . get_string('users'));
    $usercount = $usersearchcount;
} else {
    echo $OUTPUT->heading("$usercount " . get_string('users'));
}


$baseurl = new moodle_url('/certification/index.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

flush();

//add user rows in table
if (!$users) {
    $match = array();
    echo $OUTPUT->heading(get_string('nousersfound'));

    $table = NULL;
} else {

    $table = new html_table();
    $table->head = array();
    $table->colclasses = array('leftalign', 'leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
    $table->head[] = $fullnamedisplay;
    $table->attributes['class'] = 'admintable generaltable table-sm';
    foreach ($extracolumns as $field) {
        $table->head[] = ${$field};
    }
    $table->head[] = 'Grade';
    $table->head[] = 'Certificate';
    $table->head[] = 'Profile';
    $table->head[] = "";


    $table->id = "users";
    foreach ($users as $user) {
        $profileButtons = array();
        $certificateButtons = array();
        $gradeButtons = array();
        $lastcolumn = '';

        $url = new moodle_url('../user/profile.php', array('id' => $user->id));
        $profileButtons[] = html_writer::link($url, $OUTPUT->pix_icon('i/portfolio', null));

        checking_issued_certificate($user->id);

        $certificateCount = get_certificate_count($user->id);

        $courseCount= get_course_count($user->id);

        if ($courseCount > 0) {
            $url = new moodle_url('./admin_certificate_list.php', array('id' => $user->id));
            $certificateButtons[] = html_writer::link($url, $certificateCount . '/'.$courseCount.' '. $OUTPUT->pix_icon('i/badge', null));
        } else {
            $certificateButtons[] = '' ;
        }

        $url = new moodle_url('./quiz_list.php', array('id' => $user->id));
        $gradeButtons[] = html_writer::link($url,$OUTPUT->pix_icon('i/grades', null));

        $fullname = fullname($user, true);

        $row = array();
        $row[] = "<a href=\"./publicinfo.php?username=$user->username\">$fullname</a>";
        foreach ($extracolumns as $field) {
            $row[] = s($user->{$field});
        }
        $row[] = implode(' ', $gradeButtons);
        $row[] = implode(' ', $certificateButtons);
        $row[] = implode(' ', $profileButtons);
        $row[] = $lastcolumn;
        $table->data[] = $row;
    }
}

// add filters
$ufiltering->display_add();
$ufiltering->display_active();

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
