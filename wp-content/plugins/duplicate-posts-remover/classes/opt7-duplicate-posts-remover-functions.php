<?
  class opt7_duplicate_posts_remover_functions{
	   //rss feed function
	   public function get_rss_feed($allow_url_fopen){
			try{
				$result ='';
				if ($allow_url_fopen==1){
					$xmlDoc = new DOMDocument();
					$xmlDoc->load(OPT7_PDD_RSS_URL);
					//get elements from "<channel>"
					$channel=$xmlDoc->getElementsByTagName('channel')->item(0);
					$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
					$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
					$channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
					//output elements from "<channel>"
					$result = $result."<p style='margin-left:10px;'><a href='http://www.optimum7.com/internet-marketing/?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'>" . $channel_title . " (Optimum7)</a>";
					$result = $result."<br />";
					$result = $result.$channel_desc."</p>";
					//get and output "<item>" elements
					$result = $result."<ul>";
					for ($i=0; $i<OPT7_PDD_RSS_LINKS; $i++){
						  if (($i%2)==0)
						   	 $class="even";
						  else
						  	 $class="";
						  
						  $x=$xmlDoc->getElementsByTagName('item');
						  $item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
						  $item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
						  $item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
						  $result = $result."<li class='".$class."'><span>".($i+1)."</span><a href='" . $item_link."?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'>" . $item_title . "</a></li>";
					}
				}
				else{
					$result = "<li><a href='http://www.optimum7.com/contact-us?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'> Plugin Support</a></li>";	
					$result = $result."<li><a href='http://www.optimum7.com/alpha/seo/main-analysis-bullets-2.html?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'> Website Analysis </a></li>";	
					$result = $result."<li><a href='http://www.optimum7.com/?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'> Internet Marketing Services </a></li>";	
					$result = $result."<li><a href='http://www.optimum7.com/internet-marketing/?utm_source=".OPT7_PDD_UTM_SOURCE_CODE."'> Internet Marketing Articles </a></li>";	
				}
				$result = $result."</ul>";
				return $result;
			}
			catch (Exception $e){	
			}
	}
 }
?>