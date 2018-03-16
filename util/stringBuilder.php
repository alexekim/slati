<?php
/*
 * Class: StringBuilder
 * Author: Adam Mitchell
 * Rev: 1.2
 * Date: 4.21.09
 * Purpose: Simple string builder to create large strings
 * Description: Builds large strings using an array instead of string concatenation
 * Use: Create an instance and use StringBuilder->add($data) to append data to the string
 * 		Handles strings, arrays of strings, and nested arrays of strings
 */ 
class StringBuilder{
    var $strings = array();
    
    /*
     * StringBuilder Constructor
     * Params: $data as String or Array
     */
    function StringBuilder($data = null){
    	if($data != null){
    		$this->add($data);
    	}
    }
    
    /*
     * add
     * Params: $data as String or Array
     * Desc: Calls addStr() or addArr() based on type of $data
     */
    function add($data){
        if(is_string($data)){
        	return $this->addStr($data);
        }elseif(is_array($data)){
        	return $this->addArr($data);
        }else{
        	return false;
        }
    }
    
    /*
     * addStr
     * Params: $str as String
     * Desc: Adds $str to strings array
     */
    function addStr($str){
        if(is_string($str)){
            array_push($this->strings, $str);
            return true;
        }
        return false;
    }
    
    /*
     * addArr
     * Params: $arr as array
     * Desc: Calls add() for each item in $arr
     * Notes:
     * This function is non-transactional
     * i.e. valid elements of $arr will be added even if others fail
     * Any array elements that are not strings will not be added and will fail silently
     * Calls add() which can handle strings or arrays, allowing for support of nested arrays
     */ 
    function addArr($arr){
        foreach($arr as $item){
            $this->add($item);
        }
        return true;
    }
    
    /*
     * getString
     * Returns: Full concatenated string from $strings array 
     */
    function getString(){
        return implode('', $this->strings);
    }
    
    /*
     * getArr
     * Return: Current full $strings array
     */
    function getArr(){
    	return $this->strings;
    }
    
    /*
     * clear
     * Desc: Reinitializes $strings to empty
     */
    function clear(){
    	unset($this->strings);
    	$this->strings = array();
    	return true;
    }
}
?>
