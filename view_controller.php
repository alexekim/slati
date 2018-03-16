<?php 
include_once 'data_controller.php';
include_once 'util/xml_parse.php';
include_once 'util/general_utils.php';
include_once 'util/stringBuilder.php';

function getStateHTMLData($sID, $isPDF){
    $htmlBlocks = array();
    $sData = getStateDataFromXML($sID);
    $htmlBlocks["stateName"] = $sData["Name"];
    $htmlBlocks["stateAbbrev"] = $sData["Abbrev"];

    $dataTables = getDataTablesArr();
    $slatiByCategory = parseToByCategory($sData["qryXMLSLATIDetail"], true);
    $htmlBlocks = array_merge($htmlBlocks, renderCategoryHTML($slatiByCategory, $isPDF));

    return $htmlBlocks;
}

function getLatestUpdateStamp(){
    $date = getLastUpdate();
    $text = '<div id="dbinfo" align="left">Last Updated: 07/16/2013</div>';
    return $text;
}

function getAppendixTableHTML($appendix){
    $htmlArr = array();
    switch($appendix){
        case 'b':
            $appendixData = getAppendixBData();
            $headers = $appendixData['headerRow'];
            $stateData = $appendixData['data'];
            $keyData = $appendixData['key'];
            $htmlArr["table1"] = renderAppendixTable($headers, $stateData, true, true, true);
            $htmlArr["key"] = renderAppendixBKey($keyData);
        break;
        case 'c':
            $appendixData = getAppendixCData();
            $t1Headers = $appendixData['t1HeaderRow'];
            $t1StateData = $appendixData['t1data'];
            $t1Html = renderAppendixTable($t1Headers, $t1StateData, false);
            $t2Headers = $appendixData['t2HeaderRow'];
            $t2StateData = $appendixData['t2data'];
            $t2Html = renderAppendixTable($t2Headers, $t2StateData, false);
            $htmlArr["table1"] = $t1Html;
            $htmlArr["table2"] = $t2Html;
        break;
        case 'd':
            $appendixData = getAppendixDData();
            $t1Headers = $appendixData['t1HeaderRow'];
            $t1StateData = $appendixData['t1data'];
            $t1Html = renderAppendixTable($t1Headers, $t1StateData, false);
            $t2Headers = $appendixData['t2HeaderRow'];
            $t2StateData = $appendixData['t2data'];
            $t2Html = renderAppendixTable($t2Headers, $t2StateData, false);
            $htmlArr["table1"] = $t1Html;
            $htmlArr["table2"] = $t2Html;
        break;
        case 'e':
            $appendixData = getAppendixEData();
            $headers = $appendixData['headerRow'];
            $stateData = $appendixData['data'];
            $htmlArr["table1"] = renderAppendixTable($headers, $stateData);
        break;
        case 'f':
            $appendixData = getAppendixFData();
            $headers = $appendixData['headerRow'];
            $stateData = $appendixData['data'];
            $htmlArr["table1"] = renderAppendixTable($headers, $stateData);
        break;
        case 'overview':
            $appendixData = getSLATIOverviewData();
            $htmlArr["overviewData"] = $appendixData;
        break;
        default:
        break;
    }
    return $htmlArr;
}
// expects headers to be list of column names
// state data as data[columnName] = data to display
function renderAppendixTable($headers, $data, $showStateCol = true, $wrapAllHeaders = false, $useDataCSSClass = false){
    $cssClass = '';
    if($useDataCSSClass == true){
        $cssClass = ' class="data"';
    }
    $sb = new StringBuilder();
    $sb->add('<table><thead><tr>');
    if($showStateCol){
        $sb->add('<th>State</th>');
    }
    foreach($headers as $type){
        if($wrapAllHeaders == true){
            $type = str_replace(' ', '<br/>', $type);
        }
        $sb->add('<th'.$cssClass.'>'.$type.'</th>');
    }
    $sb->add('</tr></thead>');
	$i = 1;
    foreach($data as $name => $stateData){
        if($i%2 == 0){
			$sb->add('<tr class="even">');
		}else{
			$sb->add('<tr>');
		}
		$i++;
		if($showStateCol){
            $sb->add('<td>'.$name.'</td>');
        }
        foreach($headers as $type){
            $typeData = '';
            if(array_key_exists($type, $stateData)){
                $typeData = $stateData[$type];
            }
            $sb->add('<td'.$cssClass.'>'.$typeData.'</td>');
        }
        $sb->add('</tr>');
    }
    $sb->add('</table>');
    $html = $sb->getString();
    return $html;
}

function renderAppendixBKey($keyData){
    $sb = new StringBuilder();
    foreach($keyData as $k => $data){
        $sb->add('<b><font color="#003366">'.$k.'</font></b><font size="1">='.$data.';</font>');
    }
    return $sb->getString();
}

function renderCategoryHTML($data, $isPDF){
    $toReturn = Array();
    $sb = new StringBuilder();
    $jumpSB = new StringBuilder();
    $jumpSB->add('<a name="top" />');
    foreach($data as $catKey => $cat){
        $catTitle = '';
        $catSB = new StringBuilder();
        foreach($cat as $coverage){
            //var_dump($coverage);
            $citation = '';
            if(array_key_exists('Citation', $coverage)){
                $citation = '<p>'.$coverage['Citation'].'</p>';
            }
            if(array_key_exists('Description', $coverage)){
                $catSB->add('<div class="cat"><h3>'.$coverage['Subcategory'].'</h3>');
                $catSB->add('<p>'.$coverage['Description'].'</p>'.$citation.'</div>');
            }elseif(array_key_exists('HasLawOrRestrictionDescription', $coverage)){
                $catSB->add('<div class="cat"><h3>'.$coverage['Subcategory'].'</h3>');
                $catSB->add('<p>'.$coverage['HasLawOrRestrictionDescription'].'</p>'.$citation.'</div>');
            }
            $catTitle = $coverage['Category'];
        }
        $jumpSB->add('<a href="#jump'.$catKey.'">'.$catTitle.'</a><br/>');
        $sb->add('<h2><a name="jump'.$catKey.'">'.$catTitle.'</a></h2>');
        $sb->add($catSB->getString());
        if(!$isPDF){
            $sb->add('<div align="right" class="asmall"><a href="#top">Back To Top</a></div>');
        }
    }
    $toReturn['jumpLinksHTML'] = $jumpSB->getString();
    $toReturn['catHTML'] = cleanStr($sb->getString());
    //var_dump($retStr);
    return $toReturn;
}

function parseAttrsToHTML($sData, &$htmlBlocks){
	foreach($sData as $row){
		$htmlBlocks[$row["AttributeType"]] = cleanStr($row["AttributeDescription"]);
	}
	return $htmlBlocks;
}

function getStateQLData($sData, $isPDF){
    $retStr = '';
    $valArray = Array();
    $qlData = Array();
    foreach($sData['qryXMLQuitlineCategory'] as $rec){
        $qlData[$rec['Coverage']] = $rec;
    } 
    $valArray['contactInfo'] = linkifyURLs($qlData['Contact Information']['ExplanationNotes']);
    $valArray['hours'] = linkifyURLs($qlData['Hours']['ExplanationNotes']);
    $valArray['counselingEligibility'] = linkifyURLs($qlData['Eligibility requirements for counseling']['ExplanationNotes']);
    $valArray['counselingEligibilityVal'] = decodeCoverageValue($qlData['Eligibility requirements for counseling']['CoverageValue']);

    $retStr = <<<QLInfo
<p>Contact Information: {$valArray['contactInfo']}</p>
<p>Hours: {$valArray['hours']}</p>
<p>Eligibility to recieve counseling: {$valArray['counselingEligibility']}</p>
QLInfo;
    return $retStr;
}

function getOverviewCoverageListHTML($data, $includeMissing, $includeNo, $isPDF = false){
    $sb = new StringBuilder();
    $coveragesToSkip = getCoveragesToSkip();
    $html = array();

    $start =     $isPDF? '<table><tr><td><table>'     : '<ul>';
    $end =       $isPDF? '</table></td></tr></table>' : '</ul>';
    $halfEnd =   $isPDF? '</table></td>'              : '</ul>';
    $halfStart = $isPDF? '<td><table>'                : '<ul>';
    $itemStart = $isPDF? '<tr><td>'                   : '<li>';
    $itemEnd =   $isPDF? '</td></tr>'                 : '</li>';
    $separator = $isPDF? '</td><td>'                  : '';

    foreach($data as $row){
        if(in_array($row["Coverage"], $coveragesToSkip)) continue;
        if(isset($row["CoverageValue"])){
            $rawCovVal = $row["CoverageValue"];

            if(isset($row['DisplayOnWebInd'])) if($row['DisplayOnWebInd'] == 0) continue;
            if($includeNo && $rawCovVal == 'no') continue;

            $covVal = getCoverageValueHTML($rawCovVal, $isPDF);
            $rowStr = $itemStart.$covVal.$separator.$row["Coverage"].$itemEnd;
            $html[$row["CoverageSubtypeDisplayOrder"]] = $rowStr;
        }
    }

    if(!count($html)) return '';

    ksort($html);
    $html = array_values($html);
    
    $sb->add($start);
    $i = 1;
    $split = false;
    $half = getArrSplitIndex($html);
    foreach($html as $line){
        $sb->add($line);
        if(!($split) && $i >= $half){
            $sb->add($halfEnd);
            $sb->add($halfStart);
            $split = true;
        }
        $i++;
    }
    $sb->add($end);
    $retStr = $sb->getString();
    return $retStr;
}

function generateDataTable($data, $type, $colType, $printIfBlank){
    $sb = new StringBuilder();
    $tmpStr = "";
    if(count($data)||$printIfBlank){
    	$cols = getTableCols($colType);
        $sb->add("<table>");
        $tmpStr = "<tr class='headers'>";
        $i = 0;
        foreach($cols["display"] as $name => $value){
        	$class = "";
        	if($i == 1){
        		$class = " class='second'";
        	}
            if($name!=""){
                $tmpStr .= "<th".$class.">".$name."</th>";
            }
            $i++;
        }
        $sb->add($tmpStr);
        $tmpStr = "";
        if(count($data)){
            foreach($data as $row){
                if(!isset($row["Coverage"])) continue;
            	if(!isValidRow($row["Coverage"])) continue;
                $tmpStr = "<tr>";
                foreach($cols["display"] as $name => $data){
                    $str = "";
                    if($data == "dataSource"){
                        $str = "";
                        if(isset($row["WebURL"])){
                            $url = $row["WebURL"];
                        }else{
                            $url = null;
                        }
                        $src = null;
                        if(isset($row["SourceNotes"])){
                            $src = $row["SourceNotes"];
                        }elseif(isset($row["Source"])){
                            $src = $row["Source"];
                        }
                        $src = cleanStr($src);
                        if($url){
                            $str .= "<a href='".$url."' target='blank'>".$src."</a>";
                        }elseif($src){
                            $str .= $src;
                        }                    		
                    }else{
                        if(isset($row[$data])){
                            $str = cleanStr($row[$data]);
                            if($data == "CoverageValue"){
                                $str = decodeCoverageValue($str);	
                            }
                        }
                    }    
                    $tmpStr .= "<td>".$str."</td>";
                }    
                $tmpStr .="</tr>";
                $sb->add($tmpStr);
            }
        }else{ //if print if blank and no data
            $sb->add("<tr><td>No Data to Display</td></tr>");
        }
        $sb->add("</table>");
        return $sb->getString();
    }else return "";
}

function getStatesAsOpts(){
	$sb = new StringBuilder();
	$states = getStateListData();
	foreach($states as $s){
		$name = $s["Name"];
		$id = $s["FipsCode"];
		$sb->add("<option value='".$id."'>".$name."</option>");
	}
	return $sb->getString();
}

function renderStateList($states){
    $sb = new StringBuilder();
    $split = false;
    $half = getArrSplitIndex($states);
    if($half < 7)$split = true;
    $i=1;
    foreach($states as $s){
        $name = $s["Name"];
        $id = $s["FipsCode"];
        $link = "statedetail.php?stateId=".$id;
        $sb->add("<li><a href='".$link."' target='blank'>".$name."</a><br/></li>");
        $i++;
        if($i>$half&&!($split)){
            $sb->add("</ul><ul class='stateList'>");
            $split = true;
        }
    }
    return $sb->getString();
}

function renderCoverageResultsTable($s1, $s2, $data){
    $sld = getStateListData('none', true);
    $s1name = $sld[$s1]['Name'];
    $s2name = $sld[$s2]['Name'];

    
    $sb = new StringBuilder();
    $sb->add('<table id="searchResults">');
    $header = <<<HDR
<thead>
<tr>
    <th>Coverage</th>
    <th>$s1name Medicaid</th>
    <th>$s1name SEHP</th>
    <th>$s2name Medicaid</th>
    <th>$s2name SEHP</th>
</tr>
</thead>
HDR;
    $sb->add($header);
    foreach($data as $cov => $data){
        if(!isValidRow($cov)) continue;
        $s1m = isset($data[$s1]['medicaid'])?$data[$s1]['medicaid']:'';
        $s1s = isset($data[$s1]['sehp'])?$data[$s1]['sehp']:'';
        $s2m = isset($data[$s2]['medicaid'])?$data[$s2]['medicaid']:'';
        $s2s = isset($data[$s2]['sehp'])?$data[$s2]['sehp']:'';

        $sb->add("<tr>");
        $sb->add("<td  class='stateComparisonHeader'>".$cov."</td>");
        $sb->add("<td class='cValue'>".getCoverageValueHTML($s1m, true)."</td>");
        $sb->add("<td class='cValue'>".getCoverageValueHTML($s1s, true)."</td>");
        $sb->add("<td class='cValue'>".getCoverageValueHTML($s2m, true)."</td>");
        $sb->add("<td class='cValue'>".getCoverageValueHTML($s2s, true)."</td>");
        $sb->add("</tr>");
    }

    $sb->add('</table>');
    $html = $sb->getString();
    return Array('s1name'=>$s1name, 's2name'=>$s2name, 'html'=>$html);
}

function renderSearchResultTable($states){
    $sb = new StringBuilder();
    $tableData = new StringBuilder();
    $split = false;
    $half = getArrSplitIndex($states);
    if($half < 7)$split = true;
    $i=1;

    foreach($states as $s){
        $name = $s["Name"];
        $id = $s["FipsCode"];
        $link = 'http://'.server.dir."statedetail.php?stateId=".$id;
        $linkedName = "<a href='".$link."'>".$name."</a>";
        $i++;

        $tableData->add("<tr><td class='stateHeader'>");
        $tableData->add($linkedName);
        $tableData->add("</td>");
        $tableData->add("</tr>");
    }
    
    $tableDataHTML = $tableData->getString();

    if($tableDataHTML != ''){

        $tableString = <<<TABLESTR
<table id='searchResults'>
<thead>
<tr>
<th>State</th>
</tr>
</thead>
TABLESTR;

        $tableString .= $tableDataHTML;

        $tableString .= '</table>';
        return $tableString; 
    }
    return '';

}

function getSearchFieldsHTML($data, $pdf = false){
    $searchParams = array();
    foreach($data as $key => $val){
        if($val != ''){
            $searchParams[$key] = $val;
        }
    }

	$sb = new StringBuilder();
    if($pdf == false){
    	$sb->add("<ul id='searchFields'>");
    }else{
        $sb->add("<table>");
    }
    foreach($searchParams as $field => $data){
        $dataArr = split('__', $data);
        $value = $dataArr[0];
        $desc = $dataArr[1];
        $fieldArr = split('__', $field);
        $cat = preg_replace('/_/', ' ', $fieldArr[0]);
        $fieldName = preg_replace('/_/', ' ',$fieldArr[1]);
        if($value != 'any' && $field != "searchType"){
            if($pdf == false){
    	        $sb->add("<li>".$fieldName." - ".$desc."</li>");
            }else{
                $sb->add("<tr><td>".$fieldName." - ".$desc."</td></tr>");
            }
	    }
    }
    if($pdf == false){
        $sb->add("</ul>");
    }else{
        $sb->add("</table>");
    }
	return $sb->getString();
}

function getSLATISearchHTML(){
    $html = '';
    $data = getSLATISearchFields();
    $sb = new StringBuilder();
    foreach($data['HasLawOrRestrictionFields'] as $cat => $field){
        $sb->add('<div><h3>'.$cat.'</h3>');
        foreach($field as $name => $values){
            $sb->add('<div class="searchFieldWrap">');
            $sb->add('<label for="'.$cat.'__'.$name.'">'.$name."</label>\n");
        	$sb->add('<select name="'.$cat.'__'.$name.'">'."\n");
		    $sb->add('<option value="">Select</option>'."\n");
            foreach($values as $key => $val){
                $sb->add('<option value="'.$key.'__'.$val.'">'.$val.'</option>'."\n");
            }
            $sb->add('</select>'."\n");
            $sb->add('</div>');
        }
        $sb->add('</div>');
    }
    $html = $sb->getString();
    return $html;
}

function getSearchFormFieldsHTML(){
    $searchFieldsHTML = <<<HTML
<h3>Clean Indoor Air</h3>
<label for="public">Public Places</label>
	<select name="public">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Government Buildings</label>
	<select name="government">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Private Workplaces</label>
	<select name="private-work">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Schools</label>
	<select name="schools">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Childcare Centers</label>
	<select name="childcare">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Health Facilities</label>
	<select name="health">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Restaurants</label>
	<select name="restaurants">
		<option value="">Select</option>
		<option value="4">None</option>
		<option value="1">Restriction Required</option>
		<option value="2">Smoking Prohibited</option>
		<option value="3">Enclosed, Separately Ventilated</option>
	</select>
<label>Penalties/Enforcement</label>
	<select name="cia-enforcement" id="cia-enforcement">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Tobacco Excise Tax</h3>
<label>Cigarettes</label>
	<select name="cigarettes">
		<option value="">Select</option>
		<option value="50+">Over 50 cents</option>
		<option value="1+">Over $1</option>
	</select>
<label>Revenue Collected</label>
	<select name="revenue">
		<option value="">Select</option>
		<option value="1,000,000-50,000,000">1,000,000-50,000,000</option>
		<option value="50,000,000-100,000,000">50,000,000-100,000,000</option>
		<option value="100,000,000-200,000,000">100,000,000-200,000,000</option>
		<option value="200,000,000-500,000,000">200,000,000-500,000,000</option>
		<option value="500,000,000-1,000,000,000">500,000,000-1,000,000,000</option>
		<option value="More than 1,000,000,000">More than 1,000,000,000</option>
	</select>
<h3>Youth Access</h3>
<label>Enforcement</label>
	<select name="enforcement">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Penalty</label>
	<select name="penalty">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Penalty to Minors</label>
	<select name="minors">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Placement</label>
	<select name="placement">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Samples</label>
	<select name="samples">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Single Cigarettes</label>
	<select name="single">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Vending Machines</label>
	<select name="vend">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Licensing</label>
	<select name="lisensing">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Bidis</label>
	<select name="bidis" id="bidis">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Sign Posting</label>
	<select name="ya-sign" id="ya-sign">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="None">None</option>
	</select>
<label>Other Provisions</label>
	<select name="ya-other" id="ya-other">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Smoker Protection Laws</h3>
    <select name="protection">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Advertising and Promotion</h3>
    <select name="add">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Product Disclosure</h3>
    <select name="disclosure">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Tobacco Divestment</h3>
    <select name="divestment">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Tobacco Liability</h3>
    <select name="liability">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<h3>Tobacco Settlement</h3> 
<label>Tobacco Control Appropriations</label>
    <select name="settlement">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Non-Monetary Provisions</label>
    <select name="ts-non-monetary" id="select">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
<label>Securitization</label>
    <select name="ts-security" id="select2">
		<option value="">Select</option>
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select>
HTML;
    return $searchFieldsHTML;
}

function getCoverageValueHTML($val, $pdf = false){
    $html = "";
    $val = trim(strtolower($val));
    $answer = '';
    $imgPath = 'imgs/';

    // validValues is set up to decode the values from the xml and select the correct image
    // img is the image filename within $imgPath defined above
    // values is an array of the valid lower-cased, trimmed values from the xml that this image should match
    // refer to the different values for varies to see why we do this
    $validAnswers = array(
        'yes'=> array(
            'img' => 'yes.jpg',
            'values' => array(
                'yes'
            )
        ),
        'no' => array(
            'img' => 'no.jpg' ,
            'values' => array(
                "no"
            )
        ),
        'varies' => array(
            'img' => 'varies.jpg',
             'values' => array(
                "*",
                "**",
                "star",
                "d",
                "p",
                "varies"
            )
        ),
        'na' => array(
            'img' => 'varies.jpg',
            'values' => array(
                "n/a",
                "not applicable"
            )
        ),
    );

    foreach($validAnswers as $key => $data){
        // if the val we have is in the valid values array for this answer
        if(in_array($val, $data['values'])){
            $answer = $key;
            $imgSrc = $data['img'];
            break;
        }
    }
    // short circuit for not applicable
    if($answer == 'na'){
        return ''; //killing this for now
        $naStr = "<span style='color:red;'>N/A</span>";
        if($pdf){
            $naStr = "<span style='color:red;'>N/A - </span>";
        }
        return $naStr;
    }
    if($answer != ''){
        $imgSrc = $imgPath.$imgSrc;
        if($pdf) $imgSrc = 'http://'.server.dir.$imgSrc;
        $html = "<img src='".$imgSrc."' alt='".$answer."'/>";
    }
    return $html;

}

function getIconLegend($pdf = false){
    $yes = getCoverageValueHTML('yes', $pdf);
    $varies = getCoverageValueHTML('varies', $pdf);
    $no = getCoverageValueHTML('no', $pdf);
    if($pdf){
        $html = <<<PDFHTML
<table width='65%'>
    <tr>
        <td class='first'>Legend:</td>
        <td>$yes = Covered</td>
        <td>$varies = Coverage Varies</td>
        <td>$no = Not Covered</td>
    </tr>
</table>
PDFHTML;
    }else{
        $html = <<<HTML
<ul id="legend">
    <li class="first">Icon Legend:</li>
    <li>$yes = Covered</li>
    <li>$varies = Coverage Varies</li>
    <li>$no = Not Covered</li>
</ul>
HTML;
    }
    return $html;
}
function getCoverageHTMLFromRawValue($str, $isPDF){
    $decoded = decodeCoverageValue($str);
    $html = getCoverageValueHTML($decoded, $isPDF);
    return $html;
}

function getSponsorText(){
    $data = getStateDataFromXML("01");
    $ary = array();
    if(isset($data["qryXMLStateAttribute"])){
        parseAttrsToHTML($data["qryXMLStateAttribute"], $ary);
    }
    $retStr = cleanStr($ary["Donor recognition"]);
    return $retStr;
}

function renderCrumbs($pgTitle = false, $parents = false){
    if($pgTitle != false){
        $pgTitle = '<span class="breadcrumbseparator">> </span>'.$pgTitle;
    }
    $siteRoot = 'http://'.server.dir;
    $retStr = <<<HTML
    <div id="breadcrumbs">
        <div id="breadcrumb-57863998" style="DISPLAY: inline">
            <span class="breadcrumbComponent">      
                <a class="breadcrumb" href="$siteRoot">Home</a>&nbsp;
HTML;
    if($parents){
        foreach($parents as $t => $l){
            $retStr .= '<span class="breadcrumbseparator">></span>&nbsp;<a class="breadcrumb" href="'.$l.'">'.$t.'</a>&nbsp;';
        }
    }

    $retStr .= $pgTitle;
    $retStr .= <<<HTML2
        </div>
    </div>
HTML2;
    return $retStr;
}

function renderCrumbsAndHeaderStart($crumbTitle, $title){
    $retStr = renderCrumbs($crumbTitle);
    $retStr .= <<<HTML
    <h2>{$title}</h2>
    <div id="content-well">
        <a href="http://www.lung.org">Home</a> &gt; <a href="http://www.lungusa.org/stop-smoking/">Stop Smoking</a> &gt; <a href="http://www.lungusa.org/stop-smoking/tobacco-control-advocacy/">Tobacco Control Advocacy</a> &gt; <a href="http://www.lungusa.org/stop-smoking/tobacco-control-advocacy/reports-resources/">Reports and Resources</a> &gt; <a href="http://lungusa2.org/cessation2/">State Tobacco Cessation Coverage</a> &gt; {$crumbTitle}
HTML;
    return $retStr;
}

function renderCrumbsAndHeaderEnd(){
    $retStr = <<<HTML
    </div> <!-- content-well -->
HTML;
    return $retStr;
}

function renderMap(){
    $retStr = <<<HTML
    <map id="Map" name="Map">
        <div><img alt="" src="http://www.lungusa2.org/slati/imgs/Map3.gif" usemap="#Map" border="0" /></div>
        <area shape="rect" coords="422,174,455,195" href="statedetail.php?stateId=11"><!-- dc -->
        <area shape="rect" coords="418,160,454,173" href="statedetail.php?stateId=24"><!-- md -->
        <area shape="rect" coords="418,144,456,160" href="statedetail.php?stateId=10"><!-- de -->
        <area shape="rect" coords="424,127,454,144" href="statedetail.php?stateId=34"><!-- nj -->
        <area shape="rect" coords="425,111,454,127" href="statedetail.php?stateId=09"><!-- ct -->
        <area shape="rect" coords="420,95,454,111" href="statedetail.php?stateId=44"><!-- ri -->
        <area shape="rect" coords="425,77,454,95" href="statedetail.php?stateId=25"><!-- ma -->
        <area shape="poly" coords="394,90,410,79,420,87,402,100" href="statedetail.php?stateId=33"><!-- nh -->
        <area shape="poly" coords="386,71,398,68,409,79,390,89" href="statedetail.php?stateId=50"><!-- vt -->
        <area shape="poly" coords="404,67,416,82,434,65,412,38" href="statedetail.php?stateId=23"><!-- me -->
        <area shape="poly" coords="353,117,382,112,396,121,391,102,384,81,362,90" href="statedetail.php?stateId=36"><!-- ny -->
        <area shape="poly" coords="352,137,385,134,388,124,381,114,349,121" href="statedetail.php?stateId=42"><!-- pa -->
        <area shape="poly" coords="338,160,347,142,366,146,355,158,342,170" href="statedetail.php?stateId=54"><!-- wv -->
        <area shape="poly" coords="316,145,326,129" href="#">
        <area shape="poly" coords="344,175,393,165,384,149,374,144" href="statedetail.php?stateId=51"><!-- va -->
        <area shape="poly" coords="298,124,326,120,329,100,307,85,298,97" href="statedetail.php?stateId=26"><!-- mi -->
        <area shape="poly" coords="320,154,334,155,347,139,345,120,330,128,317,131" href="statedetail.php?stateId=39"><!-- oh -->

        <area shape="poly" coords="297,167,316,155,309,129,297,133" href="statedetail.php?stateId=18"><!-- in -->
        <area shape="poly" coords="295,178,298,172,319,159,337,168,328,179" href="statedetail.php?stateId=21"><!-- ky -->
        <area shape="poly" coords="368,195" href="#">
        <area shape="poly" coords="334,193,349,177,384,171,397,179,381,188" href="statedetail.php?stateId=37"><!-- nc -->
        <area shape="poly" coords="311,182,325,166" href="#">
        <area shape="poly" coords="281,198,291,185,338,179,324,195" href="statedetail.php?stateId=47"><!-- tn -->
        <area shape="poly" coords="337,195,349,191,362,192,374,201,359,216" href="statedetail.php?stateId=45"><!-- sc -->
        <area shape="poly" coords="311,245,361,242,391,282,373,296" href="statedetail.php?stateId=12"><!-- fl -->
        <area shape="poly" coords="330,238,351,238,357,220,332,198,318,201" href="statedetail.php?stateId=13"><!-- ga -->
        <area shape="poly" coords="298,203,312,202,323,240,296,241" href="statedetail.php?stateId=01"><!-- al -->
        <area shape="poly" coords="273,244,294,249,292,204,282,205,272,218" href="statedetail.php?stateId=28"><!-- ms -->
        <area shape="poly" coords="277,127,291,130,294,156,287,179,273,161,268,146" href="statedetail.php?stateId=17"><!-- il -->
        <area shape="poly" coords="264,81,289,99,289,122,270,120,257,94" href="statedetail.php?stateId=55"><!-- wi -->
        <area shape="poly" coords="249,227,265,227,265,244,277,247,287,258,265,265,250,259" href="statedetail.php?stateId=22"><!-- la -->
        <area shape="poly" coords="246,188,275,189,267,222,247,222" href="statedetail.php?stateId=05"><!-- ar -->
        <area shape="poly" coords="239,147,261,147,280,184,240,185" href="statedetail.php?stateId=29"><!-- mo -->
        <area shape="poly" coords="230,114,259,115,268,127,261,141,234,141,230,127" href="statedetail.php?stateId=19"><!-- ia -->

        <area shape="poly" coords="223,60,261,66,250,84,247,98,256,111,226,113" href="statedetail.php?stateId=27"><!-- mn -->
        <area shape="poly" coords="143,232,170,235,178,206,190,207,209,217,232,217,240,232,243,255,219,295,178,273,148,255" href="statedetail.php?stateId=48"><!-- tx -->
        <area shape="poly" coords="183,185,182,189,198,190,200,208,233,215,241,208,236,193,236,185" href="statedetail.php?stateId=40"><!-- ok -->
        <area shape="poly" coords="190,153,233,154,238,180,187,178" href="statedetail.php?stateId=20"><!-- ks -->
        <area shape="poly" coords="181,123,221,126,233,147,190,147,180,134" href="statedetail.php?stateId=31"><!-- ne -->
        <area shape="poly" coords="175,92,221,94,223,123,205,120,169,117" href="statedetail.php?stateId=46"><!-- sd -->
        <area shape="poly" coords="177,58,217,58,220,89,175,87" href="statedetail.php?stateId=38"><!-- nd -->
        <area shape="poly" coords="123,181,167,184,167,229,118,230" href="statedetail.php?stateId=35"><!-- nm -->
        <area shape="poly" coords="127,139,179,142,177,178,121,174" href="statedetail.php?stateId=08"><!-- co -->
        <area shape="poly" coords="115,98,166,101,163,136,113,133" href="statedetail.php?stateId=56"><!-- wy -->
        <area shape="poly" coords="87,333,126,265,182,288,186,322,222,341,233,355,200,364,113,349" href="statedetail.php?stateId=02"><!-- ak -->
        <area shape="poly" coords="13,237,18,269,70,296,96,270,56,243" href="statedetail.php?stateId=15"><!-- hi -->
        <area shape="poly" coords="98,88,159,95,165,59,89,51" href="statedetail.php?stateId=30"><!-- mt -->
        <area shape="poly" coords="106,123,68,115,82,52" href="statedetail.php?stateId=16"><!-- id -->
        <area shape="poly" coords="83,169,117,175,122,140,109,139,108,130,93,127" href="statedetail.php?stateId=49"><!-- ut -->
        <area shape="poly" coords="71,216,109,237,115,182,80,175" href="statedetail.php?stateId=04"><!-- az -->
        <area shape="poly" coords="72,178,84,127,49,117,42,143" href="statedetail.php?stateId=32"><!-- nv -->

        <area shape="poly" coords="16,110,41,114,36,141,69,197,62,209,39,209,20,181,9,122" href="statedetail.php?stateId=06"><!-- ca -->
        <area shape="poly" coords="69,79,31,70,16,98,60,109" href="statedetail.php?stateId=41"><!-- or -->
        <area shape="poly" coords="71,73,78,45,46,36,32,39,32,57,43,68" href="statedetail.php?stateId=53"><!-- wa -->

    </map>
HTML;
    return $retStr;
}

?>
