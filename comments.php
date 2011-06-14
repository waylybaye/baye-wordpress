<?php
global $BM_API_KEY;
global $BM_TOKEN;
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$SPIDERS = array("YodaoBot", "Googlebot", "Yahoo!", "Baiduspider", "msnbot", "Sogou", "Soso");
$is_spider = false;
foreach( $SPIDERS as $spider ){
    if( strpos($user_agent, $spider) > -1 ){
        $is_spider = true;
        break;
    }
}

$seo_open = get_option("bm_seo_open");

if( $seo_open == "1" && $is_spider ){
    $params = "apikey=$BM_API_KEY&token=$BM_TOKEN&identify=entry_".get_the_ID();
    echo http_get("/wordpress/load_comments", $params);
}else{
?>
    <div id="bm_comments">
    </div>
    <script type="text/javascript"> 
        var bm_apikey = "<?php echo get_option("bm_api_key") ?>";
        var bm_identify = "entry_<?php echo get_the_ID(); ?>";
        var bm_url = "<?php echo get_permalink(); ?>";
        var bm_title = "<?php echo get_the_title(); ?>";
        var script = document.createElement('script');
        script.type = "text/javascript";
        script.src = "http://baye.me/embed.js?f=wordpress";
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(script);
    </script> 

<?php }  ?>
