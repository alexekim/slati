<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
<?php print renderCrumbs('Appendix D', array('Factsheets, Reports and Resources'=>'/slati/factsheets.php')); ?>
    <div id="content-well">
    <?php $htmlHeader = <<<HTML
        <h2>Net Cigarette Excise Tax Revenue in Fiscal Year 2012</h2>
HTML;
    $tables = getAppendixTableHTML('d');

?>
    <?php print $htmlHeader; ?>
   <a href="#" id="toggleSortName">Sort by State Name</a>
    <a href="#" id="toggleSortRate">Sort by Revenue Collected</a>
    <div id="table1wrapper">
        Revenue by State Name
        <?php print $tables['table1']; ?>
        <?php $table1PDFhtml = $htmlHeader.'<h3>Revenue by State Name</h3>'.$tables['table1']; ?>
        <form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
            <input type='hidden' name='html' id='html' value='<?php print urlencode($table1PDFhtml); ?>' />
            <input type='hidden' name='type' id='type' value='d' />
            <input type='submit' value='Download a PDF of this Report' />
        </form>
    </div>
    <div id="table2wrapper">
        Revenue by Revenue Collected
        <?php print $tables['table2']; ?>
        <?php $table2PDFhtml = $htmlHeader.'<h2>Revenue by Revenue Collected</h2>'.$tables['table2']; ?>
        <form name='appendixPDF' id='appendixPDF' action='generatePDF.php?type=appendix' method="POST">
            <input type='hidden' name='html' id='html' value='<?php print urlencode($table2PDFhtml); ?>' />
            <input type='hidden' name='type' id='type' value='d' />
            <input type='submit' value='Download a PDF of this Report' />
        </form>
    </div>
 <p>&nbsp;</p>
<p align="center"><font size="1"><em>Source: Orzechowski &amp; Walker, Tax Burden on Tobacco, 2009</em></font></p>
<div align="center">
<p>&nbsp;</p>
<p><font size="1"><font size="1"><font size="1">Last updated: 7/16/2013</font></font></font></p>

    </div><!-- content-well -->
</div><!-- #content -->
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
