<?php

require_once('../config.php');

require_login();

$userid     = optional_param('userid', 0, PARAM_INT); // This are required.
$courseid     = optional_param('courseid', 0, PARAM_INT); // This are required.

require_once('./lib.php');

add_navbar();

$returnurl = new \moodle_url('/certification/index.php');
if (!exist_certificate($userid,$courseid)) redirect($returnurl);

$cburl = new \moodle_url('/certification/badge.php', array('id' => $userid));
$PAGE->navbar->add('Bages');
$title .= 'Bages';

$PAGE->set_url('/certification/badge.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagetype('certification');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

$info=get_certificate_info($userid,$courseid);
$username= $info->firstname . ' ' . $info->lastname;

$description='';
$newstr = str_replace('</p>', '<br>', $info->summary);
$newstr=strip_tags($newstr,'<br>');

$lines= explode('<br>',$newstr);

foreach ($lines as $line) {
    $line=trim($line);
    if(strlen($line)!=0)   $description.= $line . '<br>';

}

$courseDescription= $description;
$coursename= $info->fullname;
$qualification= $info->qualification;
$issuedate = date("F d, Y", $info->issuedate);
$tokennumber= $info->token;
$issuername= $info->issuername;
$issuerposition= $info->issuerposition;

include("./badge_paper.php");

echo $OUTPUT->footer();
