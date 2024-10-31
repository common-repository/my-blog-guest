<?php
function get_list_of_blogs()
{
	$url = WPMBG_BASE_URL . "/my_blogs";	
			
	$body = array("status" => 2);
	
	$res = mbg_api($url, $body);
	
	if (is_string($res)) {
		// display error message of some sort
		echo "something went wrong: '$res'";
		return false;
	} else {
		//$res = str_replace("<br />", "", $res);
		return $res;
	}

}

function display_list_of_blogs($blog_sites,$options)
{
	$current_id_site = $options['wpmbg_id_site'];
?>
	<select name="wpmbg_id_site">
    	<option value="">------------- SELECT A BLOG -------------</option>
         <?php foreach ($blog_sites->blogs as $site)
		 { 
		  echo "<option value='". $site->id ."'";
		  if ($site->id == $current_id_site) { echo " SELECTED "; }
		  echo " >". $site->name."</option>\n";
		 } 
		 ?> 
    </select>
<?php		

}
?>
