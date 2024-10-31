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
		
		echo '<p class="category"><b>Category:</b> '.$ig['category'].'</p>';
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
				
					if(!empty($descr['offer']))
					{
					echo '<div class="ig-descr-offer"><b>Your offer:</b> '.$descr['offer'].'</div>';
					}
					else
					{
					echo '<p class="tasks-menu">';
					//echo '<a class="thickbox" title="MyGuestBlog Make Offer" href="'.WPMBG_MAKE_IG_OFFER_URL.'?id_descr='.$descr['id'].'&TB_iframe=true&height=600&width=600">Make Offer</a>';
                                        echo '<a title="MyGuestBlog Make Offer" href="javascript:void(0);" onclick="mbgIgOfferDlg('.$descr['id'].')">Make Offer</a>';
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
