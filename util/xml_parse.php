<?php
function xml_parse_into_assoc($file) {
  $data = implode("", file($file));
  $p = xml_parser_create();
  $vals = null;
  $index = null;
 
  xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
 
  xml_parse_into_struct($p, $data, $vals, $index);
  xml_parser_free($p);

  $levels = array(null);
 
  foreach ($vals as $val) {
    if ($val['type'] == 'open' || $val['type'] == 'complete') {
      if (!array_key_exists($val['level'], $levels)) {
        $levels[$val['level']] = array();
      }
    }
   
    $prevLevel =& $levels[$val['level'] - 1];
    $parent = $prevLevel[sizeof($prevLevel)-1];
   
    if ($val['type'] == 'open') {
      $val['children'] = array();
      array_push($levels[$val['level']], $val);
      continue;
    }
   
    else if ($val['type'] == 'complete') {
      $parent['children'][$val['tag']] = $val['value'];
    }
   
    else if ($val['type'] == 'close') {
      $pop = array_pop($levels[$val['level']]);
      $tag = $pop['tag'];
     
      if ($parent) {
        if (!array_key_exists($tag, $parent['children'])) {
          $parent['children'][$tag] = $pop['children'];
        }
        else if (is_array($parent['children'][$tag])) {
            if(!isset($parent['children'][$tag][0])) {
                $oldSingle = $parent['children'][$tag];
                $parent['children'][$tag] = null;
                $parent['children'][$tag][] = $oldSingle;
               
            }
              $parent['children'][$tag][] = $pop['children'];
        }
       
      }
      else {
        return(array($pop['tag'] => $pop['children']));
      }
    }
   
    $prevLevel[sizeof($prevLevel)-1] = $parent;
  }
}
function xml_parse_generated_date($file) {
  $data = implode("", file($file));
  $p = xml_parser_create();
  $vals = null;
  $index = null;
  xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
  xml_parse_into_struct($p, $data, $vals, $index);
  xml_parser_free($p);
  $levels = array(null);
  $tStr = $vals[0]["attributes"]["generated"];
  $y = substr($tStr, 0, 4);
  $m = substr($tStr, 5, 2);
  $d = substr($tStr, 8, 2);
  $h = substr($tStr, 11, 2);
  $mi = substr($tStr, 14, 2);
  $s = substr($tStr, 17, 2);
  date_default_timezone_set('America/New_York') ;
  return mktime($h, $mi, $s, $m, $d, $y);
}
?>
