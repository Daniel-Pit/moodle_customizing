<?php

require_once('../config.php');

$token     = optional_param('token', '', PARAM_TEXT); // This are required.

require_once('./lib.php');

//$cc = $DB->get_record('custom_certificate', array('token' => $token));
$cc=$DB->get_record_sql("select * from mdl_custom_certificate where token='$token'");

if (!$cc){
    header("HTTP/1.0 404 Not Found");
    die;
}else if($cc->issuedate==0){
    header("HTTP/1.0 404 Not Found");
    die;
}

$userid     = $cc->user; // This are required.
$courseid     = $cc->course; // This are required.

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

 echo "<html><head><title>Certification</title></head><body>";
 include("./badge_paper.php");
 echo "</body></html>";

