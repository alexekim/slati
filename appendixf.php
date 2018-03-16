<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
    <?php print renderCrumbs('Appendix F'); ?>
    <div id="content-well">
<?php ob_start(); ?>
        <h2>State "Smoker Protection" Laws</h2>
        <p>Twenty-nine states and the District of Columbia have laws in effect elevating 
		smokers to a protected class.  The American Lung Association does not support these types of laws.</p>
		<?php
    $tables = getAppendixTableHTML('f');
    print $tables['table1'];
?>

<?php
    $html = ob_get_contents();
    ob_end_clean();
    print $html;
?>
<form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($html); ?>' />
        <input type='hidden' name='type' id='type' value='f' />
        <input type='submit' value='Download a PDF of this Report' />
    </form>
    </div><!-- content-well -->
</div><!-- #content -->
<?php include 'footer.php'?>
