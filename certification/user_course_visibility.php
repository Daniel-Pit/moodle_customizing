<?php

 require_once('../config.php');
require_once('../certification/lib.php');

if (isset($_POST['id']) && isset($_POST['visible'])) {
    echo $_POST['id'];
    echo $_POST['visible'];
    set_course_visibility($_POST['id'], $_POST['visible']);
    
}

?>
