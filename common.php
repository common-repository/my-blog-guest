<?php

//define('WPMBG_TEST_ENV', 1);

@ini_set('session.save_handler','files');

    if (!session_id())
    {
    session_start();
    }

if ( ! defined( 'WPMBG_BASE_URL' ) )
{
	if(defined('WPMBG_TEST_ENV')) {

	//define( 'WPMBG_BASE_URL', 'http://apitest.myblogguest.com' );
        define( 'WPMBG_BASE_URL', 'http://api' );
	}
	else
        {
	define( 'WPMBG_BASE_URL', 'http://api.myblogguest.com' );
        }
}

define('MBG_URL', 'http://myblogguest.com');

function aaa_result($res, $action, $args) {
	print_r($res);
	return $res;
}

	// mb_string 
	
	if(!function_exists('mb_strtolower'))
	{
		function mb_strtolower($str)
		{
		return strtolower($str);
		}
	}

		// полономочие, необходимое для использования плагина
	if(!defined('WPMBG_REQUIRED_CAPABILITY'))
	{
	define('WPMBG_REQUIRED_CAPABILITY', 'publish_posts');
	}

	// find post by title
	
	// find post by title


function mbgGetPostByTitle($page_title, $output = OBJECT) {
    global $wpdb;
	
    $page_title = '%'.trim($page_title).'%';
    $page_title = str_replace(array('\'', '`', '"', '&#146;', '&#145;', '&#039;', '&rdquo;', '&ldquo;', '&rsquo;', '&lsquo;', '&amp;ldquo;', '&amp;rdquo;', '&amp;lsquo;', '&amp;rsquo;', '“', '”', '–', '-'), '%', $page_title);

    $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title like %s AND post_type='post' AND post_status != 'inherit'", $page_title ));

        if($post)
        {
        return get_post($post, $output);
        }

    return null;
}

function mbg_download_file($url, $local_path)
{
set_time_limit(0); // unlimited max execution time

$options = array(
  CURLOPT_FILE    => fopen($local_path, 'w+'),
  CURLOPT_TIMEOUT => 28800, // set this to 8 hours so we dont timeout on big files
  CURLOPT_URL     => $url,
);

$ch = curl_init();
curl_setopt_array($ch, $options);

//curl_setopt($ch, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

curl_exec($ch);

$responseInfo = curl_getinfo($ch);
curl_close($ch);

return $responseInfo['http_code'];
}

function process_http($url, &$http_code = null, $postdata = false){


$user_agents = array(
	'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.30 Safari/525.13',
	'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)',
	'Mozilla/4.0 (compatible; MSIE 8.0; AOL 9.5; AOLBuild 4337.43; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.21022; .NET CLR 3.5.30729; .NET CLR 3.0.30618)',
	'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) Gecko/20121223 Ubuntu/9.25 (jaunty) Firefox/3.8',
	'Mozilla/5.0 (Windows; U; Windows NT 5.2; zh-CN; rv:1.9.2) Gecko/20100101 Firefox/3.6',
	'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322)',
	'Mozilla/5.0 (Windows; U; Windows NT 6.1; ko-KR) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10',
);


$curl_conn = curl_init();

shuffle($user_agents);

curl_setopt($curl_conn, CURLOPT_URL, $url); //URL to connect to

	if($postdata)
	{
	curl_setopt($curl_conn, CURLOPT_POST, true);
      	curl_setopt($curl_conn, CURLOPT_POSTFIELDS, $postdata);
      	curl_setopt($curl_conn, CURLOPT_CUSTOMREQUEST, 'POST');
	}

curl_setopt($curl_conn, CURLOPT_CONNECTTIMEOUT, 20);
//curl_setopt($curl_conn, CURLOPT_LOW_SPEED_LIMIT, 1024);
//curl_setopt($curl_conn, CURLOPT_LOW_SPEED_TIME, 5);
curl_setopt($curl_conn, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($curl_conn, CURLOPT_FOLLOWLOCATION, 1);

$user_agent = $user_agents[0];
curl_setopt($curl_conn, CURLOPT_USERAGENT, $user_agent);

curl_setopt($curl_conn, CURLOPT_SSL_VERIFYPEER, false); 	//Do not check SSL certificate (but use SSL of course), live dangerously!

	// Result from querying URL. Will parse as xml
$output = curl_exec($curl_conn);

//var_dump($output);

	// close cURL resource. It's like shutting down the water when you're brushing your teeth.
//var_dump($output);			
$responseInfo = curl_getinfo($curl_conn);
curl_close($curl_conn);

$http_code = $responseInfo['http_code'];

	if($responseInfo['http_code'] != 200)
	return false;

	if($output && trim($output) != '')
	return $output;
	else
	return false;
}

		/*
		Выполняет запрос к MBG API
		*/
function mbg_api($url, $data = null, $silent = false, $method = 'POST')
{
$ret = '';

$options = get_option('wpmbg_options');
$token = $options['wpmbg_oauth_token'];

	if ($token == "") { 
	return "This plugin has yet to be authorized, please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>initialize plugin</a> for accept connecting to MyBlogGuest!";
	}
	elseif (!token_okay($token)) {
	return "Your sites authorization has either expired, or there was a problem communicating with MyBlogGuest. Please try again, if this problem presists please <a href='".  get_admin_url() ."admin.php?page=wpmbg_settings'>renew access token</a>";
	}

$request = new WP_Http;	

	if($data == null)
	{
	$data = array();
	}
	
$body = array_merge($data, array("oauth_token" => $token, "addition_info" => array('plugin_version' => WPMBG_VERSION)));
//print_r($body);

$result = $request->request($url, array('method' => $method, 'body' =>$body));
//var_dump($result);
	if (isset($result->errors)) {
		// display error message of some sort
		$error =  "There was a  problem contacting the server!";
	} else {
		//$result['body'] = '{"errors":{"text":"Bad Request!", "code": 9900}}';
		$arr = json_decode($result['body'], true);
		
			if(empty($arr))
			{
			$error = 'Bad response format!';
			}
			if(!empty($arr['errors']))
			{
                            if(isset($arr['errors']['text']))
                            {
                            $error = $arr['errors']['text'];
                            }
                            else if(isset($arr['errors'][0]['text']))
                            {
                            $error = $arr['errors'][0]['text'];
                            }
			}
			else
			{
			$ret = $arr;
			
				if(!empty($arr['msg']) && !$silent)
				{
				showMessage($arr['msg']);
				}
			}
	}
	
return ((!empty($error)) ? $error : $ret);
}


function array_to_obj($array, &$obj = null)
{
	if(!is_object($obj))
	{
	$obj = (object) array();
	}
	
	foreach ($array as $key => $value)
	{
		if (is_array($value))
		{
		$obj->$key = new stdClass();
		array_to_obj($value, $obj->$key);
		}
		else
		{
		$obj->$key = $value;
		}
	}
return $obj;
}

	/*
	возвращает переданные этому пользователю, но еще неопубликованные статьи и инфографики
	
		array(
		     'created' => <timestamp when cache created>,
		     
		     'article_available' => array(
		     		
		     				 ),
		     'article_dp' => array(
		     		
		     				 ),
		     'ig_available' => array(
		     		
		     				 ),
		     )
	*/
function mbgGetGTM()
{
$gtm 			= get_option('wpmbg_gtm_cache');

	if(empty($gtm) || empty($gtm['created']) 
			|| ((intval($gtm['created']) + 1800) < time())
			|| (isset($_GET['page']) && ($_GET['page'] == 'ig_management' || $_GET['page'] == 'article_management'))
		)
	{
	return mbgUpdateGTMCache();
	}
	else
	{
	return $gtm;
	}
}

	/*
	Получает с MBG первую переданную на данный сайт статью, статью отправленную на данный сайт через DirectPost
	и первую переданную для публикации на данный сайт инфографику
	
	сохраняет эти данные в wpmbg_gtm_cache и возвращает в виде массива
	*/
function mbgUpdateGTMCache()
{
$options = get_option('wpmbg_options');
$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;

$ret = array('created' => time());

$articles = get_articles_given_to_me($options, "", -1, 100, 0, $id_site, "", "", "2,4");

// search through all article to see if status is set to 2
	if (!empty($articles) && isset($articles['articles']) && is_array($articles['articles'])) {
		foreach ($articles['articles'] as $article)
		{
			if ($article['status'] == 2)
			{
			$ret['article_available'] = $article;
			}
				
			if ($article['status'] == 4)
			{
			$ret['article_dp'] = $article;
			
                        $post = mbgGetPostByTitle($article['title']);

				if(!empty($post) && $post->post_status == 'draft')
				{
                                $post->edit_date = true;
                                $post->post_date = '0000-00-00 00:00:00';
                                $post->post_date_gmt = '0000-00-00 00:00:00';
                                wp_update_post($post);
				}

				// отмечаем эту статью в $options['published_articles'], если вдруг этого не было сделано раньше
		
				if(empty($options['published_articles']))
				{
				$options['published_articles'] = array();
				}
				
				if(empty($options['published_articles'][$article['id']]))
				{
					if(!empty($post))
					{
					$options['published_articles'][$article['id']] = $post->ID;
					update_option('wpmbg_options', $options);
					}
				}
			}
		}	// end foreach		
	}


        // Infographics
        
$url = WPMBG_BASE_URL . "/given_to_me_ig";	
			
$body = array("num" => 1,
	      "id_site" => $id_site,
	      "status"  => 2,
	      );

$res = mbg_api($url, $body);
	
    	if(!is_string($res))
    	{
    	$ig = ((isset($res['infographics']) && isset($res['infographics'][0])) ? $res['infographics'][0] : null);

    		if(is_array($ig))
    		{
    			foreach($ig['descriptions'] as $descr)
    			{
    				if($descr['status'] == 2)
    				{
    				$descr['id_category'] = $ig['id_category'];
    				$descr['authorship_name'] = isset($ig['authorship_name']) ? $ig['authorship_name'] : null;
    				$descr['authorship_email'] = isset($ig['authorship_email']) ? $ig['authorship_email'] : null;
    				$ret['ig_available'] = $descr;
    				break;
    				}
    			}
    		}
    	}
	 


        // eBooks
        
$url = WPMBG_BASE_URL . "/given_to_me_books";	
			
$body = array("num" => 1,
	      "id_site" => $id_site,
	      "status"  => 2,
	      );

$res = mbg_api($url, $body);
	
    	if(!is_string($res))
    	{
    	$book = ((isset($res['books']) && isset($res['books'][0])) ? $res['books'][0] : null);

    		if(is_array($book))
    		{
    			foreach($book['descriptions'] as $descr)
    			{
    				if($descr['status'] == 2)
    				{
    				$descr['id_category'] = $book['id_category'];
    				$descr['authorship_name'] = isset($book['authorship_name']) ? $book['authorship_name'] : null;
    				$descr['authorship_email'] = isset($book['authorship_email']) ? $book['authorship_email'] : null;
    				$ret['book_available'] = $descr;
    				break;
    				}
    			}
    		}
    	}
        
//add_option('wpmbg_gtm_cache', $ret, '', 'yes');
update_option('wpmbg_gtm_cache', $ret);
	
return $ret;
}

    /*
     *  Получает связанный с этим блогом реквест и записывает его в кеш и возвращает
     */
function mbgUpdateRequestCache()
{
$options = get_option('wpmbg_options');
$id_site = isset($options['wpmbg_id_site']) ? intval($options['wpmbg_id_site']) : 0;

$ret = array('created' => time());
$url = WPMBG_BASE_URL . "/article_request/".$id_site;
$request = mbg_api($url);

$ret['request'] = $request;
    
update_option('wpmbg_request_cache', $ret);
	
return $ret;
}


function mbgClearRequestCache()
{
update_option('wpmbg_request_cache', '');
}

    /*
     *  Возвращает реквест, привязанный к данному сайту, берёт его из кеша или c МБГ
     */
function mbgGetRequest()
{
$req 			= get_option('wpmbg_request_cache');

	if(empty($req) || empty($req['created']) 
			|| ((intval($req['created']) + 1800) < time())
			|| (isset($_GET['page']) && $_GET['page'] == 'articles_request')
		)
	{
	$req = mbgUpdateRequestCache();
	}
        
return (isset($req['request']) ? $req['request'] : null);
}


	// удаляет из wordpress пост, созданный из MBG статьи $id_article
	
function mbgDelDraftArticle($id_article)
{
$options = get_option('wpmbg_options');

	if(!empty($options['published_articles']) && !empty($options['published_articles'][$id_article]))
	{
	$post_id = $options['published_articles'][$id_article];
	$post = get_post($post_id, OBJECT);
		
		if($post && ($post->post_status == 'draft' || $post->post_status == 'trash'))
		{
			if(wp_delete_post($post_id, true) !== false)
			{
			unset($options['published_articles'][$id_article]);
			update_option('wpmbg_options',$options);
			}
		}
	}
}

	// удаляет из wordpress пост, созданный из MBG описания инфографики $id_descr
	
function mbgDelDraftIgDescr($id_descr)
{
$options = get_option('wpmbg_options');

	if(!empty($options['published_ig_descr']) && !empty($options['published_ig_descr'][$id_descr]))
	{
	$post_id = $options['published_ig_descr'][$id_descr];
	$post = get_post($post_id, OBJECT);
		
		if($post && ($post->post_status == 'draft' || $post->post_status == 'trash'))
		{
			if(wp_delete_post($options['published_ig_descr'][$id_descr], true) !== false)
			{
			unset($options['published_ig_descr'][$id_descr]);
			update_option('wpmbg_options', $options);
			}
		}
	}
}


	// удаляет из wordpress пост, созданный из MBG описания книги $id_descr
	
function mbgDelDraftBookDescr($id_descr)
{
$options = get_option('wpmbg_options');

	if(!empty($options['published_books_descr']) && !empty($options['published_books_descr'][$id_descr]))
	{
	$post_id = $options['published_books_descr'][$id_descr];
	$post = get_post($post_id, OBJECT);
		
		if($post && ($post->post_status == 'draft' || $post->post_status == 'trash'))
		{
			if(wp_delete_post($options['published_books_descr'][$id_descr], true) !== false)
			{
			unset($options['published_ig_descr'][$id_descr]);
			update_option('wpmbg_options', $options);
			}
		}
	}
}


function syncPosts()
{
$options = get_option('wpmbg_options');

	if(!empty($options['sync_date']) && (($options['sync_date'] + 7200) > time()))
	{
	return;
	}
	
		// синхронизируем посты, на основе статей с MBG
		
	if(!empty($options['published_articles']))
	{
	$arr = array();
	
		foreach($options['published_articles'] as $id_article => $post_id)
		{
		$arr[] = $id_article;
		}
		
	$articles_list = implode(',', $arr);
	
	$url = WPMBG_BASE_URL . "/get_articles_list";
	$res = mbg_api($url, array('articles_list' => $articles_list));
	
		if(is_array($res) && $res['count'] > 0)
		{
			foreach($res['articles'] as $article)
			{
				if(($article['status'] != 2 && $article['status'] != 3 && $article['status'] != 4)
					|| (!empty($article['id_user_blogger']) && $article['id_user_blogger'] != $res['id_curr_user']))
				{
				mbgDelDraftArticle($article['id']);
				}
			}
		}
		
			/* удаляем из published_articles ссылки на дохлые или уже опубликованные статьи */
		foreach($options['published_articles'] as $id_article => $post_id)
		{
		$post = get_post($post_id, OBJECT);
		
			if(!$post || ($post->post_status != 'draft' && $post->post_status != 'trash'))
			{
			unset($options['published_articles'][$id_article]);
			}
		}
	}
	
		
		// синхронизируем посты, на основе инфографик с MBG
		
	if(!empty($options['published_ig_descr']))
	{
	$arr = array();
	
		foreach($options['published_ig_descr'] as $id_descr => $post_id)
		{
		$arr[] = $id_descr;
		}
		
	$descr_list = implode(',', $arr);
	
	$url = WPMBG_BASE_URL . "/get_ig_descr_list";
	$res = mbg_api($url, array('descr_list' => $descr_list));
	
		if(is_array($res) && $res['count'] > 0)
		{
			foreach($res['descrs'] as $descr)
			{
				if(($descr['status'] != 2 && $descr['status'] != 3)
					|| (!empty($descr['id_user_blogger']) && $descr['id_user_blogger'] != $res['id_curr_user']))
				{
				mbgDelDraftIgDescr($descr['id']);
				}
			}
		}
		
			/* удаляем из published_ig_descr ссылки на дохлые или уже опубликованные статьи */
		foreach($options['published_ig_descr'] as $id_descr => $post_id)
		{
		$post = get_post($post_id, OBJECT);
		
			if(!$post || ($post->post_status != 'draft' && $post->post_status != 'trash'))
			{
			unset($options['published_ig_descr'][$id_descr]);
			}
		}
	}
        
        
		// синхронизируем посты, на основе книг с MBG
		
	if(!empty($options['published_books_descr']))
	{
	$arr = array();
	
		foreach($options['published_books_descr'] as $id_descr => $post_id)
		{
		$arr[] = $id_descr;
		}
		
	$descr_list = implode(',', $arr);
	
	$url = WPMBG_BASE_URL . "/get_books_descr_list";
	$res = mbg_api($url, array('descr_list' => $descr_list));
	
		if(is_array($res) && $res['count'] > 0)
		{
			foreach($res['descrs'] as $descr)
			{
				if(($descr['status'] != 2 && $descr['status'] != 3)
					|| (!empty($descr['id_user_blogger']) && $descr['id_user_blogger'] != $res['id_curr_user']))
				{
				mbgDelDraftBookDescr($descr['id']);
				}
			}
		}
		
			/* удаляем из published_books_descr ссылки на дохлые или уже опубликованные статьи */
		foreach($options['published_books_descr'] as $id_descr => $post_id)
		{
		$post = get_post($post_id, OBJECT);
		
			if(!$post || ($post->post_status != 'draft' && $post->post_status != 'trash'))
			{
			unset($options['published_books_descr'][$id_descr]);
			}
		}
	}
	
$options['sync_date'] = time();
update_option('wpmbg_options', $options);

mbgUpdateGTMCache();
}

function token_okay($oauth_token)
{
	$okay = 1;
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC."class-http.php");
	}

	$url = WPMBG_BASE_URL . "/categories?oauth_token=$oauth_token";	
	
			
	$request = new WP_Http;
	$result = $request->request($url);
	$html_content = array();
	
	if (isset($result->errors)) {
		// display error message of some sort
		$okay = 0;
	} else {
		$html_content = $result['body'];
	}

	if ($html_content == "") { $okay = 0; }
	
	return $okay;
}	

function mbgRunTpl($tpl, $params = null)
{

	
$tpl_path = WPMBG_PLUGIN_DIR.'/tpl/'.$tpl.'.php';
	
    if(!empty($params))
    {
	foreach($params as $var => $val)
	{
	$$var = $val;
	}
    }
    
ob_start();
include $tpl_path;
$ret = ob_get_clean();

	if(strpos($ret, chr(239).chr(187).chr(191)) === 0)
	$ret = substr($ret, 3);

return trim($ret, ' ');
}


    // сравнение заголовков статей
function cmpTitle($title1, $title2)
{
$title1 = str_replace(array('\'', '`', '"', '&#146;', '&#145;', '&#039;', '&rdquo;', '&ldquo;', '&rsquo;', '&lsquo;', '&amp;ldquo;', '&amp;rdquo;', '&amp;lsquo;', '&amp;rsquo;', '“', '”', '-'), '', $title1);
$title2 = str_replace(array('\'', '`', '"', '&#146;', '&#145;', '&#039;', '&rdquo;', '&ldquo;', '&rsquo;', '&lsquo;', '&amp;ldquo;', '&amp;rdquo;', '&amp;lsquo;', '&amp;rsquo;', '“', '”', '-'), '', $title2);

$title1 = trim(mb_strtolower($title1));
$title2 = trim(mb_strtolower($title2));

return ($title1 == $title2);
}

