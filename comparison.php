<?php
    include 'header.php';
    include_once 'data_controller.php';
?>
<div id="content" class="wide-layout">
<?php
    print renderCrumbs($siteRoot, 'State Comparison'));
?>
        <div class="stateWrap">
            <p>Choose two states below and submit to compare each state's Medicaid plan and State Employee Health Plan.</p>
            <form name="searchForm" action="comparisonresults.php" method="post" class="searchForm">
                <label for="state1">State 1:</label>
                <select onchange="stateSelect()" id="state1" name="state1">
                    <option value="-1">Select a State...</option>
                    <?php print getStatesAsOpts();?>
                </select>
                <label for="state1">State 2:</label>
                <select onchange="stateSelect()" id="state2" name="state2">
                    <option value="-1">Select a State...</option>
                    <?php print getStatesAsOpts();?>
                </select>
                <input type="submit" class="submit" value="Compare"/>
            </form>
        </div>
</div>
<?php include 'footer.php'?>
