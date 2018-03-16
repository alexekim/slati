<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
    <?php print renderCrumbs('Tobacco Control', array('Factsheets, Reports and Resources'=>'/slati/factsheets.php')); ?>
    <div id="content-well">
        <?php print getTextBlock('tobacco_control'); ?>
    </div><!-- content-well -->
</div><!-- #content -->

<?php include 'footer.php'?>
