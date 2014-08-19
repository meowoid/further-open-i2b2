<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>i2b2 Web Client (FURTHeR)</title>
<!--
 *  *************************
 *       i2b2 Web Client
 *           v1.3.1
 *  *************************
 *  @modified: 4/30/2009
 *  Contributors:
 *     Nick Benik
 *     Griffin Weber, MD, PhD
 *
 */-->

<meta http-equiv="X-UA-Compatible" content="IE=8" />

<!-- LOAD YUI FROM Yahoo's CDN
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/yahoo/yahoo.js" ></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/event/event.js" ></script>
<script type="text/javascript" src="http://yui.yahooapis.com/2.5.2/build/dom/dom.js"></script>
... etc ...
-->

<!-- LOAD YUI FROM local server -->
<script type="text/javascript" src="js-ext/yui/build/yahoo/yahoo.js" ></script>
<script type="text/javascript" src="js-ext/yui/build/event/event.js" ></script>
<script type="text/javascript" src="js-ext/yui/build/dom/dom.js"></script>
<script type="text/javascript" src="js-ext/yui/build/yuiloader/yuiloader.js"></script>
<script type="text/javascript" src="js-ext/yui/build/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="js-ext/yui/build/element/element-beta.js"></script>
<script type="text/javascript" src="js-ext/yui/build/container/container_core.js"></script>
<script type="text/javascript" src="js-ext/yui/build/container/container.js"></script>
<script type="text/javascript" src="js-ext/yui/build/resize/resize.js"></script>
<script type="text/javascript" src="js-ext/yui/build/utilities/utilities.js"></script>
<script type="text/javascript" src="js-ext/yui/build/menu/menu.js" ></script>
<script type="text/javascript" src="js-ext/yui/build/calendar/calendar.js"></script>
<script type="text/javascript" src="js-ext/yui/build/treeview/treeview.js" ></script>
<script type="text/javascript" src="js-ext/yui/build/tabview/tabview.js"></script>
<script type="text/javascript" src="js-ext/yui/build/animation/animation.js"></script>

<!-- Bug in IE - use MINIMUM number of LINK and STYLE tags in the DOM as possible: http://support.microsoft.com/kb/262161 -->
<style>
	@import url(js-ext/yui/build/fonts/fonts-min.css);
	@import url(js-ext/yui/build/tabview/assets/skins/sam/tabview.css);
	@import url(js-ext/yui/build/menu/assets/skins/sam/menu.css);
	@import url(js-ext/yui/build/button/assets/skins/sam/button.css);
	@import url(js-ext/yui/build/container/assets/skins/sam/container.css);
	@import url(js-ext/yui/build/container/assets/container.css);
	@import url(js-ext/yui/build/calendar/assets/calendar.css);
	@import url(js-ext/yui/build/treeview/assets/treeview.css);
	@import url(js-ext/yui/build/resize/assets/skins/sam/resize.css);
	@import url(assets/mod-treeview.css);
	@import url(assets/help_viewer.css);
	@import url(assets/msg_sniffer.css);

</style>
<!--  External libraries -->
<script type="text/javascript" src="js-ext/prototype.js"></script>
<!-- <script type="text/javascript" src="js-ext/firebug/firebug-lite-compressed.js"></script> -->
<script type="text/javascript" src="js-ext/firebug/firebugx.js"></script>
<script type="text/javascript" src="js-ext/excanvas.js"></script>


<!-- load i2b2 framework -->
<script type="text/javascript" src="js-i2b2/i2b2_loader.js?v=7"></script>
<link type="text/css" href="assets/i2b2.css" rel="stylesheet" />
<link type="text/css" href="assets/i2b2-NEW.css" rel="stylesheet" />
<link type="text/css" href="assets/i2b2-further.css" rel="stylesheet" />

<script type="text/javascript">

/*****************************************************/
/******************** GLOBAL VARS ********************/
/*****************************************************/

//var queryStatusDefaultText = 'Drag query items to one or more groups then click Run Query.';

/****************************************************/
/******************** INITIALIZE ********************/
/****************************************************/

<?php
//Get the config from an ini file
$ini = parse_ini_file("includes/i2b2config.ini.php");
?>

function initI2B2() {
	i2b2.events.afterCellInit.subscribe(
		(function(en,co,a) {
			var cellObj = co[0];
			var cellCode = cellObj.cellCode;
			switch (cellCode) {
				case "PM":
					// This i2b2 design implementation uses a prebuild login DIV we connect the Project Management cell to
					// handle this method of login, the other method used for login is the PM Cell's built in floating
					// modal dialog box to prompt for login credentials.  You can edit the look and feel of this dialog box
					// by editing the CSS file.  You can remark out the lines below with no ill effect.  Use the following
					// javascript function to display the modal login form: i2b2.PM.doLoginDialog();
					//cellObj.doConnectForm($('loginusr'),$('loginpass'),$('logindomain'), $('loginsubmit'));
					
					<?php
					session_start();
					if ($ini['cas.enabled']) {
						echo 'var inputUser="' . $_SESSION['userId'] . '";
						      var inputPass="' . $_SESSION['password'] . '";
						      var inputDomain="FURTHeR";
						      i2b2.PM.doAutoLogin(inputUser,inputPass,inputDomain);
						      break;';
					} else {
						echo 'i2b2.PM.doLoginDialog();';
					}
					?>
			}
		})
	);


	i2b2.events.afterHiveInit.subscribe(
		(function(ename) {
			// Misc GUI actions that need to be done after loading
//			$('infoQueryStatusText').innerHTML = queryStatusDefaultText;
			$('QPD1').style.background = '#FFFFFF';
			$('queryBalloon1').style.display = 'block';
		})
	);


	i2b2.events.afterLogin.subscribe(
		(function() {
			// after successful login hide the login box and display the application GUI
			$('topBar').style.display = 'block';
			$('screenQueryData').style.display = 'block';
			i2b2.hive.MasterView.setViewMode('Patients');
			if (i2b2.PM.model.login_debugging) { $('debugMsgSniffer').show(); }
		}), i2b2
	);

	// start the i2b2 framework
	i2b2.Init();
}

function init() {
	// ------------------------------------------------------
	// put any pre-i2b2 initialization code here
	// ------------------------------------------------------

	// initialize the i2b2 framework
	initI2B2();

}
YAHOO.util.Event.addListener(window, "load", init);

/********************************************************/
/******************** JAVASCRIPT END ********************/
/********************************************************/
</script>

</head>
<body class="yui-skin-sam">
<div id="title-back"></div>
<div class="pageMask" id="topMask" style="display:none;">&nbsp;</div>


<div id="help-viewer-panel" style="display:none;">
	<div class="hd">OpenFurther Help</div>
	<div class="bd" id="help-viewer-body" class="help-viewer-body">
		<p>Lorem Ipsum...</p>
	</div>
	<div class="ft"></div>
</div>

<div id="dua-viewer-panel" style="display:none;">
	<div class="hd">Sample Agreement</div>
	<div class="bd" id="dua-viewer-body" class="help-viewer-body">
		<p>Lorem Ipsum...</p>
	</div>
	<div class="ft"></div>
</div>

<div id="datasource-info-viewer-panel" style="display:none;">
	<div class="hd">Data source Information</div>
	<div class="bd" id="datasource-info-viewer-body" class="help-viewer-body">
		<p>Loading</p>
	</div>
	<div class="ft"></div>
</div>

<div id="explain-results-viewer-panel" style="display:none;">
	<div class="hd">Results Explanation</div>
	<div class="bd" id="explain-results-viewer-body" class="help-viewer-body"> 
		<p>Lorem Ipsum...</p>
	</div>
	<div class="ft"></div>
</div>

<div id="commViewerSingleMsg-panel" style="display:none;">
	<div class="hd">XML Message</div>
	<div class="bd" id="commViewerSingleMsg-body"><div class="xmlMsg"></div></div>
	<div class="ft"></div>
</div>



<table border="0" cellspacing="0" cellpadding="0" width="100%" id="topBarTable">
<tr>
	<td align="left" valign="middle"><img src="assets/images/title.gif" id="topBarTitle" border="0" alt="" /></td>
	<td align="right" valign="middle">
		<div id="topBar" style="display:none;">
			<a id="datasource-info" class="further_datasource-info" href="Javascript:void(0)" onclick="i2b2.hive.DataSourceInfoViewer.show('datasource-info');">Data Source Info</a>
			&nbsp;|&nbsp;
			<a id="viewMode-Patients" href="Javascript:void(0)" onclick="i2b2.hive.MasterView.setViewMode('Patients');">Find Patients</a>
			&nbsp;|&nbsp;
			<a id="viewMode-Analysis" href="Javascript:void(0)" onclick="i2b2.hive.MasterView.setViewMode('Analysis');">Analysis Tools</a>
			&nbsp;|&nbsp;
			<span id="debugMsgSniffer" style="display:none">
				<a href="Javascript:void(0)" onclick="i2b2.hive.MsgSniffer.show();">Log</a>
				&nbsp;|&nbsp;
			</span>
			<a id="duaLink" href="Javascript:void(0)" onclick="i2b2.hive.HelpViewer.show('dua', 'dataUseAgreement.php');">Data Use Agreement</a>
			&nbsp;|&nbsp;
			<a id="helpLink" href="Javascript:void(0)" onclick="i2b2.hive.HelpViewer.show('help', 'help/default.htm');">Help</a>
			&nbsp;|&nbsp;
			<?php
				if ($ini['cas.enabled']) {
					echo '<a href="?logout=true&userId=' . $_SESSION['userId'] . '">Logout</a>';
				} else {
					echo '<a href="Javascript:void(0);" onclick="i2b2.PM.doLogout();">Logout</a>';
				}
			?>
		</div>
	</td>
</tr>
</table>


<div id="screenQueryData" style="display:none">


<!-- ############### <ONT View> ############### -->
	<div id="ontMainBox" style="display:none">
		<div class="TopTabs">
			<div style="position:absolute;z-index:200;">
				<div id="tabNavigate" class="tabBox active" onclick="i2b2.ONT.view.main.selectTab('nav')">
				<div>Navigate Terms</div>
			</div>
				<div id="tabFind" class="tabBox" onclick="i2b2.ONT.view.main.selectTab('find')">
				<div>Find Terms</div>
			</div>
			</div>
			<div class="opXML">
<!--				<a href="JavaScript:showXML('ONT',i2b2.ONT.view.main.currentTab,'Request');" class="debug"><img src="assets/images/msg_request.gif" border="0" width="16" height="16" alt="Show XML Request" title="Show XML Request" /></a> -->
<!--				<a href="JavaScript:showXML('ONT',i2b2.ONT.view.main.currentTab,'Response');" class="debug"><img src="assets/images/msg_response.gif" border="0" width="16" height="16"  alt="Show XML Response" title="Show XML Response" /></a> -->
				<a href="JavaScript:showXML('ONT',i2b2.ONT.view.main.currentTab,'Stack');" class="debug"><img src="assets/images/msg_stack.gif" border="0" width="16" height="16"  alt="Show XML Message Stack" title="Show XML Message Stack" /></a>
				<a href="JavaScript:i2b2.ONT.view.main.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16" alt="Show Options" title="Show Options" /></a>
				<a href="JavaScript:i2b2.ONT.view.main.ZoomView();"><img id="ontZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/ONT/assets/zoom_icon.gif" alt="Toggle Zoom" title="Toggle Zoom" /></a>
			</div>
		</div>
		<div id="ontMainDisp">
			<div id="ontNavDisp">
				<!--<div id="standardQuery">Standard Query Items</div>-->
				<div id="ontNavResults"></div>
			</div>
			<div id="ontFindDisp" style="display:none">
				<a id="ontFindTabName" href="Javascript:i2b2.ONT.view.find.selectSubTab('names')" class="findSubTabSelected" >Search by Names</a>
				<a id="ontFindTabCode" href="Javascript:i2b2.ONT.view.find.selectSubTab('codes')" class="findSubTab" >Search by Codes</a>
				<div id="ontFindFrameName" class="findSubFrame">
					<form id="ontFormFindName" method="post" action="JavaScript:i2b2.ONT.ctrlr.FindBy.clickSearchName();" style="margin:0px; padding:0px;">
					<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%;">
					<tr>
						<td style="width:100px;" valign="middle"><select name="ontFindStrategy" style="width:90px;overflow:hidden;font-size:11px;"><option value="contains">contains</option><option value="exact">exact</option><option value="left">left</option><option value="right">right</option></select></td>
						<td valign="middle"><input name="ontFindNameMatch" type="text" maxlength="100" style="border:1px solid #7c9cba;width:100%;font-size:11px;" /></td>
					</tr>
					<tr><td colspan="2" style="height:5px;overflow:hidden;"></td></tr>
					<tr>
						<td valign="middle" style="width:100px;"><div class="ontFindButton"><a href="JavaScript:i2b2.ONT.ctrlr.FindBy.clickSearchName();">Find</a></div></td>
						<td valign="middle"><select id="ontFindCategory" name="ontFindCategory" style="font-size:11px;"><option value="i2b2">Any Category</option></select></td>
					</tr>
					</table>
					</form>
				</div>
				<div id="ontSearchNamesResults" oncontextmenu="return false"></div>
				<div id="ontFindFrameCode" class="findSubFrame" style="display:none">
					<form id="ontFormFindCode" method="post" action="JavaScript:i2b2.ONT.ctrlr.FindBy.clickSearchCode();" style="margin:1px; padding:0px;">
					<div><input id="ontFindCodeMatch" type="text" maxlength="100" style="border:1px solid #7c9cba;width:95%;font-size:11px;" /></div>
					<table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%; margin-top:5px;">
					<tr>
						<td style="width:100px;" valign="middle">
							<div class="ontFindButton" style=""><a href="JavaScript:i2b2.ONT.ctrlr.FindBy.clickSearchCode();">Find</a></div>
						</td>
						<td valign="middle">
						<select id="ontFindCoding" name="ontFindCoding" style="font-size:11px;">
							<option value="">Loading...</option>
						</select>
						</td>
					</tr>
					</table>
					</form>
				</div>
				<div id="ontSearchCodesResults" oncontextmenu="return false"></div>
			</div>
			<div id="ontBalloonBox" xonmouseover="i2b2.ONT.view.main.hballoon.hideBalloons()">
			<!--
				<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center">
				<div id="ontBalloon">drag an<br />item<br />from here</div>
				</td></tr></table>
			-->
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
<!-- ############### </ONT View> ############### -->


<!-- ############### <WRK View> ############### -->
	<div id="wrkWorkplace" style="display:none;">
		<div class="TopTabs">
			<div class="tabBox active">
				<div>Workplace</div>
			</div>
			<div class="opXML">
<!--				<a href="JavaScript:showXML('WORK','main','Request');" class="debug"><img src="assets/images/msg_request.gif" border="0" width="16" height="16" alt="Show XML Request" title="Show XML Request" /></a> -->
<!--				<a href="JavaScript:showXML('WORK','main','Response');" class="debug"><img src="assets/images/msg_response.gif" border="0" width="16" height="16" alt="Show XML Response" title="Show XML Response" /></a> -->
				<a href="JavaScript:showXML('WORK','main','Stack');" class="debug"><img src="assets/images/msg_stack.gif" border="0" width="16" height="16"  alt="Show XML Message Stack" title="Show XML Message Stack" /></a>
<!--				<a href="JavaScript:i2b2.WORK.view.main.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16" alt="Show Options" title="Show Options" /></a> -->
				<a href="JavaScript:i2b2.WORK.view.main.ZoomView();"><img id="wrkZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/WORK/assets/zoom_icon.gif" alt="Toggle Zoom" title="Toggle Zoom" /></a>
			</div>
		</div>
		<div class="bodyBox">
			<div id="wrkTreeview" class="StatusBoxText"></div>
		</div>
	</div>
<!-- ############### </WRK View> ############### -->



<!-- ############### <CRC History View> ############### -->
	<div id="crcHistoryBox" style="display:none;">
		<div class="TopTabs">
			<div class="tabBox active">
				<div>Previous Queries</div>
			</div>
			<div class="opXML">
<!--				<a href="JavaScript:showXML('CRC','history','Request');" class="debug"><img src="assets/images/msg_request.gif" border="0" width="16" height="16" alt="Show XML Request" title="Show XML Request" /></a> -->
<!--				<a href="JavaScript:showXML('CRC','history','Response');" class="debug"><img src="assets/images/msg_response.gif" border="0" width="16" height="16" alt="Show XML Response" title="Show XML Response" /></a> -->
				<a href="JavaScript:showXML('CRC','history','Stack');" class="debug"><img src="assets/images/msg_stack.gif" border="0" width="16" height="16"  alt="Show XML Message Stack" title="Show XML Message Stack" /></a>
				<a href="JavaScript:i2b2.CRC.view.history.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16" alt="Show Options" title="Show Options" /></a>
				<a href="JavaScript:i2b2.CRC.view.history.ZoomView();"><img id="histZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/WORK/assets/zoom_icon.gif" alt="Toggle Zoom" title="Toggle Zoom" /></a>
			</div>
		</div>
		<div class="bodyBox">
			<div id="crcHistoryData" oncontextmenu="return false"></div>
		</div>
	</div>
<!-- ############### </CRC History View> ############### -->



<!-- ############### <CRC QueryTool View> ############### -->
	<div id="crcQueryToolBox">
		<div class="TopTabs">
			<div class="tabBox active">
				<div>Query Tool</div>
			</div>
			<div class="opXML">
<!--				<a href="JavaScript:showXML('CRC','QT','Request');" class="debug"><img src="assets/images/msg_request.gif" border="0" width="16" height="16" alt="Show XML Request" title="Show XML Request" /></a> -->
<!--				<a href="JavaScript:showXML('CRC','QT','Response');" class="debug"><img src="assets/images/msg_response.gif" border="0" width="16" height="16" alt="Show XML Response" title="Show XML Response" /></a> -->
				<a href="JavaScript:showXML('CRC','QT','Stack');" class="debug"><img src="assets/images/msg_stack.gif" border="0" width="16" height="16"  alt="Show XML Message Stack" title="Show XML Message Stack" /></a>
				<a href="JavaScript:i2b2.CRC.view.QT.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16" alt="Show Options" title="Show Options" /></a>
				<a href="JavaScript:i2b2.CRC.view.QT.ZoomView();"><img id="qtZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/CRC/assets/zoom_icon.gif" alt="Toggle Zoom" title="Toggle Zoom" /></a>
			</div>
		</div>
		<div class="bodyBox" >
			<div class="queryNameBar" style="width:512px;">
				<div class="queryLabel">Query Name:&nbsp;</div>
				<div id="queryName"></div>
			</div>
			<div id="crcQryToolPanels" style="width:512px;overflow:hidden;">
			<div style="width:550px;">
				<div class="qryPanel">
					<div class="qryPanelTitle">
						<div class="qryPanelClear" style="float:right"><a href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[0].doDelete();"><img src="js-i2b2/cells/CRC/assets/QryTool_b_clear.gif" border="0" alt="Clear" /></a></div>
						<div id="queryPanelTitle1">Group 1</div>
					</div>
					<div class="qryPanelButtonBar">
						<div class="qryButtonDate" style="float:left"><a id="queryPanelDatesB1" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.dateConstraint.showDates(0)" title="Select the date range for this group's criterion to have occured within...">Dates</a></div>
						<div class="qryButtonOccurs" style="float:left"><a id="queryPanelOccursB1" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[0].showOccurs()" title="Select the minimum number of times this group's criterion has occured...">Occurs &gt; <span id="QP1Occurs">0</span>x</a></div>
						<div class="qryButtonExclude queryPanelButtonSelected" style="float:left"><a id="queryPanelExcludeB1" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[0].doExclude()" title="Exclude records matching this group's criteria...">Exclude</a></div>
					</div>
					<div id="QPD1" style="clear:both" oncontextmenu="return false" class="queryPanel"></div>
				</div>
				<div class="qryPanel" style="margin-left:2px;">
					<div class="qryPanelTitle">
						<div class="qryPanelClear" style="float:right"><a href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[1].doDelete();"><img src="js-i2b2/cells/CRC/assets/QryTool_b_clear.gif" border="0" alt="Clear" /></a></div>
						<div id="queryPanelTitle2">Group 2</div>
					</div>
					<div class="qryPanelButtonBar">
						<div class="qryButtonDate" style="float:left"><a id="queryPanelDatesB2" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.dateConstraint.showDates(1)" title="Select the date range for this group's criterion to have occured within...">Dates</a></div>
						<div class="qryButtonOccurs" style="float:left"><a id="queryPanelOccursB2" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[1].showOccurs()" title="Select the minimum number of times this group's criterion has occured...">Occurs &gt; <span id="QP2Occurs">0</span>x</a></div>
						<div class="qryButtonExclude queryPanelButtonSelected" style="float:left"><a id="queryPanelExcludeB2" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[1].doExclude()" title="Exclude records matching this group's criteria...">Exclude</a></div>
					</div>
					<div id="QPD2" style="clear:both" oncontextmenu="return false" class="queryPanel"></div>
				</div>
				<div class="qryPanel" style="margin-left:2px;">
					<div class="qryPanelTitle">
						<div class="qryPanelClear" style="float:right"><a href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[2].doDelete();"><img src="js-i2b2/cells/CRC/assets/QryTool_b_clear.gif" border="0" alt="Clear" /></a></div>
						<div id="queryPanelTitle3">Group 3</div>
					</div>
					<div class="qryPanelButtonBar">
						<div class="qryButtonDate" style="float:left"><a id="queryPanelDatesB3" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.dateConstraint.showDates(2)" title="Select the date range for this group's criterion to have occured within...">Dates</a></div>
						<div class="qryButtonOccurs" style="float:left"><a id="queryPanelOccursB3" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[2].showOccurs()" title="Select the minimum number of times this group's criterion has occured...">Occurs &gt; <span id="QP3Occurs">0</span>x</a></div>
						<div class="qryButtonExclude queryPanelButtonSelected" style="float:left"><a id="queryPanelExcludeB3" class="queryPanelButton" href="JavaScript:i2b2.CRC.ctrlr.QT.panelControllers[2].doExclude()" title="Exclude records matching this group's criteria...">Exclude</a></div>
					</div>
					<div id="QPD3" style="clear:both" oncontextmenu="return false" class="queryPanel"></div>
				</div>
				<div id="assocResultBox" style="display:none">Drop a previous result set on here</div>
				<div style="clear:both; width:100%; height:5px; overflow:hidden;"></div>

				<div id="queryBalloonBox" onmouseover="i2b2.CRC.view.QT.hballoon.hideBalloons()">
					<div class="queryBalloon" id="queryBalloon1">drop a<br />term<br />on here</div>
					<div class="queryBalloonAnd" id="queryBalloonAnd1">AND</div>
					<div class="queryBalloon" id="queryBalloon2">drop a<br />term<br />on here</div>
					<div class="queryBalloonAnd" id="queryBalloonAnd2">AND</div>
					<div class="queryBalloon" id="queryBalloon3">drop a<br />term<br />on here</div>
				</div>
			</div>

			</div>
			<div id="qryToolFooter" style="width:512px; overflow:hidden">
				<div id="runBox"><a href="JavaScript:i2b2.CRC.ctrlr.QT.doQueryRun()">Run Query</a></div>
				<div id="newBox"><a href="JavaScript:i2b2.CRC.ctrlr.QT.doQueryClear();">New Query</a></div>
				<div id="groupCount" style="width:175px;float:left;height:16px;overflow:hidden;"></div>
				<div id="scrollBox">
					<a href="JavaScript:i2b2.CRC.ctrlr.QT.doScrollFirst();"><img id="panelScrollFirst" src="js-i2b2/cells/CRC/assets/QryTool_b_first_hide.gif" border="0" alt="Go First" /></a>
					<a href="JavaScript:i2b2.CRC.ctrlr.QT.doScrollPrev();"><img id="panelScrollPrev" src="js-i2b2/cells/CRC/assets/QryTool_b_prev_hide.gif" border="0" alt="Go Previous" /></a>
					<a href="JavaScript:i2b2.CRC.ctrlr.QT.doScrollNew();"><img src="js-i2b2/cells/CRC/assets/QryTool_b_newgroup.gif" border="0" alt="Add New" /></a>
					<a href="JavaScript:i2b2.CRC.ctrlr.QT.doScrollNext();"><img id="panelScrollNext" src="js-i2b2/cells/CRC/assets/QryTool_b_next_hide.gif" border="0" alt="Go Next" /></a>
					<a href="JavaScript:i2b2.CRC.ctrlr.QT.doScrollLast();"><img id="panelScrollLast" src="js-i2b2/cells/CRC/assets/QryTool_b_last_hide.gif" border="0" alt="Go Last" /></a>
				</div>
			</div>
		</div>
	</div>


<!-- ############### <CRC Status View> ############### -->
	<div id="crcStatusBox" style="display:none">
		<div class="TopTabs">
			<div class="tabBox tabQueryStatus active"><div>Query Status</div></div>
			<div class="further_explain-results-container"><a class="further_explain-results" href="Javascript:void(0);" onclick="i2b2.hive.HelpViewer.show('explain-results', 'help/results.htm');">Explain Results</a></div>
		</div>
		<div class="StatusBox">
			<div id="infoQueryStatusText" class="StatusBoxText" oncontextmenu="return false"></div>
		</div>
	</div>

<!-- ############### <Workplace> ############### -->
	<div class="PluginListBox" style="display:none;">
	</div>
<!-- ############### </Workplace> ############### -->

<!-- ############### <PluginMgr List View> ############### -->
	<div id="anaPluginListBox" style="display:none">
		<div class="TopTabs">
			<div class="tabBox tabPluginList active" ><div>Plugins</div></div>
			<div class="opXML">
<!--			<a href="JavaScript:i2b2.PLUGINMGR.view.list.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16"></a> -->
				<a href="JavaScript:i2b2.PLUGINMGR.view.list.ZoomView();"><img id="pluglstZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/CRC/assets/zoom_icon.gif" alt="Toggle Zoom" /></a>
			</div>
		</div>
		<a id="plugListRecDETAIL-CLONE" class="pluginRecordBox DETAIL" style="display:none">
			<div class="Icon"><img src="js-i2b2/cells/PLUGINMGR/assets/DEFAULTLIST_icon_32x32.gif" alt="" /></div>
			<div class="txtBoundBox">
				<div class="Name">Plugin Name</div>
				<div class="Descript">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
			</div>
			<div style="clear:both"></div>
		</a>
		<a id="plugListRecSUMMARY-CLONE" class="pluginRecordBox SUMMARY" style="display:none">
			<div class="Icon"><img src="js-i2b2/cells/PLUGINMGR/assets/DEFAULTLIST_icon_16x16.gif" alt="" /></div>
			<div class="txtBoundBox Name">Plugin Name</div>
			<div style="clear:both"></div>
		</a>
		<div class="PluginListBox">
			<div class="topmenu" oncontextmenu="return false">
				<form style="margin-top:1px;" action="javascript:void(0)">
					<div style="float:left;"><select id="anaPluginView" style="width:160px" onchange="i2b2.PLUGINMGR.view.list.Render()"><option value="DETAIL">Detailed List View</option><option value="SUMMARY">Summary List View</option></select></div>
					Category: <select id="anaPluginCats" style="width:200px" onchange="i2b2.PLUGINMGR.view.list.Render();"><option value="">Loading...</option></select>
				</form>
			</div>
			<div id="anaPluginList" oncontextmenu="return false"></div>
		</div>
		<div style="clear:both;"></div>
	</div>
<!-- ############### </PluginMgr List View> ############### -->

<!-- ############### <Plugin Viewer> ############### -->
	<div id="anaPluginViewBox" style="display:none">
		<div class="TopTabs">
			<div class="tabBox active"><div>Plugin Viewer</div></div>
			<div class="opXML">
<!--				<a href="JavaScript:showXML('PLUGINMGR','PlugView','Request');" class="debug"><img src="assets/images/msg_request.gif" border="0" width="16" height="16" alt="Show XML Request" title="Show XML Request" /></a> -->
<!--				<a href="JavaScript:showXML('PLUGINMGR','PlugView','Response');" class="debug"><img src="assets/images/msg_response.gif" border="0" width="16" height="16" alt="Show XML Response" title="Show XML Response" /></a> -->
				<a href="JavaScript:showXML('PLUGINMGR','PlugView','Stack');" class="debug"><img src="assets/images/msg_stack.gif" border="0" width="16" height="16"  alt="Show XML Message Stack" title="Show XML Message Stack" /></a>
				<a href="JavaScript:i2b2.PLUGINMGR.view.PlugView.showOptions();"><img src="assets/images/options.gif" border="0" width="16" height="16" alt="Show Options" title="Show Options" /></a>
				<a href="JavaScript:i2b2.PLUGINMGR.ctrlr.main.ZoomView();"><img id="plugviewZoomImg" width="16" height="16" border="0" src="js-i2b2/cells/PLUGINMGR/assets/zoom_icon.gif" alt="Toggle Zoom" title="Toggle Zoom" /></a>
			</div>
		</div>
		<div class="PluginViewBox">
			<div id="anaPluginViewFrame" oncontextmenu="return false">
				<div class="initialMsg">Select a plugin to load from the "Plugins" window.</div>
			</div>
			<iframe id="anaPluginIFRAME" src="assets/blank.html" style="display:none"></iframe>
		</div>
		<div style="clear:both;"></div>
	</div>
<!-- ############### </Plugin Viewer> ############### -->

	<div class="pageMask" id="itemOptionsMask" style="display:none" onclick="hidePopMenu();" onmousedown="hidePopMenu();"></div>
	<div class="pageMask" id="itemConstraintsMask" style="background-color: #000; filter:alpha(opacity=25); -moz-opacity:0.25;opacity: 0.25; display:none">&nbsp;</div>
	<div id="itemOptions" style="display:none"></div>
	<div id="itemConstraints" style=""></div>

<!-- ############### <Option Screens> ############### -->
	<div id="optionsQT" style="display:none;">
		<div class="hd" style="background:#6677AA;">Query Tool Options</div>
		<div class="bd">
			<center>
			<table style="font-size:12px">
<!--				<tr><td>Maximum Number of Children to Display:</td><td><input id="MaxChldDisp" style="width:35px" /></td></tr> -->
				<tr><td>Maximum Time to Wait for XML Response (in seconds):</td><td><input id="QryTimeout" style="width:35px" /></td></tr>
			</table>
			</center>
		</div>
	</div>

	<div id="optionsHistory" style="display:none;">
		<div class="hd" style="background:#6677AA;">Options for "Previous Queries" Window</div>
		<div class="bd">
			<center><br />
			<table style="font-size:12px">
				<tr><td>Maximum Number of Queries to Display:</td><td><input id="HISTMaxQryDisp" style="width:35px" /></td></tr>
				<tr><td colspan="2"><br />Sort Queries</td></tr><tr><td colspan="2" class="dateBorder" align="center"><table id="HISToptSortBox" style="font-size:12px; text-align:left;">
				<tr><td><input type="radio" name="HISTsortBy" id="HISTsortByNAME" value="NAME" checked="checked" /> By Name</td></tr><tr><td><input type="radio" name="HISTsortBy" id="HISTsortByDATE" value="DATE" /> By Create Date</td></tr>
				<tr><td colspan="2"><hr width="75%" /></td></tr>
				<tr><td><input type="radio" name="HISTsortOrder" id="HISTsortOrderASC" value="ASC" checked="checked" /> Ascending</td><td><input type="radio" name="HISTsortOrder" id="HISTsortOrderDESC" value="DESC" /> Descending</td>
				</tr></table></td></tr>
			</table>
			</center>
		</div>
	</div>

	<div id="optionsOntNav" style="display:none;">
		<div class="hd" style="background:#6677AA;">Options for Navigating Terms</div>
		<div class="bd">
				<br />
				<div style="font-size:12px; margin-left:50px" >Maximum Number of Children to Display: <input id="ONTNAVMaxQryDisp" style="width:35px" value="200" /></div>
				<div style="margin-left:50px"><input type="checkbox" id="ONTNAVshowHiddens" /> Show Hidden Terms</div>
				<div style="margin-left:50px"><input type="checkbox" id="ONTNAVshowSynonyms" /> Show Synonymous Terms</div>
		</div>
	</div>
	<div id="optionsOntFind" style="display:none;">
		<div class="hd" style="background:#6677AA;">Options for Finding Terms</div>
		<div class="bd">
				<br />
				<div style="font-size:12px; margin-left:50px" >Maximum Number of Children to Display: <input id="ONTFINDMaxQryDisp" style="width:35px" value="400" /></div>
				<div style="margin-left:50px"><input type="checkbox" id="ONTFINDshowHiddens" /> Show Hidden Terms</div>
				<div style="margin-left:50px"><input type="checkbox" id="ONTFINDshowSynonyms" /> Show Synonymous Terms</div>
		</div>
	</div>
<!-- ############### </Option Screens> ############### -->
	<div id="calendarDiv" style="z-index:1520; display:none;"></div>
	<!-- DO NOT MOVE calendarDivMask IE 5/6/7 has major z-index bug -->
	<div id="calendarDivMask" style="display:none; z-index:1510; position:absolute; background-image:url('null.gif')" onclick="i2b2.CRC.ctrlr.dateConstraint.hideCalendar()"></div>
	<div id="constraintDates" style="display:none;">
		<div class="hd" style="background:#6677AA;">Constrain Group by Date Range</div>
		<div class="bd">
			<br />
			<center>
			<table style="font-size:12px">
				<tr><td>From:</td><td></td><td>To:</td></tr>
				<tr>
					<td class="dateBorder">
						<table><tr>
							<td valign="middle"><input id="checkboxDateStart" type="checkbox" onchange="i2b2.CRC.ctrlr.dateConstraint.toggleDate()" /></td>
							<td valign="middle"><input id="constraintDateStart" value="01/31/2008" style="width:75px;" disabled="disabled" /></td>
							<td valign="middle"><a href="Javascript:i2b2.CRC.ctrlr.dateConstraint.doShowCalendar('S')"><img id="dropDateStart" style="position:relative; top:1px; border:none;" class="calendarDropdown" src="assets/images/b_dropdown.gif" alt="" /></a>&nbsp;</td>
						</tr></table>
					</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td class="dateBorder">
						<table>
						<tr>
							<td valign="middle"><input id="checkboxDateEnd" type="checkbox" onchange="i2b2.CRC.ctrlr.dateConstraint.toggleDate()" /></td>
							<td valign="middle"><input id="constraintDateEnd" value="12/31/2008" style="width:75px;" disabled="disabled" /></td>
							<td valign="middle"><a href="Javascript:i2b2.CRC.ctrlr.dateConstraint.doShowCalendar('E');"><img id="dropDateEnd" style="position:relative; top:1px" class="calendarDropdown" border="0" src="assets/images/b_dropdown.gif" alt=""/></a>&nbsp;</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
			</center>
			<br /><br />
		</div>
	</div>


	<div id="constraintOccurs" style="display:none;">
		<div class="hd" style="background:#6677AA;">Constrain Group by Number of Occurrences</div>
		<div class="bd">
			<br />Event(s) within the group occur more than
			<select style="width: 46px;" id="constraintOccursInput" name="constraintOccursInput">
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
			</select> times.
		</div>
	</div>



<!-- ############### <LabRange> ############### -->
	<div id="itemLabRange" style="display:none;">
		<div class="hd" style="background:#6677AA;">Lab Range Constraint</div>
		<div class="bd modLabValues">
			<div style="margin: 0px 5% 12px; text-align: center;">Searches by Lab values can be constrained by the high/low flag set by the performing laboratory, or by the values themselves.</div>
			<div class="mlvBody">
				<div class="mlvtop">
					<div class="mlvModesGroup">
						<div class="mlvMode"><input name="mlvfrmType" id="mlvfrmTypeNONE" value="NO_VALUE" type="radio" checked="checked" /> No value [<a href="JavaScript:alert('Picking no value for the test value will select all of the tests irrespective of its associated value.');" style="text-decoration:none;color:#000;">?</a>]</div>
						<div class="mlvMode"><input name="mlvfrmType" id="mlvfrmTypeFLAG" value="BY_FLAG" type="radio" /> By flag [<a href="JavaScript:alert('Picking a value flag will select those tests determined to be abnormal by the laboratory\nthat performed the test. This has an advantage over selecting by numeric comparisons\nbecause differences in calibration and errors in units are eliminated. The disadvantage\nto numeric comparisons is that the laboratory may not be reliable in setting this flag.');" style="text-decoration:none;color:#000;">?</a>]</div>
						<div class="mlvMode"><input name="mlvfrmType" id="mlvfrmTypeVALUE" value="BY_VALUE" type="radio" /> By value [<a href="JavaScript:alert('Picking a numeric value will select those tests that, according to an operator, will be\nabove, below, or between certain values. This gives fine control over the value that\nthe test result must have in order for it to be selected. The disadvantage of using\nnumeric comparisons is that differences in calibration and errors in units can create\nerroneous results.');" style="text-decoration:none;color:#000;">?</a>]</div>
					</div>
					<div class="mlvInputGroup">
						<div id="mlvfrmFLAG" style="display:none">
							Please select a range:<br />
							<select id='mlvfrmFlagValue'><option value="">Loading...</option></select>
						</div>
						<div id="mlvfrmVALUE" style="display:none">
							<p id="mlvfrmEnterOperator">
								Please select operator:<br />
								<select id='mlvfrmOperator'>
									<option value="LT">LESS THAN (&lt;)</option>
									<option value="LE">LESS THAN OR EQUAL TO (&lt;=)</option>
									<option value="EQ">EQUAL (=)</option>
									<option value="BETWEEN">BETWEEN</option>
									<option value="GT">GREATER THAN (&gt;)</option>
									<option value="GE">GREATER THAN OR EQUAL (&gt;=)</option>
								</select>
							</p>
							<p id="mlvfrmEnterVal">
								Please enter a value:<br />
								<input id="mlvfrmNumericValue" class="numInput" />
							</p>
								<p id="mlvfrmEnterVals" style="display:none">Please enter values:<br />
								<input id="mlvfrmNumericValueLow" class="numInput" /> &nbsp;-&nbsp; <input id="mlvfrmNumericValueHigh" class="numInput" />
							</p>

							<p id="mlvfrmEnterStr">Please enter a value:<br /><input id="mlvfrmStrValue" class="strInput" /> </p>

							<p id="mlvfrmEnterEnum">Please select a value:<br />
								<select id="mlvfrmEnumValue" class="enumInput" multiple="multiple" size="5" style="overflow: scroll; width: 562px;">
								<option value="">Loading...</option>
								</select>
							</p>
						</div>
					</div>

					<div style="clear:both;height:1px;overflow:hidden;"></div>

					<div id="mlvfrmUnitsContainer" style="margin: 10px 0px 0px 15px;">
						Units =
						<select id='mlvfrmUnits' class="units"><option value="0">Loading...</option></select>
						<span id="mlvUnitExcluded" style="color:#900; margin-left: 20px">A value cannot be specified for these units.</span>
					</div>
				</div>
				<div id="mlvfrmBarContainer" class="barContainer" style="white-space:nowrap; display:none">
					<table id="mlvfrmBarVALUES" class="bar" width="100%" cellspacing="0">
						<tr>
							<td colspan="5" align="center">Range in <span id="mlvfrmLblUnits" style="font-decoration:italic bold">mm/Hg</span></td>
						</tr>
						<tr>
							<td class="barfrag" style="background:#F00; width:10%; border-left: 1px solid #000"><a href="javascript:void(0);" title="Low Toxic" class="barlink">&nbsp;</a></td>
							<td class="barfrag" style="background:#FFA500; width:20%"><a href="javascript:void(0);" title="Low Abnormal" class="barlink">&nbsp;</a></td>
							<td class="barfrag" style="background:#080; width:40%"><a href="javascript:void(0);" title="Normal" class="barlink">&nbsp;</a></td>
							<td class="barfrag" style="background:#FFA500; width:20%" ><a href="javascript:void(0);" title="High Abnormal" class="barlink">&nbsp;</a></td>
							<td class="barfrag" style="background:#F00; width:10%"><a href="javascript:void(0);" title="High Toxic" class="barlink">&nbsp;</a></td>
						</tr>
						<tr>
							<td><div class="barlabel" id="mlvfrmLblLowToxic">LowTx</div></td>
							<td><div class="barlabel" id="mlvfrmLblHighOfLow">HofL</div></td>
							<td><div class="barlabel" id="mlvfrmLblLowOfHigh">LofH</div></td>
							<td><div class="barlabel" id="mlvfrmLblHighToxic">HighTx</div></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
<!-- ############### </LabRange> ############### -->


<!-- ############### <Query Rename Dialog> ############### -->
	<div id="dialogQmName" style="display:none;">
		<div class="hd" style="background:#6677AA;">Query Name</div>
		<div class="bd">
			<br />
			<div style="font-size:12px; margin-left:40px" >Please type a name for the query:</div>
			<div style="margin-left:40px"><input id="inputQueryName" style="width:335px" /></div>
		</div>
	</div>
<!-- ############### </Query Rename Dialog> ############### -->

<!-- ############### <Query Run Dialog> ############### -->
	<div id="dialogQryRun" style="display:none;">
		<div class="hd" style="background:#6677AA;">Run Query</div>
		<div class="bd">
			<br />
			<div style="font-size:12px; margin-left:40px" >Please type a name for the query:</div>
			<div style="margin-left:40px"><input class="inputQueryName" style="width: 505px" /></div>
			<div style="height:25px;"></div>
			<div style="font-size:12px; margin-left:40px" >Please check the query result type(s):</div>
			<div style="border: 1px solid rgb(171, 173, 179); margin-left: 40px; width: 505px; padding: 4px">

                <div class="further_text"><input id="countQuery" type="radio" class="chkQueryType" name="queryType" value="count_query" checked="checked" onclick="i2b2.CRC.ctrlr.QT.toggleQueryType()"/>Submit Count Only Query to OpenFurther?</div>
                <div style="margin-left: 15px; padding: 5px;">Results common to multiple sources won't be aggregated and will be counted more than once. Analysis tools can NOT be used with results.</div>
                <div id="countDatasources" style="padding: 2px">
						<?php
						if ($ini['demo']) {
							echo 
							'<div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="countQueryType" value="OMOPv2">Schultz Cancer Repository (OMOP data source)</div>
	                        <div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="countQueryType" value="OpenMRS-V1_9">Schultz Healthcare Systems (OpenMRS data source)</div>';
						} else {
							echo 
							'<div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="countQueryType" value="count_ds_edw">University of Utah Enterprise Data Warehouse</div>
	                        <div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="countQueryType" value="count_ds_updb">Utah Population Database Limited</div>';
						}
						?>

                </div>
                
                <div class="further_text"><input id="dataQuery" type="radio" class="chkQueryType" name="queryType" value="data_query" onclick="i2b2.CRC.ctrlr.QT.toggleQueryType()"/>Submit Data Query to OpenFurther ?</div>
                <div style="margin-left: 15px; padding: 5px;"><span class="further_text_highlight">Results common to multiple sources will be displayed and analysis tools can be used with results. This also allows you to export the data set later.</div>
                
                <div id="dataDatasources" style="display:none; padding: 2px;">
						<?php
						if ($ini['demo']) {
							echo 
							'<div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="dataQueryType" value="OMOPv2">Schultz Cancer Repository (OMOP data source)</div>
	                        <div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="dataQueryType" value="OpenMRS-V1_9">Schultz Healthcare Systems (OpenMRS data source)</div>';
						} else {
							echo 
							'<div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="dataQueryType" value="data_ds_edw">University of Utah Enterprise Data Warehouse</div>
			                <div class="further_datasource_name" style="margin-left:10px"><input type="checkbox" class="chkQueryType" name="dataQueryType" value="data_ds_updb">Utah Population Database Limited</div>';
						}
						?>
			<br/>   
					<div><strong>Data Categories</strong></div>             
                	<div id="dataCategories" style="margin-left: 15px; padding: 5px;">Please check the data categories you want to be returned for use with the analysis tools <span class="further_text_highlight">(Only Demographics are supported for now)</span></div>
                	<div class="further_data_category" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="demographic">Demographics <font color="black">(Age, Religion, Race, Gender, Language, Marital Status, State, County)</font></div>
                	<div class="further_data_category_disabled" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="condition" disabled>Condition</div>
                	<div class="further_data_category_disabled" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="procedure" disabled>Procedure</div>
                	<div class="further_data_category_disabled" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="labObs" disabled>Lab Observation</div>
                	<div class="further_data_category_disabled" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="drug" disabled>Drug</div>
                	<div class="further_data_category_disabled" style="margin-left:10px"><input type="checkbox" class="chkDataCat" name="dataCat" value="provider" disabled>Provider</div>
                </div>
			</div>
		</div>
	</div>
<!-- ############### </Query Run Dialog> ############### -->

<!-- ############### <Information Dialog> ############### -->
	<div id="dialogOntInfo" style="display:none;">
		<div class="hd">Concept Information</div>
		<div class="bd" id="dialogOntInfo-viewer-body" class="help-viewer-body">
		<p>Loading</p>
		</div>
	</div>
	
<!-- ############### </Information Dialog> ############### -->



</div>
</body>
</html>
