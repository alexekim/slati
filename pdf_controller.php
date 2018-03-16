<?php
require_once('data_controller.php');
require_once("dom2pdf/dompdf_config.inc.php");


function generatePDF($html, $name, $isLandscape = false, $size = 'letter'){
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    if($isLandscape){
        $dompdf->set_paper($size, "landscape");
    }
    $dompdf->render();
    $dompdf->stream($name);
}

function getPDFHTML($type, $data, $appendixType = null){
    switch($type):
        case('stateDetail'):
            $html = getStateDetailPDF($data);
            break;
        case('search'):
            $html = getSearchResultsPDFHTML($type, $data);
            break;
        case('comparison'):
            $html = getSearchResultsPDFHTML($type, $data);
            break;
        case('appendix'):
            $html = getAppendixPDFHTML($appendixType, $data);
            break;
        case('medicaid'):
        case('pic'):
        case('sehp'):
            $html = getDetailPDFHTML($type, $data);
            break;
        case('overview'):
        default:
            $html = getOverviewPDFHTML($data);
            break;
    endswitch;
    return $html;
}

function getAppendixPDFHTML($type, $data){
    $html = '';
    switch($type):
    default:
        $html = $data;
        break;
    endswitch;
    return $html;
}

function getDetailPDFHTML($type, $stateID){
    $sb = new StringBuilder();
    $htmlData = getStateHTMLData($stateID, true);
    $lastUpdate = getLastUpdate();
    $html = '';
    $stateName = $htmlData["stateName"];
    $title = '';
    $detailDataHTML = '';

    switch($type):
        case('medicaid'):
            $title = 'Medicaid Data Sources';
            $detailDataHTML .= $htmlData["medicaidCoverage"];
            $detailDataHTML .= $htmlData["medicaidBarrier"];
            $detailDataHTML .= $htmlData["medicaidAddlSources"];
            break;
        case('pic'):
            $title = 'Private Insurance Coverage Details';
            $detailDataHTML .= $htmlData["picCategory"];
            $detailDataHTML .= $htmlData["picCoverage"];
            $detailDataHTML .= $htmlData["picAddlSources"];
            break;
        case('sehp'):
        $title = 'State Employee Health Plan Data Sources';
            $detailDataHTML .= $htmlData["healthplanCoverage"];
            $detailDataHTML .= $htmlData["healthplanBarrier"];
            $detailDataHTML .= $htmlData["healthplanAddlSources"];
            break;
        default:
            // do nothing, should never get here unless they have been tampering with the url
            break;
    endswitch;

    $sb->add('<link href="http://'.server.dir.'css/styles-pdf.css" media="screen" rel="stylesheet" type="text/css"/>');
    $sb->add('<div class="stateWrap">');
    $sb->add('<div class="header">');
	$sb->add('<table width="100%">');
	$sb->add('<tr>');
	$sb->add('<td width="70%">');
	$sb->add('<h1>'.$htmlData["stateName"].'</h1>');
	$sb->add('</td>');
	$sb->add('<td>');
	$sb->add('<img src="http://'.server.dir.'imgs/header-logo.jpg" />');
	$sb->add('</td>');
	$sb->add('</tr>');
	$sb->add('</table>');
    $sb->add('<div class="tabBody">');
    $sb->add('<h4>'.$title.'</h4>');
    $sb->add($detailDataHTML);
    $sb->add('<div>Database Last Updated: '.$lastUpdate.'</div>');
    $sb->add('</div>');
    $sb->add('</div>');
    $sb->add('</div>');
    $html = $sb->getString();
    return $html;

}

function getStateDetailPDF($stateID){
    $sb = new StringBuilder();
    $htmlData = getStateHTMLData($stateID, true);
    $lastUpdate = getLastUpdate();
    $html = '';
	$sb->add('<link href="http://'.server.dir.'css/styles-pdf.css" media="screen" rel="stylesheet" type="text/css"/>');
	$sb->add('<div class="stateWrap">');
	$sb->add('<div class="header">');
	$sb->add('<table width="100%">');
	$sb->add('<tr>');
	$sb->add('<td width="70%">');
	$sb->add('<h1>'.$htmlData['stateName'].'</h1>');
	$sb->add('</td>');
	$sb->add('<td>');
	$sb->add('<img src="http://'.server.dir.'imgs/header-logo.jpg" />');
	$sb->add('</td>');
	$sb->add('</tr>');
    $sb->add('</table>');  
	$sb->add('</div>');
	$sb->add('<div id="overview" class="tabBody">');
    $sb->add($htmlData['catHTML']);
	$sb->add('</div>');
    $html = $sb->getString();
    $html = convertChars($html);
    return $html;
}

function getOverviewPDFHTML($data){
    $sb = new StringBuilder();
    $lastUpdate = getLastUpdate();
    $html = $data;
	$sb->add('<link href="http://'.server.dir.'css/styles-pdf.css" media="screen" rel="stylesheet" type="text/css"/>');
	$sb->add('<div class="stateWrap">');
	$sb->add('<div class="header">');
	$sb->add('<table width="100%">');
	$sb->add('<tr>');
	$sb->add('<td width="70%">');
	$sb->add('<h1>SLATI Overview</h1>');
	$sb->add('</td>');
	$sb->add('<td>');
	$sb->add('<img src="http://'.server.dir.'imgs/header-logo.jpg" />');
	$sb->add('</td>');
	$sb->add('</tr>');
    $sb->add('</table>');  
	$sb->add('</div>');
	$sb->add('<div id="overview" class="tabBody">');
    $sb->add($html);
	$sb->add('</div>');
    $html = $sb->getString();
    return $html;
}

function getSearchResultsPDFHTML($type, $tableHTML){
    $sb = new StringBuilder();
    $header = ($type=='comparison'?'Comparison Results':'Search Results');
    $html = '';
    $sb->add('<link href="http://'.server.dir.'css/styles-pdf.css" media="screen" rel="stylesheet" type="text/css"/>');
    $sb->add('<div class="stateWrap">');
    $sb->add('<div class="header">');
	$sb->add('<table width="100%">');
	$sb->add('<tr>');
	$sb->add('<td width="70%">');
	$sb->add('<h1>'.$header.'</h1>');
	$sb->add('</td>');
	$sb->add('<td>');
	$sb->add('<img src="http://'.server.dir.'imgs/header-logo.jpg" />');
	$sb->add('</td>');
	$sb->add('</tr>');
	$sb->add('</table>');
    $sb->add($tableHTML);
    $sb->add('</div>');
    $sb->add('</div>');
    $sb->add('</div>');
    $html = $sb->getString();
    return $html;
}

?>
