<?php
    include 'data_controller.php';
    include 'header.php';

$id = $_GET['stateId'];
if($id){
	$isPDF = false;
    $htmlData = getStateHTMLData($id, $isPDF);
//    $pdfUrlStr = '/generatePDF.php?sn='.$htmlData["stateName"].'&id='.$id.'&type=';
?>

<div id="content" class="wide-layout"><!-- content  -->

    <?php print renderCrumbs($htmlData["stateName"], array('State List'=>'/slati/states.php')); ?>
<div class='stateWrap'><!-- stateWrap -->
<div class="header"><!-- header -->
<h1><b>SLATI State Information: <?php print $htmlData["stateName"]?></b></h1>

<div id="stateLogo"><img align="left" src="<?php print'http://'.server.dir; ?>imgs/states/states-new/<?php print getStandardizedStateName($htmlData["stateName"]); ?>.gif"></div>
<h1><b><?php print $htmlData["stateName"]; ?></b></h1>
</div><!-- /header  -->
 <div class="statePdfLink" style="float:right;margin-right:-100px;"><a href='generatePDF.php?sn=<?php print $htmlData["stateName"]; ?>&id=<?php print $id; ?>&type=stateDetail'>Download the <?php print $htmlData["stateName"];?> SLATI PDF</a></div>


<?php print $htmlData['jumpLinksHTML']; ?>
<br/><br/>
<a href="http://www.stateoftobaccocontrol.org/state-grades/<?php print str_replace(' ', '-', strToLower($htmlData['stateName'])); ?>"  target="_blank">View the State of Tobacco Control: 2012 Report for <?php print $htmlData["stateName"]; ?></a>
<br/>
<a href="/slati/search.php">Search the SLATI Database and customize your own criteria</a>
<br/>
<a href="/slati/states.php">Select a different state</a>
<br/>

<?php
    print $htmlData['catHTML']; 
}else{
    print "<h1>No State Specified</h1>";
}
?>
</div><!-- /statewrap -->
</div><!-- /content -->
<?php include 'footer.php'?>