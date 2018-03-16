<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>
<style>

#content-well table td ,#content-well table th{  padding:10px; border-bottom:1px solid black; }
</style>
<div id="content" class="wide-layout">
    <?php print renderCrumbs('Appendix E'); ?>
    <div id="content-well">
<?php ob_start(); ?>
        <h2>Laws that Prevent Stronger Local Tobacco Control Laws</h2> 
		<p>A number of states have  laws in place that prevent local communities from passing stronger local  tobacco control laws.&nbsp; The American Lung  Association believes that local communities should be able to enact stronger  measures than the state to protect their citizens from tobacco&rsquo;s toll.</p>
<?php
    $tables = getAppendixTableHTML('e');
    print $tables['table1'];
?>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    print $html;
?>
<form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($html); ?>' />
        <input type='hidden' name='type' id='type' value='e' />
        <input type='submit' value='Download a PDF of this Report' />
    </form>
    </div><!-- content-well -->
</div><!-- #content -->

<?php include 'footer.php'?>
