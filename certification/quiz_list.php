<?php

require_once('../config.php');

require_login();

$userid     = optional_param('id', 0, PARAM_INT); // This are required.

require_once('./lib.php');

add_navbar();

$PAGE->navbar->add('Quiz');
$title .= 'Quiz';

$PAGE->set_url('/certification/quiz_list.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagetype('certification');
$PAGE->set_pagelayout('standard');


echo $OUTPUT->header();

echo $OUTPUT->heading(get_user_fullname($userid));

$attempts= get_attempts_listing($userid);

flush();

//add user rows in table
if (!$attempts) {
    $match = array();
    $table = NULL;
} else {
    $table = new html_table();
    $table->head = array();
    $table->colclasses = array();
    $table->head[] = 'Course';
    $table->attributes['class'] = 'admintable generaltable table-sm';
    $table->head[] = 'Topic';
    $table->head[] = 'Started On';
    $table->head[] = 'State';
    $table->head[] = 'Time taken';
    $table->head[] = 'Grade/100';
    $table->head[] = 'Review';

    $table->id = "quiz_list";
    $table->data = $attempts;
}

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
