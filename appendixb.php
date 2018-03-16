<?php
    include_once 'data_controller.php';
    include 'header.php';
?>
<div id="content" class="wide-layout">
    <?php print renderCrumbs('Appendix B'); ?>
<?php ob_start(); ?>
    <div id="content-well">
        <h2>State Laws Restricting Smoking by Location</h2>
        <?php
            $tables = getAppendixTableHTML('b');
            print $tables['table1'];
            print $tables['key'];
        ?>
        <div>
<?php 
    $html = ob_get_contents();
    ob_end_clean();
    print $html;
?>

<form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
        <input type='hidden' name='html' id='html' value='<?php print urlencode($html); ?>' />
        <input type='hidden' name='type' id='type' value='b' />
        <input type='submit' value='Download a PDF of this Report' />
    </form>
</div>
    </div><!-- content-well -->
</div><!-- #content -->
<?php
    include 'footer.php';
?>
