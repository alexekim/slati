<?php
    include_once 'data_controller.php';
    include 'header.php';
?>
<script language="javascript" type="text/javascript">
function stateSelect(){
	var val = document.getElementById("states").value;
	if(val != "-1"){
		$url = "statedetail.php?stateId="+val;
		window.location = $url;
	}
}
</script>
<div id="content" class="wide-layout">
    <?php print renderCrumbs('Individual States'); ?>
    <div id="content-well">
        <div class="stateWrap">
            <?php print getTextBlock('states_intro'); ?>
            <div class="mapWrap">
                <select onchange="stateSelect()" id="states" class="right">
                    <option value="-1">Select a State...</option>
                        <?php print getStatesAsOpts();?>
                </select>
                <?php print renderMap(); ?>
            </div><!-- mapWrap -->
            <?php print getTextBlock('states_body'); ?>
        </div><!-- stateWrap -->
    </div><!-- content-well -->
</div><!-- #content -->

<?php include 'footer.php'?>
