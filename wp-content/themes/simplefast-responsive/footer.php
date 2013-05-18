<div style="clear: both"></div>
</div>
<footer id="footer">
<?php $pages = wp_list_pages('depth=1&title_li=&echo=0');
$pages2 = preg_split('/(<li[^>]*>)/' ,$pages);foreach($pages2 as $var){
echo str_replace('</li>', '', $var);}?><br/>
Copyright &#169; 2012  <a href="<?php echo home_url() ; ?>"><?php bloginfo('name'); ?></a>, All trademarks are the property of the respective trademark owners. <br/>
<?php static_footer_pages(); ?>
</footer>
</div>
<script type="text/javascript">
  var vglnk = { api_url: '//api.viglink.com/api',
                key: 'c5d8f82b94ccf34b3c04d4ff2e0d11cb' };

  (function(d, t) {
    var s = d.createElement(t); s.type = 'text/javascript'; s.async = true;
    s.src = ('https:' == document.location.protocol ? vglnk.api_url :
             '//cdn.viglink.com/api') + '/vglnk.js';
    var r = d.getElementsByTagName(t)[0]; r.parentNode.insertBefore(s, r);
  }(document, 'script'));
</script>
</body>
</html>