<?php 
echo "<script>
	let interval = setInterval(function(){
		let tagsArea = document.getElementById('tags_creationStory_id');
		if(tagsArea != undefined){
			let button = document.getElementById('done_eDF_id');
			button.onclick = function(){
				let text = tagsArea.value+' '; //прибавляем пробел, чтобы небыло ошибки в регулярке
				let regExp = /#[\wА-Яа-я\.]+[\s,\.]/ig;
				let resultArray = text.match(regExp);
				let obj = {};
				if(resultArray != null && resultArray != undefined && resultArray.length != 0){
					for(let i = 0; i < resultArray.length; i++){
						obj[String(i)] = resultArray[i].substr(0, resultArray[i].length-1).toLowerCase();
					};
					
					tagsArea.value = JSON.stringify(obj);
				};
			};
			clearInterval(interval);
		};
	}, 100);
</script>";
?>