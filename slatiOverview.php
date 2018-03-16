<?php
    include_once 'data_controller.php';
    include 'header.php';
?>

<div id="content" class="wide=layout">
<div class="stateWrap">
<div class="header">
        <h1><b>SLATI Overview Data</b></h1>
<p>This overview summarizes state tobacco control laws in effect in all 50 states and the 
	District of Columbia in the different areas covered by State Legislated Actions on 
	Tobacco Issues (SLATI).</p>

</div>
<?php ob_start(); ?>
        <?php
            $tables = getAppendixTableHTML('overview');
            print $tables['overviewData'];
        ?>
<?php 
    $html = ob_get_contents();
    ob_end_clean();
    print $html;
?>
<div>
<form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=overview' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($html); ?>' />
        <input type='hidden' name='type' id='type' value='overview' />
        <input type='submit' value='Download a PDF of this Report' />
    </form>
</div>
</div>
</div>
<?php include 'footer.php'?>
