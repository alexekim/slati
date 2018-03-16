<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
    <?php print renderCrumbs('Factsheets, Reports, and Resources'); ?>
    <div id="content-well">
        <?php print getTextBlock('factsheets'); ?>
    </div><!-- content-well -->
</div><!-- #content -->

<?php include 'footer.php'?>
