<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout"><!-- CONTENT -->
<?php print renderCrumbs('Smokefree Laws', array('Factsheets, Reports and Resources'=>'/slati/factsheets.php')); ?>
<div id="content-well"><!-- CONTENT-WELL -->
<?php print getTextBlock('smokefree_laws'); ?>
</div><!-- #CONTENT-WELL -->
</div><!-- #CONTENT -->

<?php include 'footer.php'?>