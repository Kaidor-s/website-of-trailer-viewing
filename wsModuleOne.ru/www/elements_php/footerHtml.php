<div id="footer">
	<?php 
		$adressResult = array();
		if(gettype($adressFotElem)=='string'){
			$adressResult["input"] = $adressFotElem."elements_php/hrefAFormAuth.php"; 
			$adressResult["output"] = $adressFotElem."elements_php/adminAPI/hrefAForm_exitAdminAPI.php"; 
		}else{ 
			$adressResult["input"] = "elements_php/hrefAFormAuth.php"; 
			$adressResult["output"] = "elements_php/adminAPI/hrefAForm_exitAdminAPI.php"; 
		};

		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
			$default_href_A = '../index.php';
			include_once($adressResult["output"]);
		}else{
			$default_href_A = '../form/form_authAdmin.php';
			include_once($adressResult["input"]);	
		};
	?>
	<p class="text_footer">&#64; Все&nbsp;права&nbsp;защищены.</p>
</div>