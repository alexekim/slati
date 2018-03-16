<?php
    include_once 'data_controller.php';
    include 'header.php'; 
?>

<div id="content" class="wide-layout">
    <?php print renderCrumbs('About SLATI'); ?>
    <div id="content-well">
        <?php print getTextBlock('contact', false); ?>
    </div><!-- content-well -->
</div><!-- #content -->

<?php include 'footer.php'?>
