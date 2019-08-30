<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php
	include_once("php/data.php");
	$defaultCss = '../css/style.css';
	$_SESSION['currentPage'] = 'index.php';
	$dataRes_authorizatiionAdminAPI = $_SESSION['data_auth'];

	$libraries_jsScript = "<script src='js/searchTags.js'></script>";
	include_once("elements_php/headHtml.php");
?>
<body>
	<?php //скрипт для подключения css в соответствии с значением $dataRes_authorizatiionAdminAPI
		$href_main = "";
		$href_catalog = "catalog.php";
		
		//echo $_SESSION["user_token"];
		//print_r($_POST);
		//print_r($dataRes_authorizatiionAdminAPI);
		
		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
			echo "<script>console.log('Результат авторизации: ', ".$dataRes_authorizatiionAdminAPI["status_code"].");</script>";
		};
		include_once("elements_php/headerAndNavHtml.php");
	?>
	<div id="content">
		<p id="header_content"><span>Главная</span></p>
		<label id="searchStoryByTags">
			<span>Find me, my master<button id="button_searchTags">&#128270;</button></span>
			<input id="input_searchTags" type="text" placeholder="#боевики, #аниме, #фантастика, #ужасы"/>
			<script>
				//присваиваем события отправки запроса на поиск кнопке"лупа" и при нажатии enter в строке ввода input
				document.getElementById("button_searchTags").onclick = function(){
					requestSearchTegs.call(requestSearchTegs, document.getElementById("input_searchTags").value, 'requestOnSearch', '<?=$_SESSION["user_token"]?>');
				};
				document.getElementById("input_searchTags").addEventListener("keydown", function(event) {
					if (event.keyCode == 13){
						requestSearchTegs.call(requestSearchTegs, this.value, 'requestOnSearch', '<?=$_SESSION["user_token"]?>');
					};
				 });
			</script>
		</label>
		<?php //requestSearchTegs
			if( isset($_POST["tegRequest"]) &&  !is_null($_POST["tegRequest"])){
				echo "<script>
					requestSearchTegs.call(requestSearchTegs, '".$_POST["tegRequest"]."', 'requestOnSearch', '".$_SESSION["user_token"]."');
				</script>";
			};
		?>
		<div id="content_resultSearch">
			<div id="top" class="pagination">
				<div class="paginationArea">
				</div>
			</div>
			<div id="elems_resultSearch">
			</div>
			<div id="bottom" class="pagination">
				<div class="paginationArea">
				</div>
			</div>
		</div>
	</div>
	<?php 
		include_once("elements_php/footerHtml.php");
	?>
</body>
</html>