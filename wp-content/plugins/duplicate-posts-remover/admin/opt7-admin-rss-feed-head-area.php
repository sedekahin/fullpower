<style>
#opt7-admin-rss-container{
	width:300px;
	min-height:350px;
	margin-top:20px;
	float:left;
	box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, .73), 0px 0px 18px 0px rgba(0, 0, 0, .13);
}
#opt7-admin-rss-container opt7-admin-posts-col-left li{
	list-style:none;	
}
#opt7-admin-rss-container li span {
	color: #6D6D6D;
	display: block;
	float: left;
	font-family: Georgia;
	font-size: 1.8em;
	font-weight: 700;
	height: 25px;
	line-height: 25px;
	margin-right: 5px;
	text-align: center;
	vertical-align: middle;
	width: 25px;
	position: relative;
	top: -5px;
}
#opt7-admin-rss-container .opt7-admin-rss-head{
	width:100%;
	height:40px;
	background-color: #F1F1F1;
	background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -o-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));
	background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: linear-gradient(top,#F9F9F9,#ECECEC);
	border-bottom: 1px solid #ECECEC;
	-webkit-box-shadow: inset 0px 1px 0px 0px #ECECEC;
	-moz-box-shadow: inset 0px 1px 0px 0px #ECECEC;
	box-shadow: inset 0px 1px 0px 0px #ECECEC;
}
#opt7-admin-rss-container .opt7-admin-rss-head .opt7-admin-rss-head-title-container{
	height:40px;
	margin-left:10px;
}
#opt7-admin-rss-container .opt7-admin-rss-head .opt7-admin-rss-head-title-container .title{
	 color:#000;
	 font-size:14px;
	 vertical-align:middle;
	 line-height:40px; 
	 font-weight:700;
}
#opt7-admin-rss-container #opt7-admin-posts-container{
	width:100%;
	margin-bottom:20px;
}
#opt7-admin-rss-container #opt7-admin-posts-container .opt7-admin-rss-head-links-container{
	height:20px;
}
#opt7-admin-rss-container .opt7-admin-rss-head-links-container ul li{
  float:left;
  margin-left:5px;   
}
#opt7-admin-rss-container #opt7-admin-posts-container .opt7-admin-posts-col-left li{
	margin:10px;
}
</style>
<div id="opt7-admin-rss-container">
	<div class="opt7-admin-rss-head">
    	<div class="opt7-admin-rss-head-title-container">
        	<span class="title"><img style="margin-right:5px;" src="<?php echo get_bloginfo('siteurl').'/wp-content/plugins/duplicate-posts-remover/images/icon.gif'?>"/>Optimum7 RSS</span>
        </div>
    </div>
    <div id="opt7-admin-posts-container">  
       <div class="opt7-admin-rss-head-links-container">
        	<ul>
              <li><?php echo sprintf(__( "%sGet a free Web site Analysis%s", "Optimum7" ), '<a href="http://www.optimum7.com/alpha/seo/main-analysis-bullets-3.html?utm_source='.OPT7_PDD_UTM_SOURCE_CODE.'" target="_blank">','</a>'); ?></li>
              <li> | </li>
              <li><?php echo sprintf(__( "%sHow this plugin works%s", OPT7_PDD_PLUGINNAME ), '<a href="'.OPT7_PDD_PLUGINSUPPORT_PATH.'" target="_blank">','</a>'); ?></li>
            </ul>
        </div>
       <div class="opt7-admin-posts-col-left">
        <?php
          $functions = new opt7_duplicate_posts_remover_functions();
			try{
				$result =  $functions->get_rss_feed(ini_get('allow_url_fopen'));
				echo $result;
			}
			catch (Exception $e){	
			   echo $e->getMessage();
			}
       ?>  
       </div>  	      
    </div>
</div>