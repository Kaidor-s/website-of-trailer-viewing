<head>
	<title>Модуль&nbsp;1</title>
	<meta charset="utf-8"/>
	<link href="<?=$defaultCss?>" type="text/css" rel="stylesheet"/>
	<?php
		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
			echo "<link href='../css/adminAPI/styleElemsAPI.css' type='text/css' rel='stylesheet'/>";
		};
	?>
	<?=$libraries_jsScript?>
</head>