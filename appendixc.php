<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
<?php print renderCrumbs('Appendix C', array('Factsheets, Reports and Resources'=>'/slati/factsheets.php')); ?>
    <div id="content-well">
    <?php $htmlHeader = <<<HTML
<h2>Current Cigarette Excise Tax Rates</h2>
<p>Current State Cigarette Tax Average: $1.65 per pack</p>
HTML;
?>
    <?php print $htmlHeader; ?>
        <?php
            $tables = getAppendixTableHTML('c');
?>
    <a href="#" id="toggleSortName">Sort by State Name</a>
    <a href="#" id="toggleSortRate">Sort by Tax Rate</a>
    <div id="table1wrapper">
        Taxes by State Name
        <?php print $tables['table1']; ?>
        <?php $table1PDFhtml = $htmlHeader.'<h3>Taxes by State Name</h3>'.$tables['table1']; ?>
        <form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
            <input type='hidden' name='html' id='html' value='<?php print urlencode($table1PDFhtml); ?>' />
            <input type='hidden' name='type' id='type' value='c' />
            <input type='submit' value='Download a PDF of this Report' />
        </form>
    </div>
    <div id="table2wrapper">
        Taxes by Tax Rate
        <?php print $tables['table2']; ?>
        <?php $table2PDFhtml = $htmlHeader.'<h2>Taxes by Tax Rate</h2>'.$tables['table2']; ?>
        <form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
            <input type='hidden' name='html' id='html' value='<?php print urlencode($table2PDFhtml); ?>' />
            <input type='hidden' name='type' id='type' value='c' />
            <input type='submit' value='Download a PDF of this Report' />
        </form>
    </div>
    </div><!-- content-well -->
</div><!-- #content -->
    <script type='text/javascript' language=¨Javascript">
        $(document).ready(function() {
            // put all your jQuery goodness in here.
            $('#table2wrapper').toggle();
            $('#toggleSortName').toggle();
        });
        $('[id^="toggleSort"]').click(function(){
            $('#table1wrapper').toggle();
            $('#table2wrapper').toggle();
            $('[id^="toggleSort"]').toggle();
        });
    </script>
<?php include 'footer.php'?>
