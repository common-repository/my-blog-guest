<?php

		/*
		формирует html код таблицы вывода результатов поиска инфографиков
		*/

	if(!empty($books))
	{

	echo '<table cellspacing="0" class="wp-list-table widefat fixed posts">';
		foreach($books as $book)
		{
		echo '<tr>';
		echo '<td width="25%"><img src="'.$book['img_preview'].'"></img>';
		echo '<p class="category"><b>Author:</b> <a target="_blank" href="'.MBG_URL.'/forum/profile.php?id='.$book['id_user_author'].'">'.$book['author'].'</a>';
			if(!empty($book['gravatar']))
			{
			echo ' <img style="height: 20px;" src="'.$book['gravatar'].'"></img>';
			}
		echo '</p>';
		echo '<p class="category"><b>Author Rating:</b> '.$book['author_rating'].' <a onclick="rateAuthor('.$book['id_user_author'].', 1, '.$book['author_rating'].')" href="javascript: void(0);" ><img src="'.WPMBG_IMGS.'/thumb-up.png" /></a><a onclick="rateAuthor('.$book['id_user_author'].', -1, '.$book['author_rating'].');" href="javascript: void(0);" ><img src="'.WPMBG_IMGS.'/thumb-down.png" /></a></p>';
		
		echo '<p class="category"><b>Category:</b> '.$book['category'].'</p>';

		echo '<p class="tasks-menu">';
                echo '<a target="_blank" title="" href="'.$book['img_download_url'].'">Download Image</a>';

			if(!empty($book['id_unpublished_descr']))
			{
                        echo '<a href="javascript: mbgImportBookToDraft('.$book['id_unpublished_descr'].');">Import to Draft</a>';
			}
		echo '</p>';

		echo '</td>';
	
		echo '<td>';
			
			if(!empty($book['descriptions']))
			{
			$descr_count = 0;
			
				foreach($book['descriptions'] as $descr)
				{
				echo '<div class="book-descr '.(($descr_count%2 == 0) ? 'odd' : 'even').'">';
				echo '<h5>'.$descr['title'].'</h5>';
				echo '<div class="book-descr-body">'.$descr['body'].'</div>';
				echo '<div class="book-descr-byline">'.$descr['buy_instruction'].'</div>';
				
				echo '<p class="tasks-menu">';
				
				echo '<a target="_blank" title="" href="'.$descr['img_download_url'].'">Download Image</a>';
				
					if($descr['status'] == 2)	// кнопка Import to Draft
					{
					echo '<a href="javascript: mbgImportBookToDraft('.$descr['id'].');">Import to Draft</a>';					
					echo '<a href="javascript: mbgBookNotifyMBGAjax('.$descr['id'].');">Inform MBG</a>';
					echo '<a href="javascript: mbgBookRefuseAjax('.$descr['id'].');">Refuse</a>';
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
