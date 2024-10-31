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
		
		echo '<p class="category"><b>Category:</b> '.$book['category'].'</p>';
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
				
					if(!empty($descr['offer']))
					{
					echo '<div class="book-descr-offer"><b>Your offer:</b> '.$descr['offer'].'</div>';
					}
					else
					{
					echo '<p class="tasks-menu">';
					//echo '<a class="thickbox" title="MyGuestBlog Make Offer" href="'.WPMBG_MAKE_BOOK_OFFER_URL.'?id_descr='.$descr['id'].'&TB_iframe=true&height=600&width=600">Make Offer</a>';
                                        echo '<a title="MyGuestBlog Make Offer" href="javascript:void(0);" onclick="mbgBookOfferDlg('.$descr['id'].')">Make Offer</a>';
					echo '</p>';
					}
					
				echo '</div>';
				$descr_count++;
				}
			}
			
		echo '</td>';
		echo '</tr>';
		}
	echo '</table>';
	}
