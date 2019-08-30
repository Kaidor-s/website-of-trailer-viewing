<?php
//форма отправки комментарий
echo "<div id='createComment'>
	<p class='header_createComment'>Оставить&nbsp;комментарий:</p>
	<form action='".$link_php."' method='post'>
		<input type='hidden' name='type' value='comment_send'>";
		if(!is_null($dataRes_authorizatiionAdminAPI) && $dataRes_authorizatiionAdminAPI["body"]["token"] == 'admin1_token_id78633'){
			echo "<label>
				<p style='width: 0;'>Имя:&nbsp;<span>".$dataRes_authorizatiionAdminAPI["body"]["nameAdmin"]."</span></p>
				<input type='hidden' class='form_elems' name='admin_name' value='".$dataRes_authorizatiionAdminAPI["body"]["nameAdmin"]."'>
			</label>
			<label>
				<p style='width: 0;'>Почта:&nbsp;<span>".$dataRes_authorizatiionAdminAPI["body"]["mail"]."</span></p>
				<input type='hidden' class='form_elems' name='admin_mail' value='".$dataRes_authorizatiionAdminAPI["body"]["mail"]."'>
			</label>";
		}else{
			echo "<label><p>Имя: </p><input placeholder='Джон' type='text' class='form_elems' maxlength='32' required name='user_name'></label>
			<label><p>Почта: </p><input placeholder='myMail@gmail.com' type='mail' class='form_elems' maxlength='64' required name='user_mail'></label>";
		};
		echo "<label class='tA_lab'><p>Комментарий: </p><textarea placeholder='Мой комментарий:' class='form_elems' maxlength='2048' required name='text_comment'></textarea></label>
		<input class='createComment_button' type='submit' name='done' value='отправить''>
	</form>
</div>"; 
?>