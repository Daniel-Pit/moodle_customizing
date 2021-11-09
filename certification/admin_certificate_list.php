<?php

require_once('../config.php');

require_login();

$userid     = optional_param('id', 0, PARAM_INT); // This are required.

require_once('./lib.php');

checking_issued_certificate($userid);


add_navbar();

$Certificates = get_admin_certificates_listing($userid);




$PAGE->navbar->add('Certificate');
$title .= 'Certificate';

$PAGE->set_url('/certification/certificate.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagetype('certification');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_user_fullname($userid));

flush();

//add user rows in table
if (!$Certificates) {
    $match = array();
    $table = NULL;
} else {
    $table = new html_table();
    $table->head = array();
    $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
    $table->head[] = 'Course';
    $table->attributes['class'] = 'admintable generaltable table-sm';
    $table->head[] = 'Token Number';
    $table->head[] = 'Issue Date';
    $table->head[] = 'Issuer';
    $table->head[] = 'Certificate';

    $table->id = "certification_list";
    $table->data = $Certificates;
}

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
