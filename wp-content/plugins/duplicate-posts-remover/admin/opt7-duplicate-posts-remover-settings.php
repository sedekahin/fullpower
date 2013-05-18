	<? require_once(OPT7_PDD_PLUGINPATH . '/classes/opt7-duplicate-posts-remover-functions.php');?>
    <style>
		#opt7-plugin-table-container{
			width:100%;
		}
	    #opt7-plugin-table-container .left-col{
			width:300px;
			padding-top: 7px;
		}
		#opt7-plugin-table-container .right-col{
			vertical-align:top;
		}
		#opt7-plugin-table-container .right-col #opt7-data-table{			
		}
		#opt7-plugin-table-container #opt7-sidebar{
			width:300px;
			margin-right:20px;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu {
			width: auto;
			padding-top:0px;
			height: auto;
			-webkit-box-shadow: 0px 1px 3px 0px rgba(0,0,0,.73), 0px 0px 18px 0px rgba(0,0,0,.13);
			-moz-box-shadow: 0px 1px 3px 0px rgba(0,0,0,.73), 0px 0px 18px 0px rgba(0,0,0,.13);
			box-shadow: 0px 1px 3px 0px rgba(0,0,0,.73), 0px 0px 18px 0px rgba(0,0,0,.13);
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a {
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
			border-bottom: 1px solid;
			border-top-color: white;
			border-bottom-color: #DFDFDF;
			-webkit-box-shadow: inset 0px 1px 0px 0px #878e98;
			-moz-box-shadow: inset 0px 1px 0px 0px #878e98;
			box-shadow: inset 0px 1px 0px 0px #878e98;
			text-shadow: rgba(255, 255, 255, 0.8) 0 1px 0;
			width: 100%;
			height: 2.75em;
			line-height: 2.75em;
			text-indent: 2.75em;
			display: block;
			position: relative;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size:12px;
			text-decoration:none;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu ul li a {
			background: #fff;
			width: 100%;
			height: 2.75em;
			line-height: 2.75em;
			text-indent: 2.75em;
			display: block;
			position: relative;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 12px;
			color: #878d95;
			text-decoration:none;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu ul li:last-child a {
			border-bottom: 1px solid #DFDFDF;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a:hover, .menu > li > a.active {
			border-bottom: 1px solid #DFDFDF;
			-webkit-box-shadow: inset 0px 1px 0px 0px #DFDFDF;
			-moz-box-shadow: inset 0px 1px 0px 0px #DFDFDF;
			box-shadow: inset 0px 1px 0px 0px #DFDFDF;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a.active {
			border-bottom: 1px solid #DFDFDF;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a:before {
			content: '';
			background-image: url(../images/sprite.png);
			background-repeat: no-repeat;
			font-size: 36px;
			height: 1em;
			width: 1em;
			position: absolute;
			left: 0;
			top: 50%;
			margin: -.5em 0 0 0;
		}
		#opt7-plugin-table-container #opt7-sidebar .item1 > a:before {
			background-position: 0 0;
		}
		#opt7-plugin-table-container #opt7-sidebar .item2 > a:before {
			background-position: -38px 0;
		}
		#opt7-plugin-table-container #opt7-sidebar .item3 > a:before {
			background-position: 0 -38px;
		}
		#opt7-plugin-table-container #opt7-sidebar .item4 > a:before {
			background-position: -38px -38px;
		}
		#opt7-plugin-table-container #opt7-sidebar .item5 > a:before {
			background-position: -76px 0;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a span{
			font-size: 11px; 
			display: inline-block;
			position: absolute;
			right: 1em;
			top: 50%; 
			background: #fff;
			line-height: 1em;
			height: 1em;
			padding: .4em .6em;
			margin: -.8em 0 0 0; 
			color: #5D9732;
			text-indent: 0;
			text-align: center;
			-webkit-border-radius: .769em;
			-moz-border-radius: .769em;
			border-radius: .769em;
			-webkit-box-shadow: inset 0px 1px 3px 0px rgba(0, 0, 0, .26), 0px 1px 0px 0px rgba(255, 255, 255, .15);
			-moz-box-shadow: inset 0px 1px 3px 0px rgba(0, 0, 0, .26), 0px 1px 0px 0px rgba(255, 255, 255, .15);
			box-shadow: inset 0px 1px 3px 0px rgba(0, 0, 0, .26), 0px 1px 0px 0px rgba(255, 255, 255, .15);
			text-shadow: 0px 1px 0px rgba(0,0,0,.5);
			font-weight: 500;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > a:hover span, .menu > li a.active span {
			background: #6D6D6D;
			color:#fff;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > ul li a:before{
			content: '▶';
			font-size: 11px;
			color: #bcbcbf;
			position: absolute;
			width: 1em;
			height: 1em;
			top: 0;
			left: -2.7em;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > ul li:hover a,
		#opt7-plugin-table-container #opt7-sidebar .menu > li > ul li:hover a span,
		#opt7-plugin-table-container #opt7-sidebar .menu > li > ul li:hover a:before {
			color: #32373D;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu > li > ul li a.active span{
			color: #fff;
		}
		#opt7-plugin-table-container #opt7-sidebar .menu ul > li > a span {
			font-size: 11px; 
			display: inline-block;
			position: absolute;
			right: 1em;
			top: 50%; /
			background: #fff;
			border: 1px solid #d0d0d3;
			line-height: 1em;
			height: 1em;
			padding: .4em .7em;
			margin: -.9em 0 0 0; 
			color: #878d95;
			text-indent: 0;
			text-align: center;
			-webkit-border-radius: .769em;
			-moz-border-radius: 769em;
			border-radius: 769em;
			text-shadow: 0px 0px 0px rgba(255,255,255,.01));
		}
		#opt7-posts-search{
			padding:5px;
			font-family: "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",sans-serif;
		}
	</style>
    <div class="wrap"> 
      <div id="icon-plugins" class="icon32"><br /></div> 
      <h2>Duplicate Posts Remover - by <a href="http://www.optimum7.com/?utm_source=<?php echo OPT7_PDD_UTM_SOURCE_CODE;?>">Optimum7</a></h2> 
      <table id="opt7-plugin-table-container">
      	<tr>
        	<td class="left-col">
        		<div id="opt7-sidebar">
                  <input type="hidden" id="opt7-page-value" name="opt7-page-value" value="1"/>
                  <input type="hidden" id="opt7-limit-value" name="opt7-limit-value" value="25"/>  
                  <input type="hidden" id="opt7-post-type-value" name="opt7-post-type-value" value="post"/>
                  <input type="hidden" id="opt7-post-status-value" name="opt7-post-status-value" value=""/>
                  <input type="hidden" id="opt7-post-search-value" name="opt7-post-search-value" value=""/> 
                  <input type="hidden" id="opt7-is-duplicate-value" name="opt7-is-duplicate-value" value="0"/>                                    
                  <ul class="menu">
                     <li class="item1"><a class="active" href="javascript:void()">Posts</a>
                        <ul>
                            <li class="subitem1"><a class="active" onclick="getData('post','publish',0)" href="javascript:void()">All published posts<span id="post-all-counter">-</span></a></li>
                            <li class="subitem2"><a onclick="getData('post','trash',0)" href="javascript:void()">Posts in Trash<span id="post-trash-counter">-</span></a></li>
                        </ul>
                    </li>
                    <li class="item2"><a href="javascript:void()">Pages</a>
                        <ul style="display:none">
                            <li class="subitem1"><a href="javascript:void()" onclick="getData('page','publish',0)">All published pages<span id="page-all-counter">-</span></a></li>
                            <li class="subitem2"><a onclick="getData('page','trash',0)" href="javascript:void()">Pages in Trash<span id="page-trash-counter">-</span></a></li>
                        </ul>
                    </li>
                     <li class="item3"><a href="javascript:void()">Duplicate Content</a>
                        <ul style="display:none">
                            <li class="subitem1"><a href="javascript:void()" onclick="getData('post','publish',1)">Posts with title duplicated<span id="post-duplicated-counter">-</span></a></li>
                            <li class="subitem2"><a onclick="getData('page','publish',1)" href="javascript:void()">Pages (Title Duplicated)<span id="page-duplicated-counter">-</span></a></li>
                        </ul>
                    </li>
                 </ul>
              </div>
     		  <?php include("opt7-admin-rss-feed-head-area.php");?>
            </td>
            <td class="right-col" style="padding-top: 10px;">
            </td>
        </tr>       
      </table>
    </div>