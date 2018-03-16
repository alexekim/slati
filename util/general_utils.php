<?php 
function getArrSplitIndex($arr){
	$numItems = count($arr);
	$half = 0;
    if($numItems){
        $half = ($numItems/2);
    }
    return $half;
}
function cleanStr($str){
    $retStr = convertChars($str);
    $retStr = linkifyURLs($retStr);
    $retStr = encodeChars($retStr);
    $retStr = newlinesToBRs($retStr);
	return $retStr;
}
function linkifyURLs($str){
    $str = preg_replace("/(http:\/\/[^\s]*)/", "<a href='$1' target='blank'>$1</a>", $str);
	return $str;
}
function encodeChars($str){
    $retStr = utf8_encode($str);
    return $retStr;
}
function newlinesToBRs($str){
    return str_replace("\n", "<br />", $str); 
}

function convertChars($string) 
{ 
    $find = array(
        "&",
    	chr(145), 
        chr(146), 
        chr(147), 
        chr(148), 
        chr(151),
        '"',
        "â€™",
        "Â®",
        "â??",
        "â?",
        "Â§",
        "â€“",
        "â€'"
        ); 
 
    $replace = array(
        "&amp;",
		"'",
        "'", 
        '"', 
        '"', 
        '-',
        "'",
        "'",
        "&reg;",
        "'",
        "'",
        "§",
        "-",
        "-"
        ); 
 
    return str_replace($find, $replace, $string); 
}
?>


