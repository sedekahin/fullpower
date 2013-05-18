<?php

function wpr_ab_curl_post($url, $data, &$info){

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, wpr_ab_curl_postData($data));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_REFERER, $url);
  $html = trim(curl_exec($ch));
  curl_close($ch);

  return $html;
}

function wpr_ab_curl_postData($data){

  $fdata = "";
  foreach($data as $key => $val){
    $fdata .= "$key=" . urlencode($val) . "&";
  }

  return $fdata;
}

function wpr_articlebuilderpost($keyword,$num,$start) {

	$keyword = explode(">", $keyword);
	$category = str_replace('"', "", $keyword[0]);
	$kws = explode(",", $keyword[1]);	
	
	if(empty($category)) {
		$return["error"]["module"] = "Article Builder";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = __("No keyword specified.","wprobot");
		return $return;	
	}	
	$allcats = array("affiliate marketing","article marketing","email marketing","forex","home business","internet marketing","mobile marketing","network marketing","search engine optimization","social media marketing","credit cards","credit repair","insurance - auto","insurance - general","insurance - life","personal bankruptcy","personal finance","real estate - buying","real estate - commercial","stock market","acne","aging","allergies","anxiety","arthritis","asthma","back pain","beauty","cancer","cosmetic surgery","depression","diabetes","fitness","hair care","hair loss","hemorrhoids","insurance - health","juicing","memory","muscle building","nutrition","panic attacks","personal development","quit smoking","skin care","snoring","stress","teeth whitening","tinnitus","weight loss","cooking","dog training","gardening","home improvement","insurance - home owner's","landscaping","organic gardening","parenting","plumbing","pregnancy","fishing","golf","photography","travel","jewelry","real estate - selling","weddings","blogging","green energy","web design","web hosting","college","homeschooling","coupons","payday loans","coffee","sleep apnea","yeast infection","interior design","camping","video games","fashion","iphone");

	if(!in_array($category, $allcats)) {
		$return["error"]["module"] = "Article Builder";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = __('Article Builder only supports a defined lists of categories which can be used as keywords in WP Robot. Please enter one of the following: "affiliate marketing","article marketing","email marketing","forex","home business","internet marketing","mobile marketing","network marketing","search engine optimization","social media marketing","credit cards","credit repair","insurance - auto","insurance - general","insurance - life","personal bankruptcy","personal finance","real estate - buying","real estate - commercial","stock market","acne","aging","allergies","anxiety","arthritis","asthma","back pain","beauty","cancer","cosmetic surgery","depression","diabetes","fitness","hair care","hair loss","hemorrhoids","insurance - health","juicing","memory","muscle building","nutrition","panic attacks","personal development","quit smoking","skin care","snoring","stress","teeth whitening","tinnitus","weight loss","cooking","dog training","gardening","home improvement","insurance - home owner\'s","landscaping","organic gardening","parenting","plumbing","pregnancy","fishing","golf","photography","travel","jewelry","real estate - selling","weddings","blogging","green energy","web design","web hosting"',"wprobot");
		return $return;		
	}
	
	$options = unserialize(get_option("wpr_options"));	
	$template = "{article}";

	$url = 'http://articlebuilder.net/api.php';

	$data = array();
	$data['action'] = 'authenticate';
	$data['format'] = 'php';
	$data['username'] = $options['wpr_ab_email'];
	$data['password'] = $options['wpr_ab_pw'];

	$output = unserialize(wpr_ab_curl_post($url, $data, $info));

	if($output['success']=='true'){
		$session = $output['session'];

		$data = array();
		$data['session'] = $session;
		$data['format'] = 'php';
		$data['action'] = 'buildArticle';  
		$data['apikey'] = $apikey;
		$data['category'] = $category;

		$subs = "";
		if(is_array($kws)) {
			foreach($kws as $kw) {
				$subs .= $kw . "\n";
			}
		}
		$data['subtopics'] = $subs;
		$data['wordcount'] = $options['wpr_ab_wordcount'];
		$data['superspun'] = $options['wpr_ab_superspun'];
//print_r($data);
		$posts = array();
		for ($i = 0; $i < $num; $i++) {

			$output = wpr_ab_curl_post($url, $data, $info);
			$output = unserialize($output);
			
			if($output['success']=='true'){
			
				$arts = preg_split('/\r\n|\r|\n/', $output['output'], 2);
				$art = str_replace("\r", "<br>", str_replace("\n\n", "<p>", $arts[1]));
				
				$title = $arts[0];
				
				$post = $template;				
				$post = wpr_random_tags($post);		

				$post = str_replace("{article}", $art, $post);							
		
				if(function_exists("wpr_translate_partial")) {
					$post = wpr_translate_partial($post);
				}
				if(function_exists("wpr_rewrite_partial")) {
					$post = wpr_rewrite_partial($post,$options);
				}			
					
				$posts[$i]["unique"] = rand(0, 999999);
				$posts[$i]["title"] = $title;
				$posts[$i]["content"] = $post;	
			} else {
				$return["error"]["module"] = "Article Builder";
				$return["error"]["reason"] = "No keyword";
				$return["error"]["message"] = $output["error"];
				return $return;	
			}
		}
		if(empty($posts)) {
			$posts["error"]["module"] = "Article Builder";
			$posts["error"]["reason"] = "No content";
			$posts["error"]["message"] = __("No Article Builder content found.","wprobot");	
			return $posts;			
		} else {
			return $posts;	
		}			
	} else {
		$return["error"]["module"] = "Article Builder";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = $output["error"];
		return $return;	
	}	
}

function wpr_articlebuilder_options_default() {
	$options = array(
		"wpr_ab_email" => "",	
		"wpr_ab_pw" => "",
		"wpr_ab_wordcount" => 300,
		"wpr_ab_superspun" => 0,
	);
	return $options;
}

function wpr_articlebuilder_options($options) {
	?>
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Article Builder Options","wprobot") ?></h3>	
	
	<p><i>Important: <a href="http://paydotcom.net/r/114431/thoefter/26922760/" target="_blank">ArticleBuilder.net Account required</a>, please see instructions on <a href="http://wprobot.net/documentation/#97" target="_blank">how to use this module</a>!</i></p>	
	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 
			<tr <?php if($options['wpr_ab_email'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Article Builder Username:","wprobot") ?></td> 
				<td><input size="40" name="wpr_ab_email" type="text" id="wpr_ab_email" value="<?php echo $options['wpr_ab_email'] ;?>"/>
				<!--Tooltip--><a target="_blank" class="tooltip" href="http://paydotcom.net/r/114431/thoefter/26922760/">?<span><?php _e('This setting is required for the Article Builder module to work!<br/><br/><b>Click to go to the Article Builder sign up page!</b>',"wprobot") ?></span></a>
			</td> 
			</tr>	
			<tr <?php if($options['wpr_ab_pw'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Article Builder Password:","wprobot") ?></td> 
				<td><input size="40" name="wpr_ab_pw" type="text" id="wpr_ab_pw" value="<?php echo $options['wpr_ab_pw'] ;?>"/>
			</td> 
			</tr>				
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e('SuperSpun Content:',"wprobot") ?></td> 
				<td><input name="wpr_ab_superspun" type="checkbox" id="wpr_ab_superspun" value="1" <?php if ($options['wpr_ab_superspun']=='1') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Article Length:","wprobot") ?></td> 
				<td>
				<select name="wpr_ab_wordcount" id="wpr_ab_wordcount">
							<option value="300" <?php if($options['wpr_ab_wordcount']=="300"){_e('selected');}?>><?php _e("300","wprobot") ?></option>
							<option value="400" <?php if($options['wpr_ab_wordcount']=="400"){_e('selected');}?>><?php _e("400","wprobot") ?></option>
							<option value="500" <?php if($options['wpr_ab_wordcount']=="500"){_e('selected');}?>><?php _e("500","wprobot") ?></option>
							<option value="600" <?php if($options['wpr_ab_wordcount']=="600"){_e('selected');}?>><?php _e("600","wprobot") ?></option>
							<option value="700" <?php if($options['wpr_ab_wordcount']=="700"){_e('selected');}?>><?php _e("700","wprobot") ?></option>
							<option value="800" <?php if($options['wpr_ab_wordcount']=="800"){_e('selected');}?>><?php _e("800","wprobot") ?></option>
							<option value="900" <?php if($options['wpr_ab_wordcount']=="900"){_e('selected');}?>><?php _e("900","wprobot") ?></option>
							<option value="1000" <?php if($options['wpr_ab_wordcount']=="1000"){_e('selected');}?>><?php _e("1000","wprobot") ?></option>
				</select>
			</td> 
			</tr>			
		</table>	
	<?php
}
?>