<?php

checking_issued_certificate($userid);


$Certificates = get_user_Certificates_listing($userid);


$html= "<script src='$CFG->wwwroot/lib/jquery/jquery-3.4.1.min.js'></script>";
$html.="<script src='$CFG->wwwroot/certification/user_edit_course.js'></script>";

$html .= html_writer::empty_tag('hr');
$html.=$OUTPUT->heading('Course Setting',4, 'fheader');


//add user rows in table
if (!$Certificates) {
    $match = array();
    $table = NULL;
} else {
    $table = new html_table();
    $table->head = array();
    $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign','centeralign', 'centeralign');
    $table->head[] = 'Course';
    $table->attributes['class'] = 'admintable generaltable table-sm';
    $table->head[] = 'Certificate';
    $table->head[] = 'Visible';
    $table->head[] = 'Share';

    $table->id = "certification_list";
    $table->data = $Certificates;
}

if (!empty($table)) {
    $html.=html_writer::start_tag('div', array('class' => 'no-overflow'));
    $html.=html_writer::table($table);
    $html.=html_writer::end_tag('div');
}

