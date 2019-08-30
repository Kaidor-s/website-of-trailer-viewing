<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php
	include_once("../php/data.php");
	$defaultCss = '../css/style.css';
	
	$nameFile = basename("../pagesStories/".$_SERVER['PHP_SELF']);
	$id_story = substr($nameFile, 0, strlen($nameFile) - SETTING_CLIPPAGE_SUBSTR::TEMP_EXISTFILEPHP_LEN);
	$_SESSION['currentPage'] = "pagesStories/".$id_story.'.php';
	
	$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд
	$mysqli->query("SET NAMES 'utf8'");
	$result_set = $mysqli->query("SELECT * FROM `data_stories`");
	$data = printResult($result_set, $id_story, "story_id");
	$result_set = $mysqli->query("SELECT * FROM `".SETTING_CLIPPAGE_SUBSTR::TEMP_CREATETABLETAGS.$id_story."`");
	$data_tags = printResult($result_set, null, "story_id");
	$mysqli->close();
	
	$dataRes_authorizatiionAdminAPI = $_SESSION['data_auth'];
	
	include_once("../elements_php/headHtml.php");
?>
<body>
	<?php 
		$href_main = "../index.php";
		$href_catalog = "../catalog.php";
	
		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"]=='admin1_token_id78633'){
			echo "<script>console.log('Результат авторизации: ', ".$dataRes_authorizatiionAdminAPI["status_code"].");</script>";
			if(!is_null($_SESSION['data_resultEdditionStory'])){
				echo "<script>console.log('Результат изменения истории: ', ". $_SESSION['data_resultEdditionStory']["status_code"].");</script>";
			};
			//print_r($dataRes_authorizatiionAdminAPI);
		};
		
		include_once("../elements_php/headerAndNavHtml.php");
	?>
	<div id="content">
		<div id="text_content">
			<div id="description">
				<?php include_once("../elements_php/srcImgDescriptHtml.php"); ?>
				<p id="header_creatStroty" class="header_preview"><?=$data["story_title"]?></p>
				<p id="announcement_creatStroty" class="announcementStory"><span>Анонс:&nbsp;</span><?=$data["story_anons"]?></p>
				<?php
					$linkPage_main = '../index.php';
					$str = "";
					foreach($data_tags as $key => $value){
						$str .= "<input type='submit' name='tegRequest' value='".$value["tag_id"]."' class='tags_elems'> ";
					};
					echo "<form id='tags_creatStroty' class='tags' name='formSearchRequest' action=".$linkPage_main." method='post'>";
					echo "<span>Теги:&nbsp;</span>".$str."</form>";
				?>
				<?php 
					if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"] == 'admin1_token_id78633'){
						include_once('../js/sendTags_regExp.php');
					};
				?>
				<p id="description_text_creatStroty" class="text_preview"><?=$data["story_text"]?></p>
			</div>
			<?php
				if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"] == 'admin1_token_id78633'){
					$defaultAdress_EDFButt = '../php/adminAPI/workWithStoryAPI.php'; 
					$defaultAdress_jsInterface = '../js/APIEdditionStory_interface.js';
					include_once("../elements_php/adminAPI/interfaceEddStory.php");	
				};
			?>
		</div>
		<div id="media_content">
			<p class="header_media">ТРЕЙЛЕР</p>
			<div id="video_media"></div>
		</div>
	</div>
	<div id="comments">
		<p class="header_comments">КОММЕНТАРИИ</p>
		<?php
			$linkScript_openComment = '../js/API_vievFullComent.js';
			$linkScript_deleteComment = '../js/APIDeleteComment_interface.php';
			$link_php = '../php/adminAPI/workWithComments.php'; //добавляем форму отправки комментария
		
			$mysqli = new mysqli("localhost", "root", "", "dataadminapi");//подключаемся к бд 
			$mysqli->query("SET NAMES 'utf8'");
			$result_set_users = $mysqli->query("SELECT * FROM `data_comments`");
			$data_users = printResult($result_set_users, null, "id_comment");
			$result_set_admin = $mysqli->query("SELECT * FROM `data_comments_administrator`");
			$data_admin = printResult($result_set_admin, null, "id_comment");
			$mysqli->close();
			
			helper_downloadComment($data_admin, array('id_story', 'name_admin', 'text_comment'), $id_story);
			helper_downloadComment($data_users, array('id_story', 'name_user', 'text_comment'), $id_story);
			echo "<script src='".$linkScript_openComment."'></script>";
			include_once($linkScript_deleteComment);
			
			include_once("../form/form_sendComment.php");	
			
			include_once("../js/windowComment_messageError.php");
			echo $script_winErr;
		?>
	</div>
	<?php
		$adressFotElem = "../";
		include_once("../elements_php/footerHtml.php");
	?>
</body>
</html>