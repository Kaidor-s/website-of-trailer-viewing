<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php //скрипт для подключения css в соответствии с значением $dataRes_authorizatiionAdminAPI
	include_once("php/data.php"); // подключаем общее хранилище переменных и функций для всех файлов
	$defaultCss = '../css/style.css';
	$_SESSION['currentPage'] = 'catalog.php'; //объявляем текущую страницу
	$dataRes_authorizatiionAdminAPI = $_SESSION['data_auth']; //получаем данные авторизации в админке
	
	include_once("elements_php/headHtml.php"); //подключаем шапку сайта
?>
<body>
	<?php //скрипт для подключения css в соответствии с значением $dataRes_authorizatiionAdminAPI
		//print_r($dataRes_authorizatiionAdminAPI);
		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
			echo "<script>console.log('Результат авторизации: ', ".$dataRes_authorizatiionAdminAPI["status_code"].");</script>";
			if(!is_null($_SESSION['data_resultCreateStory'])){
				echo "<script>console.log('Результат создания истории: ', ".$_SESSION['data_resultCreateStory']["status_code"].");</script>";
			};
		};
		
		$href_main = "index.php";
		$href_catalog = "";
		include_once("elements_php/headerAndNavHtml.php"); //подключаем навигацию(меню) сайта
	?>
	<div id="content">
		<p id="header_content"><span>Премьеры</span></p>
		<div id="area_content">
			<?php
				$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//получаем данные сюжетов из бд
				$mysqli->query("SET NAMES 'utf8'");
				$result_set = $mysqli->query("SELECT * FROM `data_stories`");
				$data = printResult($result_set, NULL, "story_id");
				$mysqli -> close();
				
				$elemBlock_class = 'storyBlocks';// объявляем переменные со здачениями
				$p_class = 'storyBlocks_p';
				$a_class = 'storyBlocks_a'; 
				$input_class = 'storyBlocks_deleteStoryButton';
				$link_phpScript = '../php/adminAPI/workWithStoryAPI.php';
				$link_jsScript_Interface = '../js/APIDeleteStory_interface.js';
				
				if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
					echo "<script src='".$link_jsScript_Interface."'></script>"; //подключаем логику интерфейса удаления сюжета
				};
				foreach($data as $key_idStory => $value_idStory){ //генерируем каталог в соответствии с данными из бд
					$div_id = addID_forDivEl_inCatalog($dataRes_authorizatiionAdminAPI, $key_idStory);
					echo "<div class='".$elemBlock_class."' id='".$div_id."'>
						<a href='".$data[$key_idStory]["story_link"]."' class='".$a_class."' style='background-image: url(".$data[$key_idStory]["story_image"].")'></a>
						<p class='".$p_class."'><span>".$data[$key_idStory]["story_title"]."</span></p>";
						if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
							echo "<input type='submit' value='x' name='done_eDF' class=".$input_class." onclick='createQuestionBlock.call(createQuestionBlock, ".$div_id.", ".'form_'.$key_idStory.")'>";
							echo "<form action='".$link_phpScript."' method='post' id='".'form_'.$key_idStory."'>
								<input type='hidden' value='".$data[$key_idStory]["story_link"]."' name='link'>
							</form>";
						};
					echo "</div>";
					// if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){ //подключаем интерфейс удаления сюжета
						
					// };
				};
				
				if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){ //добавляем последним элементом функцию добавления нового сюжета при админских правах
					$elemBlock_class_add = 'add_storyBlocks';
					$p_class_add = 'add_storyBlocks_p';
					
					$button_class_add = 'add_storyBlocks_button';
					$button_id = 'newStory_done_id';
					$button_name = 'done_eDF';
					
					$adressForm = 'php/adminAPI/workWithStoryAPI.php';
					echo "<div class='".$elemBlock_class."' id='".$elemBlock_class_add."'>
						<p class='".$p_class_add."'>Добавить новый сюжет</p>
						<form action=".$adressForm." method='post'>
							<input id='".$button_id."' type='submit' value='+' name='".$button_name."' class='".$button_class_add."'>
						</form>
					</div>";
				};
				
				//-----------------------------------------------------
				function addID_forDivEl_inCatalog($dataRes_authorizatiionAdminAPI, $key_idStory){// функция создания идентификатора блокам сюжетов
					if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
						return 'storyBlock_'.$key_idStory;
					};
					return 'storyBlock';
				};
			?>
		</div>
	</div>
	<?php 
		include_once("elements_php/footerHtml.php"); //подключаем футер сайта
	?>
</body>
</html>