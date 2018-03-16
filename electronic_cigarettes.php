<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout"><!-- CONTENT -->
<?php print renderCrumbs('Electronic Cigarettes', array('Factsheets, Reports and Resources'=>'/slati/factsheets.php')); ?>
<div id="content-well"><!-- CONTENT-WELL -->
<?php print getTextBlock('electronic_cigarettes'); ?>
</div><!-- #CONTENT-WELL -->
</div><!-- #CONTENT -->

<?php include 'footer.php'?>