<?php

	function presentInArray($toSearch, $arr)
	{
		$flag = false;
		if(is_array($arr))
			foreach($arr as $key => $value)
				if($arr[$key]['FormId'] == $toSearch)
				{
					$flag = true;
					break;
				}
		return $flag;
	}

	function giveCatDirName($categoryName)
	{
		$dirName = "";
		switch($categoryName)
		{
			case "Residential":
				$dirName = "residential";
			break;			
			case "Townships":
				$dirName = "townships";
			break;			
			case "Commercial":
				$dirName = "commercial";
			break;			
			case "Special Economic Zone":
				$dirName = "special-economic-zone";
			break;			
			case "Engineering and Procurement Construction":
				$dirName = "engineering-procurement-construction";
			break;			
			case "Low Cost Housing":
				$dirName = "low-cost-housing";
			break;			
		}
		return $dirName;
	}

	function giveLeftNavi($categoryName)
	{
		$dirName = "";
		switch($categoryName)
		{
			case "Residential":
				$dirName = "Residential";
			break;			
			case "Townships":
				$dirName = "Townships";
			break;			
			case "Commercial":
				$dirName = "Commercial";
			break;			
			case "Special Economic Zone":
				$dirName = "SEZ";
			break;			
			case "Engineering and Procurement Construction":
				$dirName = "EPC";
			break;			
			case "Low Cost Housing":
				$dirName = "LCH";
			break;			
		}
		return $dirName;
	}
	
	function configureOngoingProject($projectId)
	{
		$resData = mysql_query("SELECT * FROM tbl_ongoing_projects WHERE Id = ".$projectId);
		$objData = mysql_fetch_object($resData);
		if($objData -> DisplayStatus == "Publish")
			createOngoingProject($_POST['Id']);
		else if($objData -> DisplayStatus == "Draft")
			deleteOngoingProject($_POST['Id']);	
	}
	
	function deleteOngoingProject($projectId)
	{
		$objProject=new GenericClass("tbl_ongoing_projects");
		$arrData = $objProject -> getData(" Id = ".$projectId);
		$dirName = "../ongoing-projects/".giveCatDirName($arrData[0]['SubCategory'])."/".removeSpaceByDash($arrData[0]['Name']);
		if(file_exists($dirName))
		{
			unlink($dirName."/index.php");
			unlink($dirName);
		}
	}

	function createOngoingProject($projectId)
	{
		$objProject=new GenericClass("tbl_ongoing_projects");
		$arrData = $objProject -> getData(" Id = ".$projectId);
		$dirName = "../ongoing-projects/".giveCatDirName($arrData[0]['SubCategory'])."/".removeSpaceByDash($arrData[0]['Name']);
		if(!file_exists($dirName))
			mkdir($dirName);
		$fileContents = file_get_contents("../ongoingTemplate.php"); 
		$fileContents=str_replace('$LEFTNAVIGATE', "$".giveLeftNavi($arrData[0]['SubCategory'])."= \"style='color:#0B1593;'\";",$fileContents);
		$fileContents=str_replace('$DIRNAME', "../../".giveCatDirName($arrData[0]['SubCategory'])."/",$fileContents);
		$fileContents=str_replace('$SUBCATEGORY', $arrData[0]['SubCategory'],$fileContents);
		$fileContents=str_replace('$PROJECTNAME', $arrData[0]['Name'],$fileContents);
		$fileContents=str_replace('$LOCATION', $arrData[0]['Location'],$fileContents);
		$fileContents=str_replace('$AMENITIES', $arrData[0]['Amenities'],$fileContents);
		$fileContents=str_replace('$ABOUTPROJECT', $arrData[0]['AboutProject'],$fileContents);
		$fileContents=str_replace('$SPECIFICATIONS', $arrData[0]['Specifications'],$fileContents);
		$fileContents=str_replace('$FEATURES', $arrData[0]['Features'],$fileContents);
		$fileContents=str_replace('$LAYOUTPLANS', $arrData[0]['LayoutPlans'],$fileContents);
		$fileContents=str_replace('$VIRTUALVISIT', $arrData[0]['VirtualVisit'],$fileContents);
		$fileContents=str_replace('$PHOTOGALLERY', $arrData[0]['PhotoGallery'],$fileContents);
		$fileContents=str_replace('$VISITUS', $arrData[0]['VisitUs'],$fileContents);
		
		$fp = fopen($dirName."/index.php", "w");
		fwrite($fp, $fileContents);
		fclose($fp);
	}


	function removeSpaceByDash($teamName)
	{
		$teamName=ucwords($teamName);
		$str="";
		for($i=0;$i<strlen($teamName);$i++)
			if($teamName[$i] != " ")
				$str.=$teamName[$i];
			else
				$str.="-";
		return $str;
	}

	function upload_doc($doc_arr, $target_path)
	{
		if($doc_arr['error']==0)
		{
			$file_ext=explode(".",$doc_arr['name']);
			$filename=rand().time().".".$file_ext[1]; 
		 
		 //echo $target_path; echo "<br>";
		/*if (is_writable($target_path)) {
			echo "directory is writable"; 
		}
		else {
			echo 'Upload directory is not writable';
		}
		if(file_exists($target_path)) {
			echo "directory is exits"; 
		}
		else {
			echo 'Upload directory is not exits';
		}
		 exit;*/
		 
			if(!move_uploaded_file($doc_arr['tmp_name'],$target_path.$filename)) echo "Error";
			return $target_path.$filename;
		}
		else
			return false;
	}


	function putContext()
	{
		foreach($_POST as $key => $value)
			if($key != "submit" and $key != "Submit")
			echo "<input type='hidden' name='".$key."' value='".$value."' />";
	}

	function forgotPassword($tablename,$email_field, $email)
	{
		$resForgot=mysql_query("SELECT * FROM ".$tablename." WHERE ".$email_field."='".$email."'");
		if(mysql_num_rows($resForgot) <= 0)
			return false;
		else
		{
			$objForgot=mysql_fetch_object($resForgot);
			if($objForgot->$email_field != $email)
				return false;
			else
				return $objForgot;
		}
	}
	
	function authenticate($username, $password, $tablename, $username_field, $password_field)
	{
		$res_auth=mysql_query("SELECT * FROM ".$tablename." WHERE ".$username_field."='".$username."' AND ".$password_field."='".$password."' AND DisplayStatus = 'Active' ");
		if(mysql_num_rows($res_auth) <= 0)
			return false;
		else
		{	
			$obj_auth=mysql_fetch_object($res_auth);
			if($obj_auth->$username_field != $username or $obj_auth->$password_field != $password)
				return false;
			else
			{ 
				mysql_query("UPDATE ".$tablename." SET LastAccessTime='".date("Y-m-d H:i:s")."'");
				$res_auth=mysql_query("SELECT * FROM ".$tablename." WHERE ".$username_field."='".$username."' AND ".$password_field."='".$password."'");
				$obj_auth=mysql_fetch_object($res_auth);
				return $obj_auth;
			}
		}
	}
	
	function filterPostArray($array)
	{
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				$array[$key]=addslashes($value);
				$array[$key]=htmlspecialchars($value);
			}
			return $array;
		}
		else
			return false;
	}
	
	function removeSlashesPostArray($array)
	{
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				$array[$key]=stripslashes($value);
//				$array[$key]=htmlspecialchars($value);
			}
			return $array;
		}
		else
			return false;
	}

	function changePassword($tableName, $usernameField, $passwordField, $userName, $oldPassword, $newPassword, $confirmPassword)
	{
		$resCheck=mysql_query("SELECT * FROM  ".$tableName." WHERE ".$usernameField." = '".$userName."' AND ".$passwordField."='".$oldPassword."'");
		if($newPassword != $confirmPassword)
			return "conflit";
		else if(mysql_num_rows($resCheck) == 0)
			return "wrong";
		else
		{
			mysql_query("UPDATE ".$tableName." SET ".$passwordField." = '".$newPassword."' WHERE ".$usernameField." = '".$userName."'");
			return "success";
		}
	}

	function convertDBDate($dt)
	{
		if($dt!='' and $dt!='0000-00-00')
		{
			$arrDt=explode("-",$dt);
			$time=date("j F Y",mktime(0,0,0,$arrDt[1],$arrDt[2],$arrDt[0]));
			return $time;
		}
		else return "";
	}


	function convertDBDateTime($dt)
	{
		if($dt!='' and $dt!='0000-00-00 00:00:00')
		{
			$arrDt=explode(" ",$dt);
			$Dt=explode("-",$arrDt[0]);
			$Tm=explode(":",$arrDt[1]);
			$time=date("j F Y, h:i A",mktime($Tm[0],$Tm[1],$Tm[2],$Dt[1],$Dt[2],$Dt[0]));
			return $time;
		}
		else
			return false;
	}
		
	function giveMeCities($var)
	{
		$resData=mysql_query("SELECT * FROM cities ORDER BY City");
		while($objData=mysql_fetch_object($resData))
		{
			if($objData->Id==$var)
				$slt=" selected ";
			else
				$slt=" ";
			echo "<option value='".$objData->Id."' ".$slt.">".ucwords($objData->City)."</option>";
		}
	}


	function set_management($table_name, $cond, $default_column, $default_order, $paginateFor)
	{
		global $page_size, $page_no, $qry, $res, $num, $pagination, $RecordsPerPage, $mypage, $srno, $pages, $start, $sortbyColumn, $sortbyOrder;
		global $club_number;
		
		$sortbyColumn=$default_column;
		$sortbyOrder=$default_order;
	
		if($_REQUEST['sortColumn'] != '' and $_REQUEST['sortOrder'] != '')
		{
			$sortbyColumn=($_REQUEST['sortColumn']);
			$sortbyOrder =($_REQUEST['sortOrder']);
		}
		
		?>
	<script language="javascript">
	function navigationrec(pageno,frm)
	{
		frm.pages.value=pageno;
		frm.submit();
	}
	
	function sortFunction(form,cname,corder)
	{
		form.sortColumn.value=cname;
		form.sortOrder.value=corder;
		form.submit();
	}
	
	</script>
		<?php
		$qry=" select * from ".$table_name." ".$cond;
		$res=mysql_query($qry) or die(mysql_error());
		$num=mysql_num_rows($res);
		
		$pagination=$paginateFor;
		$RecordsPerPage		=  	$pagination;
		$page_size			= 	$RecordsPerPage ;	
		
		if($num == 0)
			$pages=1;
		else
			$pages=ceil($num/$page_size);
		
		
		if($_REQUEST['pages']=="")
		$page_no	=	1;
		else
		{
			$page_no = $_REQUEST['pages'];	
			if ($page_no>$pages)
			$page_no=$pages;
			if ($page_no<1)
			$page_no=1;	
		}
		
		if($num > 0 )
		{
			$mypage=$page_size*($page_no-1);
		}
		if($mypage > 0)
		$srno=$mypage + 1;
		else $srno=1;	
		$start=$page_size*($page_no-1) .  ", " . $page_size;
	} 
	
	function put_hidden()
	{
		global $sortbyColumn, $sortbyOrder, $srno, $cnt, $page_no;
	?>
		<input type="hidden" name="sortColumn" value="<?=$sortbyColumn?>">
		<input type="hidden" name="sortOrder" value="<?=$sortbyOrder?>">
		<input type="hidden" name="txtcountin" value="<?=$srno?>">
		<input type="hidden" name="hiddencnt" value="<?=$cnt?>">
		<input type="hidden" name="pages" value="<?=$page_no?>">
	<?php
	}
	
	function put_sorting($column_name, $display_name)
	{
	global $sortbyColumn, $sortbyOrder; 
	
	if($_REQUEST['sortOrder']=='')
		$order="asc";
	else if($_REQUEST['sortOrder']=='asc')
		$order="desc";
	else if($_REQUEST['sortOrder']=='desc')
		$order="asc";
	
	?>
	 onClick="sortFunction(document.forms[0],'<?php echo $column_name; ?>','<?php echo $order; ?>')" >
	 <span style="text-decoration:underline; cursor:pointer" ><?php echo $display_name; ?></span>&nbsp; <?php 			      
		if($_REQUEST['sortColumn'] == $column_name && $_REQUEST['sortOrder'] == 'asc')
			echo "<img src='./images/asc_order.gif' align='middle' style='cursor:pointer'>";
		else if($_REQUEST['sortColumn'] == $column_name && $_REQUEST['sortOrder'] == 'desc')
			echo "<img src='./images/desc_order.gif' align='middle' style='cursor:pointer'>";
	}

	function giveMeTeams($selectedTeam)
	{
		$resData=mysql_query("SELECT Id, TeamName FROM teamprofile ORDER BY TeamName");
		while($objData=mysql_fetch_object($resData))
		{
			if($selectedTeam == $objData->Id)
				$slt=" selected ";
			else
				$slt="";
			echo "<option value='".$objData->Id."' ".$slt.">".$objData->TeamName."</option>";
		}
	}
function putSiteCodes($categoryName, $selectedValue)
{
	$resCat = mysql_query("SELECT * FROM tbl_sitecodes WHERE Category = '".$categoryName."' ORDER BY Value");
	while($objCat = mysql_fetch_object($resCat))
	{
		if($objCat->Key == $selectedValue)
			$select = " selected ";
		else
			$select = "";
		echo "<option value='".$objCat->Key."' ".$select." >".$objCat->Value."</option>";
	}
}

function returnMultiplePullCodes($pullFormName, $pullFieldName, $arrData)
{
	$returnCode = "";
	$resForm = mysql_query("SELECT * FROM view_formfields WHERE FormName = '".$pullFormName."' AND FieldName = '".$pullFieldName."'");
	$objForm = mysql_fetch_object($resForm);	
	$resData = mysql_query("SELECT ".$objForm -> FieldName." FROM ".$objForm -> TableName." ORDER BY ".$objForm -> FieldName);
	while($arrFields = mysql_fetch_assoc($resData))	
	{
		if($arrFields[$objForm -> FieldName] == $arrData)
			$select = " selected ";
		else
			$select = " ";
		$returnCode .= "<option value='".$arrFields[$objForm -> FieldName]."' ".$select." >".$arrFields[$objForm -> FieldName]."</option>";	
	}
	return $returnCode;
}


function returnPullCodes($pullFormName, $pullFieldName, $arrData)
{
	$returnCode = "";
	$resForm = mysql_query("SELECT * FROM view_formfields WHERE FormName = '".$pullFormName."' AND FieldName = '".$pullFieldName."'");
	$objForm = mysql_fetch_object($resForm);	
	$resData = mysql_query("SELECT * FROM ".$objForm -> TableName." ORDER BY ".$objForm -> FieldName);
	while($arrFields = mysql_fetch_assoc($resData))	
	{
		if($arrFields['Id'] == $arrData)
			$select = " selected ";
		else
			$select = " ";
		$returnCode .= "<option value='".$arrFields['Id']."' ".$select." >".$arrFields[$objForm -> FieldName]."</option>";	
	}
	return $returnCode;
}

function returnSiteCodes($categoryName, $selectedValue)
{
	$returnCode = "";
	$resCat = mysql_query("SELECT * FROM tbl_sitecodes WHERE Category = '".$categoryName."' ORDER BY Value");
	while($objCat = mysql_fetch_object($resCat))
	{
		if($objCat->Key == $selectedValue)
			$select = " selected ";
		else
			$select = "";
		$returnCode .= "<option value='".$objCat->Key."' ".$select." >".$objCat->Value."</option>";
	}
	return $returnCode;
}

function returnMultipleSiteCodes($categoryName, $selectedValue)
{
	$returnCode = "";
	$resCat = mysql_query("SELECT * FROM tbl_sitecodes WHERE Category = '".$categoryName."' ORDER BY Value");
	while($objCat = mysql_fetch_object($resCat))
		$returnCode .= "<option value='".$objCat->Key."' ".$select." >".$objCat->Value."</option>";
	return $returnCode;
}


function returnRadioSiteCodes($ctrlName, $categoryName, $selectedValue)
{
	$returnCode = "";
	$resCat = mysql_query("SELECT * FROM tbl_sitecodes WHERE Category = '".$categoryName."' ORDER BY Value");
	while($objCat = mysql_fetch_object($resCat))
	{
		if($objCat->Key == $selectedValue)
			$checked = " checked ";
		else
			$checked = "";
		$returnCode .= "<input type='radio' name='".$ctrlName."' id='".$ctrlName."' value='".$objCat->Key."' ".$checked." /> ".$objCat->Value;
	}
	return $returnCode;
}

function filterFormFields($arrData, $searchCode)
{
	$retArr = array();
	$i=0;
	foreach($arrData as $key => $value)
		if($arrData[$key][$searchCode] == "Yes")
			$retArr[$i++] = $arrData[$key];
	return $retArr;
}

function filterMultiSelect($arrData)
{
	$retArr = array();
	$i=0;
	foreach($arrData as $key => $value)
		if($arrData[$key]["FieldControlType"] == "Multi-Select Drop Down")
			$retArr[$i++] = $arrData[$key];
	return $retArr;
}

function filterFileBrowse($arrData)
{
	$retArr = array();
	$i=0;
	foreach($arrData as $key => $value)
		if($arrData[$key]["FieldControlType"] == "File Browse")
			$retArr[$i++] = $arrData[$key];
	return $retArr;
}

function putValidations($arrFieldData)
{
	$validations = "";
	if($arrFieldData['vldBrowse'] != "")
		$validations .= $arrFieldData['vldBrowse'].",";
	if($arrFieldData['vldSelect'] != "")
		$validations .= $arrFieldData['vldSelect'].",";
	if($arrFieldData['vldRadio'] != "")
		$validations .= $arrFieldData['vldRadio'].",";
	if($arrFieldData['vldBlank'] != "")
		$validations .= $arrFieldData['vldBlank'].",";
	if($arrFieldData['vldPreSpace'] != "")
		$validations .= $arrFieldData['vldPreSpace'].",";
	if($arrFieldData['vldPostSpace'] != "")
		$validations .= $arrFieldData['vldPostSpace'].",";
	if($arrFieldData['vldEmail'] != "")
		$validations .= $arrFieldData['vldEmail'].",";
	if($arrFieldData['vldPan'] != "")
		$validations .= $arrFieldData['vldPan'].",";
	if($arrFieldData['vldPin'] != "")
		$validations .= $arrFieldData['vldPin'].",";
	if($arrFieldData['vldPhone'] != "")
		$validations .= $arrFieldData['vldPhone'].",";
	if($arrFieldData['vldMobile'] != "")
		$validations .= $arrFieldData['vldMobile'].",";
	if($arrFieldData['vldAlpha'] != "")
		$validations .= $arrFieldData['vldAlpha'].",";
	if($arrFieldData['vldAlphaNum'] != "")
		$validations .= $arrFieldData['vldAlphaNum'].",";
	if($arrFieldData['vldInt'] != "")
		$validations .= $arrFieldData['vldInt'].",";
	if($arrFieldData['vldFloat'] != "")
		$validations .= $arrFieldData['vldFloat'].",";
	return substr($validations,0,strlen($validations)-1);
}

function putFormControl($arrFieldData, $arrData)
{
	$formControl = "";
	switch($arrFieldData['FieldControlType'])
	{
		case "Text Field":
			$vldFlag = false;
			$formControl = "<input type='text' name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."' value='".$arrData[$arrFieldData['FieldName']]."'";
			if($arrFieldData['vldPin'] != "")
			{
				$vldFlag = true;
				$formControl .= " size='10' maxlength='10' ";
			}
			if($arrFieldData['vldPhone'] != "")
			{
				$vldFlag = true;
				$formControl .= " size='20' maxlength='20' ";
			}
			if($arrFieldData['vldMobile'] != "")
			{
				$vldFlag = true;
				$formControl .= " size='20' maxlength='20' ";
			}
			if($arrFieldData['vldPin'] != "")
			{
				$vldFlag = true;
				$formControl .= " size='40' maxlength='255' ";
			}
			if($vldFlag == false)
				$formControl .= " size='40' maxlength='255' />";
			else
				$formControl .= " />";
		break;
		case "Date Text Field":
			$formControl = "<input type='text' name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."' value='".$arrData[$arrFieldData['FieldName']]."' size='10' />";
		break;
		case "Text Area":
			$formControl = "<textarea name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."' rows='4' cols='40'>".$arrData[$arrFieldData['FieldName']]."</textarea>";
		break;
		case "Check Box":
			$formControl = "<input type='checkbox' name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."' value='".$arrData[$arrFieldData['FieldName']]."' />";
		break;
		case "Radio Button":
			$formControl = returnRadioSiteCodes($arrFieldData['FieldName'], $arrFieldData['SiteCode'], $arrData[$arrFieldData['FieldName']]);
		break;
		case "Single-Select Drop Down":
			if($arrFieldData['SiteCode'] != "")
				$formControl = "<select name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."'>
					<option value=''>Select</option>".
					returnSiteCodes($arrFieldData['SiteCode'], $arrData[$arrFieldData['FieldName']])."
				</select>";
			else if($arrFieldData['PullFormName'] != "" and $arrFieldData['PullFieldName'] != "")
				$formControl = "<select name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."'>
					<option value=''>Select</option>".
					returnPullCodes($arrFieldData['PullFormName'],$arrFieldData['PullFieldName'], $arrData[$arrFieldData['FieldName']])."
				</select>";
		break;
		case "Multi-Select Drop Down":
			if($arrFieldData['SiteCode'] != "")
				$formControl = "<select name='".$arrFieldData['FieldName']."[]' id='".$arrFieldData['FieldName']."' multiple>
					".
					returnMultipleSiteCodes($arrFieldData['SiteCode'], $arrData[$arrFieldData['FieldName']])."
				</select>";
			else if($arrFieldData['PullFormName'] != "" and $arrFieldData['PullFieldName'] != "")
				$formControl = "<select name='".$arrFieldData['FieldName']."[]' id='".$arrFieldData['FieldName']."' multiple>
					".
					returnMultiplePullCodes($arrFieldData['PullFormName'],$arrFieldData['PullFieldName'], $arrData[$arrFieldData['FieldName']])."
				</select>";
		break;
		case "File Browse":
			$formControl = "<input type='file' name='".$arrFieldData['FieldName']."' id='".$arrFieldData['FieldName']."' /> <input type='hidden' name='hdn_".$arrFieldData['FieldName']."' value='".$arrData[$arrFieldData['FieldName']]."' />";
			if($arrData[$arrFieldData['FieldName']] != "")
			$formControl .= "<a href='".$arrData[$arrFieldData['FieldName']]."' target='_blank'><strong>View ".$arrFieldData['FieldLabel']."</strong></a>";
		break;
		case "Rich Text Editor":
			include("./phpincludes/sitePaths.php");
			$oFCKeditor = new FCKeditor($arrFieldData['FieldName']) ;
			$oFCKeditor->BasePath = $FCKBasePath ;
			$oFCKeditor->Config['SkinPath'] = $FCKBasePath . 'editor/skins/silver/' ;
			$oFCKeditor->Config['TemplatesXmlPath'] = $basePath.$XmlFilePath;
			$oFCKeditor->Width = '900';
			$oFCKeditor->Height = '600';
			$oFCKeditor->Value = htmlspecialchars_decode($arrData[$arrFieldData['FieldName']]);
			$oFCKeditor->Create() ;
		break;
	}
	return $formControl;
}

function getCategories($catSelect)
{
	$resCat = mysql_query("SELECT Title FROM tbl_category ORDER BY Title");
	while($objCat = mysql_fetch_object($resCat))
	{
		if($objCat -> Title == $catSelect) $slt = " selected "; else $slt = "";
		echo "<option value='".$objCat -> Title."' ".$slt.">".$objCat -> Title."</option>";
	}
}

function CheckSqlInjectionEmail($text)
{
	$contents = $text;
	if(preg_match('/(base64_|eval|system|shell_|exec|php_|function|xmlhttp|GET)/i',$contents))
	{ 
		return true;
	}
	else if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $contents))
	{
		return true;	
	}
	else if(preg_match("/&\#x([0-9a-f]+);/i", $contents))
	{ 
		return true;
	}
	else if (preg_match('/&\#([0-9]+);/i', $contents))
	{ 
		return true;
	}
	else if(preg_match('/([0-9])/i', $contents))
	{ 
		return true;
	}
	else if (preg_match("/([a-z]*)=([\`\'\"]*)script:/iU", $contents))
	{ 
		return true;
	}
	else if(preg_match("/([a-z]*)=([\`\'\"]*)javascript:/iU", $contents))
	{ 
		return true;
	}
	else if(preg_match("/([a-z]*)=([\'\"]*)vbscript:/iU", $contents))
	{ 
		return true;
	}
	else if(preg_match("/(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>/iU", $contents))
	{ 
		return true;
	}
	else if(preg_match("/(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>/iU", $contents))
	{ 
		return true;
	}
	else if(preg_match('/(javascript|applet|link|style|script|iframe|frame|frameset|html|body|title|div|<p|form)/i', $contents))
	{ 
		return true;
	}
	else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $contents))
	{
		return true;
	}
	else if(preg_match('/(@@|select|nchar|varchar|nvarchar|begin|cursor|drop|update|declare|delete|exec|execute|fetch|insert|kill|sysobjects|syscolumns)/i', $contents))
	{
		return true;
	}
	else if(preg_match('/(@@|select|nchar|varchar|nvarchar|begin|cursor|drop|update|declare|delete|exec|execute|fetch|insert|kill|sysobjects|syscolumns)/', $contents))
	{
		return true;
	}
	else if (preg_match('/(<javascript|<applet|<link|<style|<script|<iframe|<frame|<frameset|<html|<body|<title|<div|<p|<form)/i', $contents))
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>