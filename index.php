<?php
include 'data_controller.php';
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
<div id="content" class="wide-layout"><!-- CONTENT -->
<?php print renderCrumbs(); ?>
<div id="content-well"><!-- CONTENT-WELL -->
<div align="center"><img src="imgs/logo10-11-02.gif" alt="info on state legislature regarding tobacco laws, smoking laws, public smoking policy, tobacco control, &amp; more" width="312" height="40"></div>
<div class="intro"><!-- INTRO -->
<?php print getTextBlock('index_intro'); ?>
</div><!-- #INTRO -->
<div class="stateWrap"><!-- STATEWRAP -->
<div class="mapWrap" style="text-align: center; margin-left: auto; margin-right: auto; padding-left: 100px;"><!-- MAPWRAP -->
<select onchange="stateSelect()" id="states" class="right">
<option value="-1">Select a State...</option>
<?php print getStatesAsOpts();?>
</select>
<?php print renderMap(); ?>
</div><!-- #MAPWRAP --> 
</div><!-- #STATEWRAP -->
</div><!-- #CONTENT-WELL -->
</div><!-- #CONTENT -->
<?php include 'footer.php'?>