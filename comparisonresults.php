<?php
    include_once 'data_controller.php';
    include 'header.php';
    $data = getStateComparison($_POST);
    $s1n = $data['s1name'];
    $s2n = $data['s2name'];
    $list = $data['html'];
?>
<div id="content" class="wide-layout">
<?php
    print(renderCrumbs($siteRoot, 'Comparison Results'));
?>
    <h2>State Coverage Comparison</h2>
    <div class="searchResultsHeader">
        <?php print getIconLegend(); ?>

    <?php
        $comparisonHeader = '<h4>Below is a comparison of '.$s1n.' and '.$s2n.':</h4>';
        print $comparisonHeader;
    ?>
    </div>
<?php if($list == ""){ ?>
    <div class='searchResults'>No states matched the given criteria<br/>Please <a href='comparison.php'>Try Another Search</a></div>
<?php }else{
    $pdfHTML = $comparisonHeader.$list;
?>
    <ul class="stateList">
        <?php print $list; ?>
    </ul>
    <form name='searchPDF' id='searchPDF' action='generatePDF.php?type=comparison' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($pdfHTML); ?>' />
        <input type='submit' value='Download Comparison PDF' />
    </form>
<?php } ?>
</div><!-- content -->
<?php include 'footer.php'?>
