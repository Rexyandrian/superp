<?php
	class pshowGrid_new{
		public $canAdd = TRUE;
		public $canEdit = TRUE;
		public $canDelete = TRUE;
		public $addFunction = "";
		public $editFunction = null;
		public $deleteFunction = "";
		public $query = "";
		public $indexHeader = 'ردیف';
		private $dataSet = null;
		public $columnHeaders = array();
		public $columnFunctions = array();
		public $columnCallBackFunctions = array();
		public $columnLists = array();
		public $columnAccesses = array();
		public $width = "80%";
		//public $rowPerPage = 0;
		public $cssClass = "showgrid";
		private $rowCount = 0;
		public $footer = "";
		public $fields = array();
		private $class_enabled = FALSE;
		private $error_message = "کلاس گرید به درستی معرفی نشده است";
		private $eRequest = null;
		public $fieldList = array();
		public $whereClause = "1=1";
		public $extraClause = "";
		public $tableName = "";
		public $gridName = null;
		public $pageCount = 10;
		public $sums = array();
		private $pageNumber = 0;
		public function __construct(/*$tableName,$gridName=null*/){
		        $args = func_get_args();
			$tableName = '';
			$gridName = null;
			if(count($args)==2){				
				$tableName = $args[0];
				$this->tableName = $tableName;
				$gridName = $args[1];
				$this->gridName = $gridName;
			}
/*		        for( $i=0, $n=count($args); $i<$n; $i++ )
			{
			        //$this->add($args[$i]);
			}
*/			if($gridName!=null && $tableName!=''){ $this->class_enabled = TRUE;}
			if($this->class_enabled){
				if($this->query == ""){
					mysql_class::ex_sql("select * from $tableName where 0=1",$q);
				}else{
					mysql_class::ex_sql($this->query,$q);
					$this->canEdit = FALSE;
					$this->canAdd = FALSE;
					$this->canDelete = FALSE;
				}
				while($r=mysql_fetch_field($q)){
					//var_dump($r);
					$this->fields[] = $r;
					$this->columnHeaders[] = $r->name;
					$this->columnFunctions[] = null;
					$this->columnLists[] = null;
					$this->columnAccesses[] = 1;
					$this->fieldList[] = $r->name;
					switch($r->type){
						case "string":
							$defVal = "";
							break;
						case "int":
							$defVal = -1;
							break;
					}
					$this->add($r->name,$defVal);
				}
			}
		}
		public function field_is_string($field){
			for($i=0;$i<count($this->fields);$i++){
				if($this->fields[$i]->name==$field && $this->fields[$i]->type!='int'){
					return TRUE;
				}
			}
			return FALSE;
		}
		public function fieldId($fieldname){
			$out = -1;
			//var_dump($this->fieldList);
			for($i = 0;$i < count($this->fieldList);$i++){
				//echo $this->fieldList[$i] . " === " . $fieldname . "<br/>\n";
				if($this->fieldList[$i]==$fieldname){
					$out = $i;
				}
			}
			return $out;
		}
		public function intial(){
			if(isset($_POST['selectedField_'.$this->gridName])){
				$mod = $_POST['mod_'.$this->gridName];
				$tmp = explode("_",$_POST['selectedField_'.$this->gridName]);
				$this->pageNumber = $_POST["pageNumber_".$this->gridName];
				$this->rowCount = $_POST["rowCount_".$this->gridName];
				//var_dump($tmp);
                                if(count($tmp)>=3){
                                        $selectedId = $_POST[$tmp[0]."_".$tmp[1]."_id"];
                                }
				switch($mod){
					case "edit":
						$field = "";
						for($i=2;$i<count($tmp)-1;$i++){
							$field .= $tmp[$i]."_";
						}
						$field .= $tmp[$i];
						$tmp[2] = $field;		
						$fieldId = $this->fieldId($field);				
						if($this->editFunction!=null){
							$editF=$this->editFunction;
							$editF($selectedId,$tmp[2],$_POST[$_POST['selectedField_'.$this->gridName]]);
						}else{
							$valu = $_POST[$_POST['selectedField_'.$this->gridName]];
							//var_dump($this->fieldId($field));
							if(isset($this->columnCallBackFunctions[$fieldId])){
								$fn = $this->columnCallBackFunctions[$fieldId];
								$valu  = $fn($_POST[$_POST['selectedField_'.$this->gridName]]);
								//echo "Fuckin CALL BACK Executed...";
							}
							
							mysql_class::ex_sqlx("update ".$this->tableName." set ".$tmp[2]."=".(($this->field_is_string($tmp[2]))?"'":"").$valu.(($this->field_is_string($tmp[2]))?"'":"")." where ".$this->tableName.".id=$selectedId");
							//echo "update ".$this->tableName." set ".$tmp[2]."=".(($this->field_is_string($tmp[2]))?"'":"").$valu.(($this->field_is_string($tmp[2]))?"'":"")." where id=$selectedId";
						}
						break;
					case "add":
						if($this->addFunction!=null){
							$addF=$this->addFunction;
							$addF();
						}else{
							//adding
							$addFields = array();
							$addValues = array();
							for($i=0;$i<count($this->fieldList);$i++){								
								if($this->columnHeaders[$i]!=null){
									$addFields[] = $this->fieldList[$i];
									$addValues[] = $_POST["new_".$this->fieldList[$i]];
								}
							}
							$qur = "(";
							for($i=0;$i<count($addFields)-1;$i++){
								$qur .= $addFields[$i].",";
							}
							$qur .= $addFields[$i].") values (";
							for($i=0;$i<count($addValues)-1;$i++){
								$valu = $addValues[$i];

								if(isset($this->columnCallBackFunctions[$this->fieldId($addFields[$i])])){
									$fn = $this->columnCallBackFunctions[$this->fieldId($addFields[$i])];
									$valu  = $fn($addValues[$i]);
								}

								$qur .= (($this->field_is_string($addFields[$i]))?"'":"").$valu.(($this->field_is_string($addFields[$i]))?"'":"").",";
							}
							$valu = $addValues[$i];

							if(isset($this->columnCallBackFunctions[$this->fieldId($addFields[$i])])){
								$fn = $this->columnCallBackFunctions[$this->fieldId($addFields[$i])];
								$valu  = $fn($addValues[$i]);
							}

							$qur .= (($this->field_is_string($addFields[$i]))?"'":"").$valu.(($this->field_is_string($addFields[$i]))?"'":"").")";
							mysql_class::ex_sqlx("insert into ".$this->tableName.$qur);
						}
						break;
					case "delete":
						if($this->deleteFunction!=null){
							$deleteF=$this->deleteFunction;
							$deleteF($selectedId);
						}else{
							mysql_class::ex_sqlx("delete from ".$this->tableName." where ".$this->tableName.".id=$selectedId");
						}
						break;
					case "next":
						if($this->pageNumber+$this->pageCount<=$this->rowCount){
							$this->pageNumber += $this->pageCount;
						}else{
							$this->pageNumber = 0;
						}
						break;
					case "prev":
						if($this->pageNumber-$this->pageCount>=0){
							$this->pageNumber -= $this->pageCount;
						}else{
							$this->pageNumber = $this->rowCount - $this->pageCount;
						}
						break;
				}
			}
			foreach($_REQUEST as $key => $value){
				if(!isset($this->eRequest[$key])){
				}
			}
		}
		private function add( $name = null, $enum = null ) {
			if( isset($enum) ){
				$this->$name = $enum;
			}
			else{
				$this->$name = end($this) + 1;
			}
		}
		public function testSort(){
			$out = trim($this->query);			
			return($out);
		}
		private function arrayToString($inp){
			$out = "";
			for($i=0;$i<count($inp);$i++){
				$out .= $inp[$i].",";
			}
			$out = substr($out,0,-1);
			return $out;
		}
		public function columnListToCombo($columnList,$sel){
			$out = "";
			foreach($columnList as $text => $value){
				$out .= "<option value=\"$value\" ".(($value==$sel)?"selected=\"selected\"":"")." >\n";
				$out .= $text."\n";
				$out .= "</option>\n";
			}
			return $out;
		}
		private function createPageNumbers(){
			$count = 0;
			if($this->rowCount % $this->pageCount>0){
				$count = 1;
			}
			$count += (($this->rowCount-($this->rowCount % $this->pageCount))/$this->pageCount);
			$out = "&nbsp;";
			for($i = 1;$i<=$count;$i++){
				$out .= ((($this->pageNumber/$this->pageCount)+1==$i)?$i."&nbsp;":"<a href=\"#\" onclick=\"gotoPage_".$this->gridName."($i);\" />$i</a>&nbsp;");
			}
			return($out);
		}
		public function executeQuery(){
			if($this->class_enabled){
				if($this->query == ""){
					mysql_class::ex_sql("select ".$this->arrayToString($this->fieldList)." from ".$this->tableName." ".$this->extraClause." where ".$this->whereClause,$this->dataSet);
				}else{
					mysql_class::ex_sql($this->query,$this->dataSet);
				}
				$temp_dataset = $this->dataSet;
				while($r = mysql_fetch_array($temp_dataset)){
					if($this->sums != null){
						foreach($this->sums as $sum_key => $sum_value){
							if(isset($r[$sum_key])){
								$this->sums[$sum_key] = $r[$sum_key];
							}
						}
					}
				}
				$this->rowCount = mysql_num_rows($this->dataSet);
				$this->dataSet = null;
				if($this->query == ""){
					mysql_class::ex_sql("select ".$this->arrayToString($this->fieldList)." from ".$this->tableName." ".$this->extraClause." where ".$this->whereClause." limit ".$this->pageNumber.",".$this->pageCount,$this->dataSet);
				}else{
					mysql_class::ex_sql($this->query." limit ".$this->pageNumber.",".$this->pageCount,$this->dataSet);
				}
			}	
		}			
		public function getGrid(){
			function khonsa_pshowgrid($inp){
                        	return($inp);
                	}
			function isOdd($inp)
			{
				$out = TRUE;
				if((int)$inp % 2 == 0 ){
					$out = FALSE;
				}
				return ($out);
			}
			$out = $this->error_message;
			if($this->class_enabled){
			$out = <<<Holly
			<div id="$this->gridName">
				<form id="frm_$this->gridName" method="post">
				<input type="hidden" id="selectedField_$this->gridName" name="selectedField_$this->gridName" value="" />
				<input type="hidden" id="mod_$this->gridName" name="mod_$this->gridName" value=""/>
				<input type="hidden" id="pageNumber_$this->gridName" name="pageNumber_$this->gridName" value="$this->pageNumber" />
				<input type="hidden" id="pageCount_$this->gridName" name="pageCount_$this->gridName" value="$this->pageCount" />
				<input type="hidden" id="rowCount_$this->gridName" name="rowCount_$this->gridName" value="$this->rowCount" />
				<script language="javascript">
					function readyToEdit_$this->gridName(gridName,fieldName){ 
						//alert(gridName+' - '+fieldName);
						if(document.getElementById(fieldName)){
							//if(document.getElementById(fieldName).value){
								document.getElementById(fieldName).style.display='block';
								if(document.getElementById(fieldName+'_back')){document.getElementById(fieldName+'_back').style.display='none';}
								document.getElementById(fieldName).focus();
							//}
							document.getElementById('selectedField_$this->gridName').value = fieldName;
							document.getElementById('mod_$this->gridName').value="edit";
						} 
					}
					function readyToDelete_$this->gridName(fieldName){
						document.getElementById('mod_$this->gridName').value="delete";
						document.getElementById('selectedField_$this->gridName').value = fieldName;
						extra_sendObj_$this->gridName("frm_$this->gridName");
					}
					function readyToAdd_$this->gridName(){
						document.getElementById('mod_$this->gridName').value="add";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");
					}
					function extra_sendObj_$this->gridName(frm){
						document.getElementById(frm).submit();
					}
					function mover_$this->gridName(j){
						document.getElementById('delete_$this->gridName'+'_'+j).style.display="block";
					}
                                        function mout_$this->gridName(j){
                                                document.getElementById('delete_$this->gridName'+'_'+j).style.display="none";
                                        }
					function ifEnter_$this->gridName(e){
						var out = false;
					        var keycode;                  
					        if (window.event) keycode = window.event.keyCode;
					        else if (e) keycode = e.which;
					        if(parseInt(keycode,10)==13){
					                out = true;
					        }
						return(out);				
					}
					function nextPage_$this->gridName(){
                        			document.getElementById('mod_$this->gridName').value="next";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
					function prevPage_$this->gridName(){
                        			document.getElementById('mod_$this->gridName').value="prev";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
					function gotoPage_$this->gridName(pageindex){
						var pageCount = parseInt(document.getElementById('pageCount_$this->gridName').value,10);
						document.getElementById('pageNumber_$this->gridName').value=(pageindex-1)*pageCount;
                        			document.getElementById('mod_$this->gridName').value="goto";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
				</script>
Holly;
			$sums = null;
			if(is_array($this->eRequest)){
				foreach($this->eRequest as $key => $value){
					$out .= "<input type=\"hidden\" id=\"$key\" name=\"$key\" value=\"$value\" />\n";
				}
			}
			$out .= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"" . $this->width . "\" style=\"border-style:solid;border-width:1px;border-color:Black;\">\n";
			$out = $out . "<tr class=\"" . $this->cssClass . "_header\" >\n<th class='".$this->cssClass."_gHead' >\n";
			$out = $out . $this->indexHeader."\n";
			$out = $out . "<input type=\"hidden\" id=\"row_number\" value=\"0\" />";
			$out = $out . "</th>\n";
			for($i=0;$i<sizeof($this->columnHeaders);$i++){
				$out = $out . "<th class='".$this->cssClass."_gHead' style=\"".(($this->columnHeaders[$i]!=null)?"":"display:none;")."\">\n";
				$out = $out . $this->columnHeaders[$i] . "\n";
				$out = $out . "</th>\n";
			}
			$out = $out . "</tr>\n";
			$j=1;
			while($r = mysql_fetch_array($this->dataSet,MYSQL_ASSOC)){
				if(isOdd($j)){
					$out = $out . "<tr class=\"" . $this->cssClass . "_row_odd\">\n";				
				}else{
					$out = $out . "<tr class=\"" . $this->cssClass . "_row_even\">\n";
				}
				$i = 0;
				$out = $out . "<td onmouseover=\"mover_".$this->gridName."('$j');\" onmouseout=\"mout_".$this->gridName."('$j');\" class=\"" . $this->cssClass . "_row_td\">\n";
				$out = $out . ($j+$this->pageNumber) . "\n";
/*				if($this->canEdit){
					$out = $out . " <u id=\"edit_" . "$j\" style=\"display:none;\"><span style=\"color:Blue;cursor:pointer;\" onclick=\"document.getElementById('row_number').value='$j';" . $this->editFunction . "\">اصلاح</span></u> ";					
				}
*/				if($this->canDelete){
					$out = $out . " <u id=\"delete_".$this->gridName."_$j\" style=\"display:none;\"><span style=\"color:Blue;cursor:pointer;\" onclick=\"if(confirm('آیا این رکورد حذف شود؟')){readyToDelete_".$this->gridName."('".$this->gridName."_$j" . "_id');}\">حذف</span></u> ";
				}
				$out = $out . "</td>\n";
				if($j==1){
					$sums =$r;
					if($sums!=null)
					{
						foreach($sums as $key=>$value){
							$sums[$key]=0;
						}
					}
				}
				//foreach($r as $key=>$value){
				for($hasan=0;$hasan<count($this->fieldList);$hasan++){
					$key = $this->fieldList[$hasan];
					$value = $r[$key];
					if($this->columnFunctions[$i]==null){
						//$fn = function ($inpp){return($inpp);};
						$fn = "khonsa_pshowgrid";
					}else{
						$fn = $this->columnFunctions[$i];
					}
					//if($i<sizeof($this->columnHeaders)){
					if($this->columnHeaders[$i]!=null){
						//.(($this->columnAccesses[$i]==1 && $this->canEdit)?"onclick=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');\"":"").
						$out = $out . "<td class=\"" . $this->cssClass . "_row_td\" ".(($this->columnAccesses[$i]==1 && $this->canEdit && $this->columnLists[$i]==null)?"onclick=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');\"":"").">\n";
						if($this->columnLists[$i]==null){
// onblur=\"this.style.display='none';extra_sendObj_".$this->gridName."('frm_".$this->gridName."');\"
							$out = $out . "<span id=\"".$this->gridName."_$j" . "_$key"."_back\" style=\"display:block;\">".(($fn($value)!='')?$fn($value):'&nbsp;') . "\n</span><input  class=\"".$this->cssClass."_inp\" ".(($this->columnAccesses[$i]==1 && $this->canEdit)?"":"readonly=\"readonly\"")." type=\"text\" id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" value=\"$value\" style=\"display:none;\" onblur=\"this.style.display='none';extra_sendObj_".$this->gridName."('frm_".$this->gridName."');\" onkeypress=\"if(ifEnter_$this->gridName(event)){this.onblur();}\">";
						}else{
							$out = $out . "<select class=\"".$this->cssClass."_inp\" ".(($this->columnAccesses[$i]==1 && $this->canEdit)?"":"disabled=\"disabled\"")." id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" onchange=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');extra_sendObj_".$this->gridName."('frm_".$this->gridName."');\" onkeypress=\"if(ifEnter_$this->gridName(event)){this.onchange();}\" >\n" . $this->columnListToCombo($this->columnLists[$i],$value)."</select>\n";
						}
						$out = $out . "</td>\n";
						if($key=='Total'){
							$sums[$key] = (int)$sums[$key] + (($fn($value)!='')?(int)$value:0);
						}else{
							$sums[$key] = (int)$sums[$key] + (($fn($value)!='')?(int)$fn($value):0);						
						}
					}else{
						$out = $out . "<td style=\"display:none;border-style:solid;border-width:1px;border-color:Black;text-align:center;font-family:Tahoma,tahoma;font-size:small;\">\n";
						$out = $out . (($fn($value)!='')?$fn($value):'&nbsp;') . "<input type=\"text\" id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" value=\"$value\">\n";				
						$out = $out . "</td>\n";
						$sums[$key] = (int)$sums[$key] + (($fn($value)!='')?(int)$fn($value):0);
					}
					$i++;
				}
				$out = $out . "</tr>\n";
				$j++;
			}
			$out = $out . "<tr class=\"" . $this->cssClass . "_insert_row\">\n";
			$out = $out . "<td colspan=\"" . (sizeof($this->columnHeaders)+1) . "\" style=\"border-style:solid;border-width:1px;border-color:Black;text-align:right;font-family:Tahoma,tahoma;font-size:small;\">\n";
			$out = $out . "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"بعدی\" onclick=\"nextPage_".$this->gridName."();\" ".((($this->rowCount/$this->pageCount)<=1)?"disabled=\"disabled\"":"")."/>";
			$out .= $this->createPageNumbers();
			$out = $out . "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"قبلی\" onclick=\"prevPage_".$this->gridName."();\" ".(($this->pageNumber<=0)?"disabled=\"disabled\"":"")."/>";
			//$out = $out . "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"قبلی\" onclick=\"prevPage_".$this->gridName."();\" />";
			$out = $out . "</td>\n";
			$out = $out . "</tr>\n";
			$out = $out . $this->footer;
			$out .= (($this->canAdd)?"<tr class=\"" . $this->cssClass . "_row_even\"><td class=\"" . $this->cssClass . "_row_td\">ثبت جدید</td>\n":"");
                        for($i=0;$i<sizeof($this->fieldList) && $this->canAdd;$i++){
                                $out = $out . "<td class=\"" . $this->cssClass . "_row_td\" style=\"".(($this->columnHeaders[$i]!=null)?"":"display:none;")."\">\n";
				if($this->columnLists[$i]==null){
                                	$out = $out . "<input  class=\"".$this->cssClass."_inp\" type=\"text\" id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\" value=\"\" />\n";
				}else{
					$out = $out . "<select id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\">\n" . $this->columnListToCombo($this->columnLists[$i],null)."</select>\n";
				}
                                $out = $out . "</td>\n";
                        }
			$out .= (($this->canAdd)?"</tr>\n":"");
			if($this->canAdd){
				$out = $out . "<tr class=\"" . $this->cssClass . "_insert_row\">\n";
				$out = $out . "<td colspan=\"" . (sizeof($this->columnHeaders)+1) . "\" style=\"border-style:solid;border-width:1px;border-color:Black;text-align:left;font-family:Tahoma,tahoma;font-size:small;\">\n";
				$out = $out . "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"ثبت\" onclick=\"readyToAdd_".$this->gridName."();\" />";
				$out = $out . "</td>\n";
				$out = $out . "</tr>\n";
			}
//			if($r = mysql_fetch_array($dataSet,MYSQL_ASSOC)){
				$out = $out . "<tr>\n";
				if($sums != null){
					foreach($sums as $key=>$value){
						$out = $out . "<td id=\"sum_$key\" style=\"display:none;border-style:solid;border-width:1px;border-color:Black;text-align:center;font-family:B Titr,b titr,B titr,b Titr,Tahoma,tahoma;font-size:medium;\">\n";
						$out = $out . $value . "\n";
						$out = $out . "</td>\n";
					}
				}
				$out = $out . "</tr>\n";
//			}
			$out = $out . "</table>";
			$out .= "</form></div>";
			}
			$this->sums = $sums;
			return($out);

		}
		public function setERequest($inp){
			$this->eRequest = $inp;
		}
	}
?>
