<?php

require_once("../config.php");

$username             = optional_param('username', '', PARAM_TEXT); // User id.
$user = $DB->get_record('user', array('username' => $username));

if(!$user){
    header("HTTP/1.0 404 Not Found");
    die;
}

$id=$user->id;

include("publicinfo_paper.php");

$coursecontext = context_course::instance(2);
$usercontext   = context_user::instance($user->id, IGNORE_MISSING);

$PAGE->set_context($coursecontext);


$strpersonalprofile = get_string('personalprofile');
$strparticipants = get_string("participants");
$struser = get_string("user");

$fullname = fullname($user);

$PAGE->set_title($fullname);
$PAGE->set_heading($fullname);
$PAGE->set_pagelayout('standard');

echo $header;
echo '<div class="userprofile">';
$headerinfo = array('heading' => '<br>'.fullname($user), 'user' => $user, 'usercontext' => $usercontext);
$messaging=$CFG->messaging;
$CFG->messaging  = 0;
echo $OUTPUT->context_header($headerinfo, 1);
$CFG->messaging = $messaging;
echo html_writer::empty_tag('hr');


$courses = $DB->get_records_sql("select cc.*,c.fullname,cat.name from  mdl_custom_certificate cc
                            join mdl_course c on cc.course=c.id
                            join mdl_course_categories cat on cat.id=c.category
                            where cc.user=$id and cc.issuedate!=0 and cc.visible=1");
if(empty($courses)) {
    echo $OUTPUT->heading('No Badges');
}else{
    echo $OUTPUT->heading('Badges');
    foreach ($courses as $course) {
        echo '<li class="list-group-item course-listitem" data-region="course-content" data-course-id="2" id="yui_3_17_2_1_1636140009304_487">
                <div class="row-fluid" id="yui_3_17_2_1_1636140009304_486">
                    <div class="col-md-11 col-md-11 d-flex align-items-center" id="yui_3_17_2_1_1636140009304_485">
                        <div>
                            <div class="text-muted muted d-flex flex-wrap">
                                <span class="sr-only">Course category</span>
                                <span class="categoryname">'.$course->name.'</span>
                            </div>
                            <a href="'.$CFG->wwwroot.'/badges/'.$course->token.'" target="_blank" class="aalink coursename">
                                <span id="favorite-icon-2-15" data-region="favourite-icon" data-course-id="2">
                                    <span class="text-primary hidden" data-region="is-favourite" aria-hidden="true">
                                        <i class="icon fa fa-star fa-fw " title="Starred course"     aria-label="Starred course"></i>
                                        <span class="sr-only">Course is starred</span>
                                    </span>
                                </span>
                                <span class="sr-only">Course name</span>'. $course->fullname.
                            '</a>
                        </div>
                    </div>
                </div>
            </li>';
    }
}
echo '</div>';  // Userprofile class.


echo $footer;