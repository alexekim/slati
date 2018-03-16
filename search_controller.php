<?php

function getSLATISearchData(){
    $sData = xml_parse_into_assoc('xml/SLATIDetailSearch.xml');
    $data = $sData['dataroot']['qryXMLSLATIDetailSearch'];
    return $data;
}

function getSLATISearchFields($searchResults = false){
    $data = getSLATISearchData();
    $fields = array();
    foreach($data as $row => $rdata){
        $cat = $rdata['Category'];
        $subcat = $rdata['Subcategory'];
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

function getSLATISearchHTML(){
    $html = '';
    $data = getSLATISearchFields();
    $sb = new StringBuilder();
    foreach($data['HasLawOrRestrictionFields'] as $cat => $field){
        $sb->add('<div style="float:left;overflow:hidden;"><h3>'.$cat.'</h3>');
        foreach($field as $name => $values){
            $sb->add('<label>'.$name."</label>\n");
        	$sb->add('<select name="'.$cat.'__'.$name.'">'."\n");
		    $sb->add('<option value="">Select</option>'."\n");
            foreach($values as $key => $val){
                $sb->add('<option value="'.$key.'">'.$val.'</option>'."\n");
            }
        	$sb->add('</select>'."\n");
        }
        $sb->add('</div>');
    }
    $html = $sb->getString();
    return $html;
}

function getSearchFieldsHTML2($data, $pdf = false){
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
    foreach($searchParams as $field => $value){
        $fieldArr = split('__', $field);
        $cat = preg_replace('/_/', ' ', $fieldArr[0]);
        $fieldName = preg_replace('/_/', ' ',$fieldArr[1]);
        if($value != 'any' && $field != "searchType"){
            if($pdf == false){
    	        $sb->add("<li>".$fieldName." - ".$value."</li>");
            }else{
                $sb->add("<tr><td>".$fieldName."</td><td>".$value."</td></tr>");
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

function getFilteredStateList2($data){
    $searchParams = array();
    foreach($data as $key => $val){
        if($val != ''){
            $searchParams[$key] = $val;
        }
    }
    //$fields = getSLATISearchData();
    $matchesByFIPSCode = array();
    $searchData = getSLATISearchFields(true);
    $firstField = true;
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
    $arr = $searchData['HasLawOrRestrictionFields'][$cat][$subcat][$val];

	$states = getStateListData($validStates);
    return renderSearchResultTable($states);
}

?>
