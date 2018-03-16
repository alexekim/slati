<?php
include_once 'data_controller.php';
include 'header.php';
$searchFieldsHTML = getSearchFieldsHTML($_POST);
$searchFieldsPDFHTML = getSearchFieldsHTML($_POST, true);

$list = getFilteredStateList($_POST);
?>
<div id="content" class="wide-layout">
<?php print renderCrumbs("Search Results"); ?>
    <h2>Search Results</h2>
    <div class="searchResultsHeader">
<?php
    $searchFieldsHeader = '<h3>Search Results for States with the Following Attributes:</h3>';
    print $searchFieldsHeader;
    print $searchFieldsHTML;
?>
    </div>
<?php if($list == ""){ ?>
    <div class='searchResults'>No states matched the given criteria<br/>Please <a href='search.php'>Try Another Search</a></div>
<?php }else{
    $pdfHTML = $searchFieldsHeader.$searchFieldsPDFHTML.$list;
?>
    <ul class="stateList">
        <?php print $list; ?>
    </ul>
    <form name='searchPDF' id='searchPDF' action='generatePDF.php?type=search' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($pdfHTML); ?>' />
        <input type='submit' value='Download Search Results PDF' />
    </form>
<?php } ?>
</div> <!-- content -->
<?php include 'footer.php'?>
