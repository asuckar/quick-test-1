<?php

    require_once "filemanager.php";

    if(!isset($_FILES['files']))
        http_response_code(400);

    $fls=$_FILES['files'];
    $total = count($_FILES['files']['name']);
    if($total==0)
        http_response_code(400);
    $failedFiles = array();

    for($i=0; $i<$total; ++$i) {
        if(!handleUploadedFile($fls, $i))
            $failedFiles[]=basename($fls["name"][$i]);
    }

    die(json_encode(array("error" => false, "failed_files" => $failedFiles)));