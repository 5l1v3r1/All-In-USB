<?php

/* ********************************************************************\
|                                                                      |
|   Copyright (c) 2011 Thimbleweed Consulting. All Rights Reserved     |
|                                                                      |
|                  This file is part of All-In-USB.                    |
|                                                                      |
| All-In-USB is free software: you can redistribute it and/or modify   |
| it under the terms of the GNU General Public License as published    |
| by the Free Software Foundation, either version 3 of the License,    |
| or (at your option) any later version.                               |
|                                                                      |
| All-In-USB is distributed in the hope that it will be useful, but    |
| WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANT- |
| ABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General     |
| Public License for more details.                                     |
|                                                                      |
| You should have received a copy of the GNU General Public License    |
| along with All-In-USB. If not, see <http://www.gnu.org/licenses/>    |
|                                                                      |
\******************************************************************** */

// ############################################################################
// # Define some base variables
// ############################################################################

include "functions.php";
$Root = getRoot();
if(!is_array($_REQUEST["executable"])) { $_REQUEST["executable"] = array(); }

// ############################################################################
// # If "Saving" Remove the Files
// ############################################################################

if($_REQUEST["action"] == "delete" && count($_REQUEST["executable"]))
	{
	foreach($_REQUEST["executable"] AS $exe)
		{
		@unlink($Root."\\utilities\\".$exe);
		if(!$_REQUEST["keeptwc"]) { @unlink($Root."\\utilities\\".$exe.".twc"); }
		}
	}

// ############################################################################
// # Initial Parse of Capture Tools
// ############################################################################

foreach(glob($Root."\\utilities\\*.*") AS $File)
	{
	if(substr($File,-4) != ".twc")
		{
		unset($Name);
		$tFile = str_replace($Root."\\utilities\\","",$File);
		if(in_array($File.".twc",$Files))
			{
			$tCap = parse_ini_file($File.".twc",true);
			$tCap = $tCap["capture"];
			$Missing = false;
			foreach($Fields AS $Field => $Params) { if($Params["required"] && !$tCap[$Field]) { $Missing = true; } }
			$Stat = $Missing ? 1 : 2;
			$Tabs[$tCap["tab"]] = 1;
			$Name = trim(trim(trim($tFile." - ".$tCap["name"]),"-"));
			}
		else
			{ $Stat = 0; }
		$Captures[$tFile]["stat"] =  $Stat;
		$Captures[$tFile]["name"] =  $Name ? $Name : $tFile;
		}
	}

?><html>
<head>
<title>Edit Configuration Files</title>
<link type="text/css" href="css/<?php echo $config["jQuery"]["ui"]; ?>/jquery-ui-<?php echo $config["jQuery"]["ui_ver"]; ?>.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-<?php echo $config["jQuery"]["jq_ver"]; ?>.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-<?php echo $config["jQuery"]["ui_ver"]; ?>.min.js"></script>
<script>

$(function() {
	$(".refresh").button( { icons: { primary: "ui-icon-refresh" } });
	$(".tool_add").button( { icons: { primary: "ui-icon-plus" } });
	$(".tool_cnf").button( { icons: { primary: "ui-icon-link" } });
	$(".tool_del").button( { icons: { primary: "ui-icon-close" } });
	});

</script>
<style type="text/css">

BODY { font-size: 70%; }
BUTTON { width: 250px; }
FORM { padding: 0px; margin: 0px; }

</style>
</head>
<body>

<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="width: 510px; padding: 10px; margin-left: auto; margin-right: auto;">
	<form method="post" action="<?php echo basename(__FILE__); ?>">
		<input type="hidden" name="action" value="delete">
		<table border="0" cellspacing="5" cellpadding="0" style="margin: 0px">
			<tr><th colspan="2"><span style="color: red;">CANNOT</span> be undone!</th></tr>
			<tr>
				<td colspan="2">
					<select name="executable[]" size="20" style="width: 500px" multiple="multiple">
						<toption value="New">Add New Capture Tool</toption>
						<?php foreach($Captures AS $Capture => $Params) { ?>
							<option value="<?php echo $Capture; ?>" <?php echo in_array($Capture,$_REQUEST["executable"]) ? 'selected="selected"' : ''; ?>>
								<?php
								echo $Params["name"];
								?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="50%"><button class="tool_del" type="submit">Permanently Remove</button></td>
				<td width="50%">
					<label for="keeptwc">
						<input type="checkbox" name="keeptwc" id="keeptwc" value="1">
						Retain Configuration File
					</label>
				</td>
			</tr>
			<tr><td align="center" colspan="2"><small>Hold down Ctrl to select multiple files.</small></td></tr>
		</table>
	</form>
</div>

</body>
</html>