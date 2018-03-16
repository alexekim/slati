<?php
include_once 'data_controller.php';
include 'header.php';


$searchFieldsHTML = getSLATISearchHTML();
?>
<div id="content" class="wide-layout">
<?php
    print(renderCrumbs('Search'));
?>
  
        <div class="stateWrap">
            <p>Make a selection from the drop down menus below to generate a list of links to specific SLATI state pages that either have or don't have the state law in question, or for certain categories like many under Smoking Restrictions a range of values.</p>
            <form name="searchForm" action="searchresults.php" onsubmit="return validateForm();" method="post" class="searchForm">
                <?php print($searchFieldsHTML); ?>
                <input type="submit" class="submit" value="Search"/>
            </form>
        </div> <!-- stateWrap -->
</div><!-- content -->
<script type="text/javascript" language='Javascript'>
    function validateForm(){
        if($('select[name!="searchType"]').filter(function(){return this.value!='any';}).length > 0){
            return true;
        }else{
            alert('Please select at least one coverage value to search.');
        }
        return false;
    }
</script>
<?php include 'footer.php'?>
