<?php

echo "
<link rel='preconnect' href='https://fonts.googleapis.com'>
<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
<link href='https://fonts.googleapis.com/css2?family=Orbitron:wght@581&display=swap' rel='stylesheet'>
    <div id='banner_container' class='banner-container'>
        <div class='content'>
            <div id='banner' class='banner'>
                <div id='canvas' class='canvas'>
                    <div class='certificate default us-letter landscape'>
                        <div id='paper' class='certificate-inner' style='transform: scale(0.511364);'>
                            <div class='certificate-bg'>

                                <canvas id='drawcanvas' width=2000 height=1540>
                                    
                                </canvas>
                            </div>
                            <div class='certificate-block top1'>
                                <span id='certify_label1'> techstudio </span>
                            </div>
                            <div class='certificate-block italic top2'>
                                <span id='certify_label2'> Certificate of Completion</span>
                            </div>
                            <div class='certificate-block top3'>
                                <span id='certify_label3'> THIS IS TO CERTIFY THAT</span>
                            </div>
                            <div class='certificate-block top4'>
                                <span id='certify_name'>$username </span>
                            </div>
                            <div class='certificate-block top5'>
                                <span id='certify_course'> $courseDescription </span>
                            </div>
                            <div class='certificate-block top6'>
                                <span id='certify_coursename'> $coursename </span>
                            </div>
                            <div class='certificate-block top7'>
                                <span id='certify_contents'> $qualification </span>
                            </div>
                            <div class='certificate-block top8'>
                                <span id='certify_issuedate'> $issuedate </span>
                            </div>
                            <div class='certificate-block top9'>
                                <span id='certify_tokennumber'>$tokennumber</span>
                            </div>
                            <hr class='certificate-block line1'>
                            <div class='certificate-block top10'>
                                <span id='certify_label4'> Certificate token number</span>
                            </div>
                            <div class='certificate-block top11'>
                                <span id='certify_issuername'> $issuername</span>
                            </div>
                            <hr class='certificate-block line2'>
                            <div class='certificate-block top12'>
                                <span id='certify_issuerposition'> $issuerposition </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/certification/badge_paper.css' />";
echo "<script src='$CFG->wwwroot/lib/jquery/jquery-3.4.1.min.js'></script>";
echo "<script src='$CFG->wwwroot/certification/badge_paper.js'></script>";

