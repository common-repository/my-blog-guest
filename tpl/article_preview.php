<h2><?php echo $article['title'] ?></h2>

<div class="mbg-preview-block">
<table class="dlg-main-table">
<tr><th>Category:</th><td><?php echo $article['category'] ?></td></tr>
<tr><th>Description:</th><td><?php echo $article['descr'] ?></td></tr>
<tr><th>Published Date:</th><td><?php echo $article['publish_date'] ?></td></tr>
<tr><th>Author:</th><td><?php echo $article['author'] ?></td></tr>
<tr><th>Pin:</th><td><?php echo $article['pin'] ?></td></tr>
<tr><th>Word Count:</th><td><?php echo $article['num_words'] ?></td></tr>
<tr><th>Author By Line:</th><td><?php echo $article['author_by_line'] ?></td></tr>
<tr><th>Preview:</th><td><?php echo $article['preview'] ?></td></tr>

<?php
	if(count($links) > 0)
	{
	echo "<tr><th>Links:</th>";
	echo "<td><ul class='links'>";
		foreach($links as $link)
		{
			if($link['self_serving'] == 1)
			$icon = "<img height='20px' src='".WPMBG_IMGS."/s.png' />";
			else
			$icon = '';
			
		echo "<li>$icon&nbsp;<a target='_blank' href='".$link['href']."'>".$link['anchor']."</a></li>";
		}
	echo "</ul></td></tr>";
	}

?>

</table>
</div>

<img style="margin-left: 200px; cursor: pointer; height: 40px;" src="<?php echo WPMBG_IMGS; ?>/offer.png" onclick="mbgArticleOfferDlg(<?php echo $id_article; ?>);"></img>
