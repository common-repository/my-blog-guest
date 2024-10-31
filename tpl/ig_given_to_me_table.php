<?php

		/*
		формирует html код таблицы вывода результатов поиска инфографиков
		*/

	if(!empty($igs))
	{
	echo '<table cellspacing="0" class="wp-list-table widefat fixed posts">';
		foreach($igs as $ig)
		{
		echo '<tr>';
		echo '<td width="25%"><img src="'.$ig['img_preview'].'"></img>';
		echo '<p class="category"><b>Author:</b> <a target="_blank" href="'.MBG_URL.'/forum/profile.php?id='.$ig['id_user_author'].'">'.$ig['author'].'</a>';
			if(!empty($ig['gravatar']))
			{
			echo ' <img style="height: 20px;" src="'.$ig['gravatar'].'"></img>';
			}
		echo '</p>';
		echo '<p class="category"><b>Author Rating:</b> '.$ig['author_rating'].' <a onclick="rateAuthor('.$ig['id_user_author'].', 1, '.$ig['author_rating'].')" href="javascript: void(0);" ><img src="'.WPMBG_IMGS.'/thumb-up.png" /></a><a onclick="rateAuthor('.$ig['id_user_author'].', -1, '.$ig['author_rating'].');" href="javascript: void(0);" ><img src="'.WPMBG_IMGS.'/thumb-down.png" /></a></p>';
		
		echo '<p class="category"><b>Category:</b> '.$ig['category'].'</p>';

		echo '<p class="tasks-menu">';
                echo '<a target="_blank" title="" href="'.$ig['img_download_url'].'">Download Image</a>';

			if(!empty($ig['id_unpublished_descr']))
			{
                        echo '<a href="javascript: mbgImportIgToDraft('.$ig['id_unpublished_descr'].');">Import to Draft</a>';
			}
		echo '</p>';

		echo '</td>';
	
		echo '<td>';
			
			if(!empty($ig['descriptions']))
			{
			$descr_count = 0;
			
				foreach($ig['descriptions'] as $descr)
				{
				echo '<div class="ig-descr '.(($descr_count%2 == 0) ? 'odd' : 'even').'">';
				echo '<h5>'.$descr['title'].'</h5>';
				echo '<div class="ig-descr-body">'.$descr['body'].'</div>';
				echo '<div class="ig-descr-byline">'.$descr['byline'].'</div>';
				
				echo '<p class="tasks-menu">';
				
				echo '<a target="_blank" title="" href="'.$descr['img_download_url'].'">Download Image</a>';
				
					if($descr['status'] == 2)	// кнопка Import to Draft
					{
					echo '<a href="javascript: mbgImportIgToDraft('.$descr['id'].');">Import to Draft</a>';					
					echo '<a href="javascript: mbgIgNotifyMBGAjax('.$descr['id'].');">Inform MBG</a>';
					echo '<a href="javascript: mbgIgRefuseAjax('.$descr['id'].');">Refuse</a>';
					}
				echo '</p>';
					
				echo '</div>';
				$descr_count++;
				}
			}
			
		echo '</td>';
		echo '</tr>';
		}
	echo '</table>';
	}
