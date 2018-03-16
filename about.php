<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>
<div id="content" class="wide-layout"><!-- CONTENT -->
<?php print renderCrumbs($pgTitle = 'About SLATI'); ?>
<div id="content-well"><!-- CONTENT-WELL -->
<?php print getTextBlock('about'); ?>
</div><!-- #CONTENT-WELL -->
</div><!-- #CONTENT -->
<?php include 'footer.php'?>