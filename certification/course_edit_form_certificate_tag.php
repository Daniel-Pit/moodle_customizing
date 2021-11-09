<?php

// Form definition with Certificate.
$mform->addElement('header', 'certificatehdr', 'Certificate');
$mform->setExpanded('certificatehdr');

$mform->addElement('text', 'qualification', get_string('qualification'), 'maxlength="254" size="50"');
$mform->addHelpButton('qualification', 'qualification');
$mform->addRule('qualification', get_string('missingqualification'), 'required', null, 'client');
$mform->setType('qualification', PARAM_TEXT);
$mform->setDefault('qualification', get_course_qualification($course->id));


$mform->addElement('text', 'issuername', get_string('issuername'), 'maxlength="254" size="50"');
$mform->addHelpButton('issuername', 'issuername');
$mform->addRule('issuername', get_string('missingissuername'), 'required', null, 'client');
$mform->setType('issuername', PARAM_TEXT);
$mform->setDefault('issuername', get_course_issuername($course->id));

$mform->addElement('text', 'issuerposition', get_string('issuerposition'), 'maxlength="254" size="50"');
$mform->addHelpButton('issuerposition', 'issuerposition');
$mform->addRule('issuerposition', get_string('missingissuerposition'), 'required', null, 'client');
$mform->setType('issuerposition', PARAM_TEXT);
$mform->setDefault('issuerposition', get_course_issuerposition($course->id));



?>