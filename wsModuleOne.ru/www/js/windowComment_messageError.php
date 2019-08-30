<?php
	session_start();
	
	if( 
		!is_null($_SESSION["data_resultCreateComment"]) && 
		$_SESSION["data_resultCreateComment"] != false && 
		$_SESSION["data_resultCreateComment"]["status_code"] != 203
	){
		$input_data_ses = array();
		foreach($_SESSION["data_resultCreateComment"]["body"] as $key => $value){
			if( $value == false ){
				$input_data_ses[$key] = '0';
			}else if( is_null($value) ){
				$input_data_ses[$key] = 'null';
			}else{ $input_data_ses[$key] = $value; };
		};
		
		$script_winErr = "<script>
			let data_code = ".$_SESSION["data_resultCreateComment"]["status_code"].",
			data_status = 'result_'+".$input_data_ses["status"].",
			data_text = '".$input_data_ses["message"]."';
			let area_mesError = document.getElementById('createComment');
			
			console.log(data_code, data_text);
			
			let elemWinErr = document.createElement('div');
			elemWinErr.id = 'windowErrorMessage';
			area_mesError.appendChild(elemWinErr);
			
			let buttonClose = document.createElement('button');
			buttonClose.onclick = function(){
				this.parentNode.removeChild(this);
			}.bind(elemWinErr);
			buttonClose.id = 'buttonClose_winEM';
			buttonClose.innerHTML = 'хорошо';
			elemWinErr.appendChild(buttonClose);
			
			let text_block = document.createElement('div');
			text_block.id = 'textBlock_winEM';
			text_block.innerHTML = data_text;
			elemWinErr.appendChild(text_block);
		</script>";
	};
	
	$_SESSION["data_resultCreateComment"] = false;
?>