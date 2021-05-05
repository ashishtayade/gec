<?php
class GenericClass
{
	public $tableName;
	public $columnsArray=array();	

	public function __construct($tableName)
	{
		$this->tableName=$tableName;
		$res=mysql_query("SELECT * FROM ".$this->tableName);
		for($i=0;$i<mysql_num_fields($res);$i++)
			$this->columnsArray[mysql_field_name($res,$i)]="";
	}
	
	public function populatePost($postArray)
	{
		foreach($postArray as $key => $value)
			if($key!="submit" and $key!="Submit")
				$this->columnsArray[$key] = ($value);
	}
	
	public function populateColumns($postArray)
	{
		foreach($this->columnsArray as $key => $value)
			$this->columnsArray[$key]=$postArray[$key];
	}
	
	public function insert()
	{
		$str1="";
		$str2="";
		foreach($this->columnsArray as $key => $value)
		{
			if($key!="submit" and $key!="Submit")
			{
				$str1.=$key.",";
				$str2.="'".addslashes(htmlspecialchars($value))."',";
//				$str2.="'".($value)."',";
			}
		}
		$str1=substr($str1,0,strlen($str1)-1);
		$str2=substr($str2,0,strlen($str2)-1);
		 $qry="INSERT INTO ".$this->tableName." (".$str1.") VALUES  (".$str2.")";
		if(mysql_query($qry))
			return mysql_insert_id();
		else
			return false;
	}
	
	public function update()
	{
		$str="";
		foreach($this->columnsArray as $key => $value)
		{
			if($key!="submit" and $key!="Submit")
				$str.=$key."='".addslashes(htmlspecialchars($value))."',";
//				$str.=$key."='".($value)."',";
		}
		$str=substr($str,0,strlen($str)-1);			
		 $qry="UPDATE ".$this->tableName." SET ".$str." WHERE Id=".$this->columnsArray['Id'];  
		if(mysql_query($qry))
			return true;
		else
			return false;
	}
	
	public function delete($whc)
	{ 
		mysql_query("DELETE FROM ".$this->tableName." WHERE ".$whc);
		return mysql_affected_rows();
	}
	
	public function checkForDuplicates($fieldName, $data, $id)
	{
		if($id!="")
			$qry="SELECT * FROM " . $this->tableName." WHERE ".$fieldName." = '".$data."' AND Id!=".$id;
		else
			$qry="SELECT * FROM " . $this->tableName." WHERE ".$fieldName." = '".$data."'";
		$res=mysql_query($qry);
		if(mysql_num_rows($res) > 0)
			return true;
		else
			return false;
	}
	
	public function getData($whc)
	{
		$arrData=null;
        if ($whc)
            $whc = " where " . $whc;
		 $qry="SELECT * FROM " . $this->tableName. $whc;
        $result = mysql_query($qry);
        if ($result)
		{
			$i=0;
			while($arr=mysql_fetch_assoc($result))
				$arrData[$i++]=$this->removeSlashes($arr);
		}
		return $arrData;
	}
	
	public function removeSlashes($arr)
	{
		foreach($arr as $key => $value)
			$arr[$key]=stripslashes($value); 
		return $arr;
	}

	public function getDataRestricted($whatToGet, $whc)
	{
		$arrData=null;
        if ($whc)
            $whc = " where " . $whc;
		$qry="SELECT ".$whatToGet." FROM " . $this->tableName. $whc;
        $result = mysql_query($qry);
        if ($result)
		{
			$i=0;
			while($arr=mysql_fetch_assoc($result))
				$arrData[$i++]=$arr;
		}
		return $arrData;
	}
	public function getDatalimited($arr,$whc,$status)
	{
		$arrData=null;
		$qry="SELECT {$arr} FROM " . $this->tableName.$whc;
		if($status == true)
		{
			echo $qry;	
		}
        $result = mysql_query($qry) or die(mysql_error());
		if ($result)
		{
			$i=0;
			while($arr=mysql_fetch_assoc($result))
				$arrData[$i++]=$this->removeSlashes($arr);
		}
		return $arrData;
	}
}
?>