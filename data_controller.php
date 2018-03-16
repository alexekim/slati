<?php
include_once 'util/xml_parse.php';
include_once 'util/general_utils.php';
include_once 'util/stringBuilder.php';
include_once 'view_controller.php';

// Set Globals
define('fHead', "xml/");
define('fTail', "data.xml");

if($_SERVER['HTTP_HOST']== 'lungslati.beaconfire.us'){
    define('dir', '');
}else{
    define('dir', 'slati/');
}
define('server', $_SERVER['SERVER_NAME'].'/');

function getStateDataFromXML($sID){
    $fURL = fHead.$sID.fTail;
    $sData = xml_parse_into_assoc($fURL);
    return $sData["dataroot"]["tblStatesALA"];
}
function getLastUpdate(){
	$fURL = fHead."01".fTail;
	$date = xml_parse_generated_date($fURL);
	return date("F d Y", $date);
}

function isValidRow($val){
    $isValid = !(in_array($val, getCoveragesToSkip()));
    return $isValid;
}
function parseToByCategory($data, $byDisplayOrder = false){
    $toReturn = Array();
    $tempArr = Array();
    foreach($data as $row){
        $key1 = "Category";
        $key2 = "Subcategory";
        if($byDisplayOrder){
            $key1 .= "DisplayOrder";
            $key2 .= "DisplayOrder";
        }
        if(!array_key_exists($row[$key1], $tempArr)){
            $tempArr[$row[$key1]] = Array();
        }
        $tempArr[$row[$key1]][$row[$key2]] = $row;
    }
    if($byDisplayOrder){
        foreach($toReturn as $key => $cat){
            ksort($cat);
            $tempArr[$key] = array_values($cat);
        }
        ksort($tempArr);
        $toReturn = array_values($tempArr);
    }else{
        $toReturn = $tempArr;
    }
    return $toReturn;
}
function getStateListData($filterIds = "none", $byFIPS = false){
	$idXMLFile = "xml/states.xml";
    $states = xml_parse_into_assoc($idXMLFile);
    $states = $states["dataroot"]["tblStatesALA"];
    $filteredStates = array();
    foreach($states as $s){
        $id = $s["FipsCode"];
        if($id != "43"){ // always remove Puerto Rico
        	if(($filterIds == "none") || in_array($id, $filterIds)){
                //if there is no filter or there is, but the state is included 
                if($byFIPS){//hash by FipsCode
                    $filteredStates[$id] = $s;
                }else{
                    array_push($filteredStates, $s);
                }
        	}
        }
    }
    return $filteredStates;
}

function getFullStateList(){
    $states = getStateListData();
    return renderStateList($states);
}

function getStandardizedStateName($name){
    $retName = strToLower($name);
    $retName = preg_replace('/ /', '', $retName);
    return $retName;
}

function getStateComparison($data){
    $s1 = $data['state1'];
    $s2 = $data['state2'];
    $coverageXMLFiles = getCoverageXMLFileArr('both');
	foreach($coverageXMLFiles as $file => $props){
        $list = xml_parse_into_assoc($file);
        $tbl = $props['table'];
    	$coverageListArr[$props['title']] = $list["dataroot"][$tbl];
    }
    
    $coverages = Array();
    foreach($coverageListArr as $cLTitle => $coverageList){
        foreach($coverageList as $c){
            $type = '';
            if($cLTitle == 'Medicaid Barrier' || $cLTitle == 'Medicaid Coverage'){
                    $type = "medicaid";
            }else{
                    $type = "sehp";
            }
            $cov = $c["Coverage"];
            if(!isset($coverages[$cov])){
                $coverages[$cov] = Array();
            }
            
            $val = "";
    		if(isset($c["CoverageValue"])){
                $val = strtolower($c["CoverageValue"]);
            }
            if(isset($c['DisplayOnWebInd'])){
                if($c['DisplayOnWebInd'] == 0){
                    continue;
                }
            }
            if($c['FipsCode'] == $s1){
                $coverages[$cov][$s1][$type] = $val;
            }elseif($c['FipsCode'] == $s2){
                $coverages[$cov][$s2][$type] = $val;
            }
	    }
    }
    return renderCoverageResultsTable($s1, $s2, $coverages);    
}

function getTextBlock($id, $linkifyStates = true){
    ob_start();
    include 'text_blocks/'.$id.'.php';
    $text = ob_get_contents();
    ob_end_clean();
    $text = fixRelativeLinks($text);
    if($linkifyStates){
        $text = linkStates($text);
    }
    return $text;
}

function fixRelativeLinks($str){
    $retStr = $str;
    $retStr = preg_replace('/<a href="\//','<a href="http://'.server.dir, $retStr);
    $retStr = preg_replace('/src="\//','src="http://'.server.dir, $retStr);
    return $retStr;
}

function linkStates($text){
    $states = getStateListData();
    $finds = Array();
    $reps = Array();
    $urlStart = "http://".server.dir.'statedetail.php?stateId=';

    foreach($states as $state){
        $finds[] = " ".$state['Name'];
        $reps[] = ' <a href="'.$urlStart.$state['FipsCode'].'">'.$state['Name'].'</a>';
    }
    return str_replace($finds, $reps, $text);
}


function getSLATISearchData(){
    $sData = xml_parse_into_assoc('xml/SLATIDetailSearch.xml');
    $data = $sData['dataroot']['qryXMLSLATIDetailSearch'];
    return $data;
}

function getSLATISearchFields($searchResults = false){
    $data = getSLATISearchData();
    $fields = array();
    $revenueFieldsAdded = false;
    $rateFieldsAdded = false;
    foreach($data as $row => $rdata){
        $cat = $rdata['Category'];
        $subcat = $rdata['Subcategory'];

        if($cat == 'Tobacco Taxes' && $subcat == 'Use of Cigarette Tax Revenue - Summary'){
            $subcat2 = $rdata['NumericValueLabel'];
            if($searchResults){
                $lbl = '';
                $amt = $rdata['NumericValue'];
                        if($amt >= 1000000000){ $lbl = '1to50';
                }elseif($amt >=  500000000){ $lbl = '50to100';
                }elseif($amt >=  200000000){ $lbl = '100to200';
                }elseif($amt >=  100000000){ $lbl = '200to500';
                }elseif($amt >=   50000000){ $lbl = '500to1b';
                }elseif($amt >=    1000000){ $lbl = '1bandup';
                }
                if(!array_key_exists($subcat2, $fields['HasLawOrRestrictionFields'][$cat])){
                    $fields['HasLawOrRestrictionFields'][$cat][$subcat2] = array();
                }
                if(!array_key_exists($lbl, $fields['HasLawOrRestrictionFields'][$cat][$subcat2])){
                    $fields['HasLawOrRestrictionFields'][$cat][$subcat2][$lbl] = array();
                }
                $fields['HasLawOrRestrictionFields'][$cat][$subcat2][$lbl][] = $rdata['FipsCode'];
            }elseif(!$revenueFieldsAdded){
                $fields['HasLawOrRestrictionFields'][$cat][$subcat2] = array(
                    '1to50' => '1,000,000-50,000,000',
                    '50to100' => '50,000,000-100,000,000',
                    '100to200' => '100,000,000-200,000,000',
                    '200to500' => '200,000,000-500,000,000',
                    '500to1b' => '500,000,000-1,000,000,000',
                    '1bandup' => 'More than 1,000,000,000'
                );
                $revenueFieldsAdded = true;
            }
        }else if($cat == 'Tobacco Taxes' && $subcat == 'Tax on Cigarettes'){
            $subcat2 = $rdata['NumericValueLabel'];
            if($searchResults){
                $lbl = '';
                $amt = $rdata['NumericValue'];
                        if($amt >= 2.5 ){ $lbl = '250andup';
                }elseif($amt >=    2   ){ $lbl = '200to250';
                }elseif($amt >=    1.5 ){ $lbl = '150to200';
                }elseif($amt >=    1   ){ $lbl = '100to150';
                }elseif($amt >=     .50){ $lbl = '50to100';
                }elseif($amt >=    0   ){ $lbl = 'under50';
                }
                if(!array_key_exists($subcat2, $fields['HasLawOrRestrictionFields'][$cat])){
                    $fields['HasLawOrRestrictionFields'][$cat][$subcat2] = array();
                }
                if(!array_key_exists($lbl, $fields['HasLawOrRestrictionFields'][$cat][$subcat2])){
                    $fields['HasLawOrRestrictionFields'][$cat][$subcat2][$lbl] = array();
                }
                $fields['HasLawOrRestrictionFields'][$cat][$subcat2][$lbl][] = $rdata['FipsCode'];
            }elseif(!$rateFieldsAdded){
                $fields['HasLawOrRestrictionFields'][$cat][$subcat2] = array(
                    'under50' => 'Under 50 cents',
                    '50to100' => '50 cents to 99.9 cents',
                    '100to150' => '$1.00 to $1.499',
                    '150to200' => '$1.50 to $1.999',
                    '200to250' => '$2.00 to $2.499',
                    '250andup' => '$2.50 and higher'
                );
                $rateFieldsAdded = true;
            }
        }

        if(array_key_exists('HasLawOrRestriction', $rdata)){
            if(!array_key_exists('HasLawOrRestrictionFields', $fields)){
                $fields['HasLawOrRestrictionFields'] = array();
            }
            if(!array_key_exists($cat, $fields['HasLawOrRestrictionFields'])){
                $fields['HasLawOrRestrictionFields'][$cat] = array();
            }
            if(!array_key_exists($subcat, $fields['HasLawOrRestrictionFields'][$cat])){
                $fields['HasLawOrRestrictionFields'][$cat][$subcat] = array();
            }
            if(!$searchResults){
                $fields['HasLawOrRestrictionFields'][$cat][$subcat][$rdata['HasLawOrRestriction']] = $rdata['HasLawOrRestrictionDescription'];
            }else{
                if(!array_key_exists($rdata['HasLawOrRestriction'], $fields['HasLawOrRestrictionFields'][$cat][$subcat])){
                    $fields['HasLawOrRestrictionFields'][$cat][$subcat][$rdata['HasLawOrRestriction']] = array();
                }
                $fields['HasLawOrRestrictionFields'][$cat][$subcat][$rdata['HasLawOrRestriction']][] = $rdata['FipsCode'];
            }
        }
    }
    return $fields;
}

function getFilteredStateList($data){
    $searchParams = array();
    foreach($data as $key => $data){
        $dataArr = split('__', $data);
        $val = $dataArr[0];
        if($val != ''){
            $searchParams[$key] = $val;
        }
    }
    if(count($searchParams) == 0){
        $states = getStateListData();
    }else{
        //$fields = getSLATISearchData();
        $matchesByFIPSCode = array();
        $searchData = getSLATISearchFields(true);
        $firstField = true;
        $validStates = array();
        foreach($searchParams as $field => $val){

            $fieldArr = split('__', $field);
            $cat = preg_replace('/_/', ' ', $fieldArr[0]);
            $subcat = preg_replace('/_/', ' ',$fieldArr[1]);

            if($firstField){
                $validStates = $searchData['HasLawOrRestrictionFields'][$cat][$subcat][$val];
                $firstField = false;
            }else{
                $tmpStates = $validStates;
                $validStates = array();
                $fieldFilter = $searchData['HasLawOrRestrictionFields'][$cat][$subcat][$val];
                foreach($tmpStates as $id){
                    if(in_array($id, $fieldFilter)){
                        $validStates[] = $id;
                    }
                }
            }
        }
        //$arr = $searchData['HasLawOrRestrictionFields'][$cat][$subcat][$val];

        $states = getStateListData($validStates);
    }
    return renderSearchResultTable($states);
}

function getAppendixBData(){
    $states = getStateListData();
    $appendixData = array();
    $first = true;
    $headers = array();
    $key = array();
    foreach($states as $state){
        $stateData = getStateDataFromXML($state['FipsCode']);
        $stateData = parseToByCategory($stateData['qryXMLSLATIDetail']);
        $restrictions = $stateData['Smoking Restrictions'];
        $suppressed = getAppendixSuppressedFields();
        foreach($restrictions as $r){
            $subCat = $r['Subcategory'];
            if(!in_array($subCat, $suppressed)){
                $name = $r["Name"];
                if(!array_key_exists($subCat, $headers)){
                    $headers[$subCat] = $r["SubcategoryDisplayOrder"];
                }
                if(!array_key_exists($name, $appendixData)){
                    $appendixData[$name] = array();
                }
                if(array_key_exists('HasLawOrRestriction', $r)){
                    $appendixData[$name][$subCat] = $r['HasLawOrRestriction'];
                    if(!array_key_exists($r['HasLawOrRestriction'], $key)){
                        $key[$r['HasLawOrRestriction']] = $r['HasLawOrRestrictionDescription'];
                    }
                }
            }
        }
    }
    $headers = array_flip($headers);
    ksort($headers);
    $headers = array_values($headers);
    $appendix = array();
    $appendix['headerRow'] = $headers;
    $appendix['data'] = $appendixData;
    $appendix['key'] = $key;
    return $appendix;
}

function getAppendixCData(){
    $states = getStateListData();
    $appendixData = array('t1data' => array(), 't2data' => array());
    $first = true;
    $headers = array();
    foreach($states as $state){
        $name = $state['Name'];
        $stateData = getStateDataFromXML($state['FipsCode']);
        $stateData = parseToByCategory($stateData['qryXMLSLATIDetail']);
        $taxRate = $stateData['Tobacco Taxes']['Tax on Cigarettes']['NumericValue'];
        $taxRate3d = number_format(floatval($taxRate), 3);
        $taxRate2d = number_format(floatval($taxRate), 2);
        $taxRate = ($taxRate3d == $taxRate2d) ? $taxRate2d : $taxRate3d;
        $dataArr = array('State' => $name, 'Tax Rate' => $taxRate); 
        $appendixData['t1data'][$name] = $dataArr;
        $appendixData['t2data'][$taxRate.$name] = $dataArr;
    }
    ksort($appendixData['t1data']);
    $appendixData['t1data'] = array_values($appendixData['t1data']);
    ksort($appendixData['t2data']);
    $appendixData['t2data'] = array_values($appendixData['t2data']);
    $headers = array('State', 'Tax Rate');
    $appendixData['t1HeaderRow'] = $headers;
    $appendixData['t2HeaderRow'] = $headers;
    return $appendixData;
}

function getAppendixDData(){
    $states = getStateListData();
    $appendixData = array('data' => array());
    $headers = array();
    foreach($states as $state){
        $name = $state['Name'];
        $stateData = getStateDataFromXML($state['FipsCode']);
        $stateData = parseToByCategory($stateData['qryXMLSLATIDetail']);
        if(array_key_exists('Use of Cigarette Tax Revenue - Summary', $stateData['Tobacco Taxes'])){
            $revCollectedRaw = $stateData['Tobacco Taxes']['Use of Cigarette Tax Revenue - Summary']['NumericValue'];
            $revCollected = number_format(floatval($revCollectedRaw), 0);
        }else{
            $revCollectedRaw = 0;
            $revCollected = '<b>Missing from XML</b>';
        }
        $revCollectedKey = (100000000000+$revCollectedRaw).$name;
        $dataArr = array('State' => $name, 'Revenue Collected in FY2012' => '$'.$revCollected);
        $appendixData['t1data'][$name] = $dataArr;
        $appendixData['t2data'][$revCollectedKey] = $dataArr;
    }
    ksort($appendixData['t1data']);
    $appendixData['t1data'] = array_values($appendixData['t1data']);
    ksort($appendixData['t2data']);
    $appendixData['t2data'] = array_values($appendixData['t2data']);
    $appendixData['t2data'] = array_reverse($appendixData['t2data']);
    $headers = array('State', 'Revenue Collected in FY2012');
    $appendixData['t1HeaderRow'] = $headers;
    $appendixData['t2HeaderRow'] = $headers;
    return $appendixData;
}

function getAppendixEData(){
    $states = getStateListData();
    //$appendixData = array('data' => array()); 
	$appendixData = array();
    $headers = array();
	
    foreach($states as $state){
        $name = $state['Name'];
        $stateData = getStateDataFromXML($state['FipsCode']);
        $stateData = parseToByCategory($stateData['qryXMLSLATIDetail']);
        $issueAreas = array();
        $description = "";
	
        // Clean Indoor AirYes in others
        // or yes, has a law policy or restriction” under:
        // a) the State Preemption of Local Youth Access Laws subcategory under the Laws Restricting Youth Access to Tobacco Products category; or
        // b) the State Preemption of Local Youth Access Laws subcategory under the Tobacco Product Samples/Minimum Sales Amounts for Tobacco Products category; or
        // c) the State Preemption of Local Vending Machines Laws subcategory in the Sales of Tobacco Products from Vending Machines category;
        // or yes, has a law policy or restriction” in the Advertising Preemption subcategory under the Advertising & Promotion category.

            $catSubcatPairs = array(
                array(
                    'cat' => 'Smoking Restrictions',
                    'subcat' => 'Stronger Local Laws on Smoking',
                    'issueArea' => 'Smoking Restrictions',
                    'matchValue' => 'N'
                ),
                array(
                    'cat' => 'Laws Restricting Youth Access to Tobacco Products',
                    'subcat' => 'State Preemption of Local Youth Access Laws',
                    'issueArea' => 'Youth Access',
                    'matchValue' => 'Y'
                ),
                array(
                    'cat' => 'Tobacco Product Samples/Minimum Sales Amounts for Tobacco Products',
                    'subcat' => 'State Preemption of Local Samples Laws',
                    'issueArea' => 'Youth Access',
                    'matchValue' => 'Y'
                ),
                array(
                    'cat' => 'Advertising & Promotion',
                    'subcat' => 'Advertising Preemption',
                    'issueArea' => 'Youth Access',
                    'matchValue' => 'Y'
                ),
                array(
                    'cat' => 'Sales of Tobacco Products from Vending Machines',
                    'subcat' => 'State Preemption of Local Vending Machine Laws',
                    'issueArea' => 'Youth Access',
                    'matchValue' => 'Y'
                ),
                array(
                    'cat' => 'Licensing Requirements for Tobacco Products',
                    'subcat' => 'State Preemption of Local Licensing Laws',
                    'issueArea' => 'Youth Access',
                    'matchValue' => 'Y'
                )
            );
            foreach($catSubcatPairs as $key => $data){
                if(array_key_exists($data['cat'], $stateData)){
                    $catData = $stateData[$data['cat']];
                    if(array_key_exists($data['subcat'], $catData)){
                        $subcatData = $catData[$data['subcat']];
                        if($subcatData['HasLawOrRestriction'] == $data['matchValue']){
                            $issueAreas[] = $data['issueArea'];
                        }
                    }else{
                        // no subcat found
                    }
                }else{
                    // no cat
                }
            }
            if($issueAreas){
                if(!array_key_exists($name, $appendixData)){
                    $appendixData[$name] = array();
                }
                $issueAreas = array_unique($issueAreas);
                $appendixData[$name]['Issue Area Where Preemption Exists'] = implode('; ', $issueAreas);
            }
            if(array_key_exists('Preemption', $stateData)){
               $description = $stateData['Preemption']['Summary of all Preemptive Tobacco Control Laws']['Description'];
            }
            if($description){
                if(!array_key_exists($name, $appendixData)){
                    $appendixData[$name] = array();
                }
                $appendixData[$name]['Specific Provisions Preempted'] = $description;
            }
        }

    $appendix = array();
    $appendix['data'] = $appendixData;
    $appendix['headerRow'] = array('Issue Area Where Preemption Exists', 'Specific Provisions Preempted');
    return $appendix;
}

function getCatSubcatData($stateData, $cat, $subCat){
    if(array_key_exists($cat, $stateData)){
        if(array_key_exists($subCat, $stateData[$cat])){
            var_dump($stateData[$cat][$subCat]);
        }else{
            print('subcat not found: '.$cat.' : '.$subCat);
        }
    }else{
        print('cat not found: '.$cat);
    }
}

function getAppendixFData(){
    $states = getStateListData();
    $appendixData = array('data' => array());
    $headers = array();
    $cleanedCode = '';
    foreach($states as $state){
		$name = $state['Name'];
        $stateData = getStateDataFromXML($state['FipsCode']);
        $stateData = parseToByCategory($stateData['qryXMLSLATIDetail']);
		$protectionLaws = $stateData['Smoking Protection Laws'];
		foreach($protectionLaws as $law){
			if($law['HasLawOrRestriction']=='Y'){
                $code = $law['Citation'];
				$year = $law['Year'];
                $appendixData['data'][$name]['Year'] = $year;
                $cleanedCode = cleanStr($code);
				$appendixData['data'][$name]['Code'] = $cleanedCode;
			}
		}
    }
    ksort($appendixData['data']);
    $appendixData['headerRow'] = array('Year', 'Code');
    return $appendixData;
}
function getSLATIOverviewData(){
    $fURL = fHead."SLATIOverview.xml";
    $sData = xml_parse_into_assoc($fURL);
    $arr = $sData['dataroot']['qryXMLSLATIOverview'];
    $sb = new StringBuilder();
    foreach($arr as $row){
        $sb->add("<h2>".$row['Letter']." ".$row['IssueName']."</h2><p>".$row['IssueDescription']."</p>");
    }
    $str = $sb->getString();
    return cleanStr($str);
}

function getAppendixSuppressedFields(){
    $suppressed = array(
        'Overall Summary of Smoking Restrictions',
        'Exceptions to the Law',
        'Stronger Local Laws on Smoking',
        'Penalties/Enforcement',
        'Other State Smoking Restrictions and Provisions',
        'Private Vehicles'
    );
    return $suppressed;
}

function getSearchFields(){
    $searchFields = array(
            "nrtGum" => "NRT Gum",
            "nrtPatch" => "NRT Patch",
            "nrtNasal" => "NRT Nasal Spray",
            "nrtLoz" => "NRT Lozenge",
            "nrtInhaler" => "NRT Inhaler",
            "veren" => "Varenicline (Chantix)",
            "bupro" => "Bupropion (Zyban)",
            "groupCounsel" => "Group Counseling",
            "individualCounsel" => "Individual Counseling",
            "phoneCounsel" => "Phone Counseling",
            "onlineCounsel" => "Online Counseling",
            "durationLimit" => "Limits on duration",
            "lifetimeQuitLimit" => "Lifetime limit on quit attempts",
            "annualQuitLimit" => "Annual limit on quit attempts",
            "priorAuth" => "Prior authorization required",
            "copay" => "Co-payments required",
            "steppedcare" => "Stepped-care therepy",
            "counselReq" => "Counseling Required for Medications",
            "dollarLimit" => "Dollar Limit"
        );
    return $searchFields;
}
function getDataTablesArr(){
	$dataTables = array(
        array(	"name"=>"medicaidCoverage",
				"tableName"=>"qryXMLMedicaidCoverage",
				"type"=>"medicaid",
				"colType"=>"coverage",
				"printIfBlank"=>false
				),
		array(  "name"=>"medicaidBarrier",
				"tableName"=>"qryXMLMedicaidBarrier",
				"type"=>"medicaid",
				"colType"=>"barrier",
				"printIfBlank"=>false
				),
		array(  "name"=>"medicaidAddlSources",
				"tableName"=>"qryXMLAddlSourceMedicaid",
				"type"=>"medicaid",
				"colType"=>"addlSrc",
				"printIfBlank"=>true
				),
        array(  "name"=>"healthplanCoverage",
				"tableName"=>"qryXMLSEHPCoverage",
				"type"=>"sehp",
				"colType"=>"coverage",
				"printIfBlank"=>false
				),
        array(  "name"=>"healthplanBarrier",
				"tableName"=>"qryXMLSEHPBarrier",
				"type"=>"sehp",
				"colType"=>"barrier",
				"printIfBlank"=>false
				),
        array(  "name"=>"healthplanAddlSources",
				"tableName"=>"qryXMLAddlSourceHealthPlan",
				"type"=>"sehp",
				"colType"=>"addlSrc",
				"printIfBlank"=>true
				),
        array(  "name"=>"picCategory",
				"tableName"=>"qryXMLPrivateInsuranceCategory",
				"type"=>"pic",
				"colType"=>"picCols",
				"printIfBlank"=>false
				),
        array(  "name"=>"picCoverage",
				"tableName"=>"qryXMLPrivateInsuranceCoverage",
				"type"=>"pic",
				"colType"=>"picCols",
				"printIfBlank"=>false
				),
        array(  "name"=>"picAddlSources",
				"tableName"=>"qryXMLAddlSourcePrivateInsurance",
				"type"=>"pic",
				"colType"=>"addlSrc",
				"printIfBlank"=>true
                ),
        array(  "name"=>"qlCoverage",
				"tableName"=>"qryXMLQuitlineCoverage",
				"type"=>"ql",
				"colType"=>"coverageUnsourced",
				"printIfBlank"=>true
                )
    );
    return $dataTables;
}
function getTableCols($tName){
	$dataArr = array("Coverage", "CoverageValue", "SourceNotes", "WebURL");
	$altDataArr = array("SourceNotes", "WebURL");
	$cols = null;
	switch($tName):
		case "coverage":
			$cols = array(
				"data"=>$dataArr,
				"display"=> array(
					"Type of Treatment"=>"Coverage",
					"State Covers Treatment?"=>"CoverageValue",
					"Data Source"=>"dataSource"
				)
    		);
            break;
        case "coverageUnsourced":
			$cols = array(
				"data"=>$dataArr,
				"display"=> array(
					"Type of Treatment"=>"Coverage",
					"State Covers Treatment?"=>"CoverageValue"
				)
    		);
    		break;
    	case "barrier":
    		$cols = array(
    			"data"=>$dataArr,
    			"display"=> array(
    				"Type of Barrier"=>"Coverage",
					"State Has Barrier?"=>"CoverageValue",
					"Data Source"=>"dataSource"
				)
			);
        	break;
        case "picCols":
        	$cols = array(
        		"data"=>$dataArr,
        		"display"=> array(
        			"Type"=>"Coverage",
					"Value"=>"CoverageValue",
					"Data Source"=>"dataSource"
				)
        	);
        	break;
        case "addlSrc":
        	$cols = array(
        		"data"=>$altDataArr,
        		"display"=> array(
        			"Additional Data Sources"=>"dataSource"
        		)
        	);
        	break;
   	endswitch;
   	return $cols;
}

function getCoverageXMLFileArr($type){
    $coverageXMLFiles = Array();
    if($type=="both"){
    	$coverageXMLFiles = array(
            "xml/medicaidcoverage.xml" => array(
                'title'=> 'Medicaid Coverage',
                'table'=> "qryXMLSearchMedicaidCoverage"
            ),
            "xml/medicaidbarrier.xml" => array(
                'title'=>'Medicaid Barrier',
                'table'=>"qryXMLSearchMedicaidBarrier"
            ),
            "xml/healthplancoverage.xml" => array(
                'title'=>'SEHP Coverage',
                'table'=>"qryXMLSearchHealthplanCoverage"
            ),
            "xml/healthplanbarrier.xml" => array(
                'title'=>'SEHP Barrier',
                'table'=>"qryXMLSearchHealthplanBarrier"
            )
			);
    }elseif($type=="medicaid"){
        $coverageXMLFiles = array(
            "xml/medicaidcoverage.xml" => array(
                'title'=> 'Medicaid Coverage',
                'table'=> "qryXMLSearchMedicaidCoverage"
            ),
            "xml/medicaidbarrier.xml" => array(
                'title'=>'Medicaid Barrier',
                'table'=>"qryXMLSearchMedicaidBarrier"
            )
        );
    }else{
    	$coverageXMLFiles = array(
            "xml/healthplancoverage.xml" => array(
                'title'=>'SEHP Coverage',
                'table'=>"qryXMLSearchHealthplanCoverage"
            ),
            "xml/healthplanbarrier.xml" => array(
                'title'=>'SEHP Barrier',
                'table'=>"qryXMLSearchHealthplanBarrier"
            )
        );
    }
    return $coverageXMLFiles;
}

function getOverviewListArr($type){
	$arr = null;
	switch($type):
		case "medicaid":
		    $arr = array(
	        	"NRT Gum",
	        	"NRT Patch",
	        	"NRT Nasal Spray",
	        	"NRT Inhaler",
	        	"NRT Lozenge",
	        	"Varenicline (Chantix)",
	        	"Bupropion (Zyban)",
	        	"Group Counseling",
 	       		"Individual Counseling"
 	       		);
 	       break;
 	   case "sehp":
 	  		$arr = array(
        		"NRT Gum",
        		"NRT Patch",
        		"NRT Nasal Spray",
        		"NRT Inhaler",
		        "NRT Lozenge",
		        "Varenicline (Chantix)",
		        "Bupropion (Zyban)",
		        "Group Counseling",
		        "Individual Counseling",
		        "Phone Counseling"
		        );
            break;
        case "ql":
 	  		$arr = array(
        		"NRT Gum",
        		"NRT Patch",
        		"NRT Nasal Spray",
        		"NRT Inhaler",
		        "NRT Lozenge",
		        "Varenicline (Chantix)",
		        "Bupropion (Zyban)",
		        "Group Counseling",
		        "Individual Counseling",
		        "Phone Counseling"
		        );
 	   	   break;
    endswitch;
    return $arr;
}

function getCoveragesToSkip(){
    $retArray = Array(
        "MCO's Control Meds Coverage?",
        "Designated Tobacco Cessation Program",
        'Fee-for-service plan',
        'Managed care'
        );
    return $retArray;
}

function decodeCoverageValue($str){
	$str = strtolower($str);
	switch($str):
		case ("yes"):
			$decoded = "Yes";
		break;
		case ("no"):
			$decoded = "No";
		break;
		case ("d"):
			$decoded = "Discounts Available";
		break;
		case ("p"):
			$decoded = "Pregnant Women Only";
		break;
		case("*"):
		case("**"):
		case("star"):
		case("d"):
		case("p"):
			$decoded = "Varies";
        break;
        case("n/a"):
        case("N/A"):
        case("Not Applicable"):
            $decoded = "Not Applicable";
        break;
		default:
			$decoded = $str;
		break;
	endswitch;
	return $decoded;
}

?>
