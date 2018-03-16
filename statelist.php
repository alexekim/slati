<?php
include_once 'data_controller.php';
include 'header.php';

$states = getFullStateList();
?>
    <div id="content" class="wide-layout">
        <?php print renderCrumbs('State Lists'); ?>
        <div id="content-well">
            <div class='stateWrap'>
                <h2>Full State List</h2>
                <ul class="stateList">
                    <?php print $states;?>
                </ul>
            </div>
<?php include 'footer.php'?>
