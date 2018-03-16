<?php
include 'pdf_controller.php';

$type = $_GET['type'];
$isLandscape = false;
$size = 'letter';

if($type == 'comparison' || $type == 'search'){
    $data = urldecode($_POST['html']);
    $html = getPDFHTML($type, $data);
    $fileName = "slati-state-".$type."-results.pdf";
}elseif($type == 'appendix'){
    $data = urldecode($_POST['html']);
    $appendixType = $_POST['type'];
    $html = getPDFHTML($type, $data, $appendixType);
    $fileName = "slati-appendix-".$appendixType.".pdf";
    if($appendixType == 'b'){
        $isLandscape = true;
        $size = 'legal';
    }
}elseif($type == 'overview'){
    $data = urldecode($_POST['html']);
    $html = getPDFHTML($type, $data);
    $fileName = "slati-overview.pdf";
}else{
    $name = $_GET['sn'];
    $id = $_GET['id'];
    $html = getPDFHTML($type, $id);
    $fileName = $name."_".$type.".pdf";
}

//var_dump($html);
generatePDF($html, $fileName, $isLandscape, $size);

?>
