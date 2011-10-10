<?php
/*
Plugin Name: BAYE.ME Social Comments
Plugin URI: http://baye.me
Description: BAYE.ME 社会化评论
Version: 0.15
Author: Wayly.baye
Author URI: http://baye.me
*/
?>
<?php
$BM_API_KEY = get_option('bm_api_key');
$BM_API_SECRET = get_option('bm_api_secret');
$BM_TOKEN = md5($BM_API_KEY . $BM_API_SECRET);

function http_get($path, $params){
    $fp = fsockopen("baye.me", 80, $errno, $errstr, 10) or exit($errstr."--->".$errno);

    //$length = strlen($params);
    //构造HTTP Header
    $header = "GET $path?$params HTTP/1.1\r\n";
    $header .= "Host: baye.me\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    //$header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    //添加GET Query
    //$header .= $params."\r\n";
    //发送GET 请求
    //echo $header . "<br/";
    fputs($fp, $header);
    while( !feof($fp) ){
        $ret .= fgets($fp);
    }
    fclose($fp);
    return substr($ret, strpos($ret, "\r\n\r\n") + 4 );
    return $ret;
}
function bm_get_plugin_path($file){
    $path = dirname($file);
    $arr = preg_split('/plugins/', $path);
    return $arr[1];
    print_r('start-' . $arr[1]. '-end');
}

function bm_comments_open($open){
    return false;
}
function bm_comments_template($value){
	return dirname(__FILE__) . '/comments.php';
}
function bm_install(){
}
function bm_remove(){
}

function bm_request_handler(){
    if( !empty($_GET['bm_action'] )){
        if( $_GET['bm_action'] == 'export_comments'){
            include_once(dirname(__FILE__) . '/export_comments.php');
        }
    }
}
add_action('init', 'bm_request_handler');

add_filter("comments_open", "bm_comments_open");
add_filter("comments_template", "bm_comments_template");

register_activation_hook(__FILE__, 'bm_install');
register_deactivation_hook(__FILE__, 'bm_remove');

function bm_admin_menu(){
    add_options_page('BAYE.ME 设置', 'BAYE.ME', 'administrator', 'bm_settings', 'bm_settings');
    add_submenu_page('edit-comments.php', 'BAYE.ME', 'BAYE.ME', 'moderate_comments', 'bm_admin', 'bm_admin');
}

function bm_admin(){
    $api_key = get_option('bm_api_key');
    $api_secret = get_option('bm_api_secret');
    $token = md5($api_key . $api_secret);
?>
<script type="text/javascript">
    function show_tab(tab){
        if( tab == 'settings' ){
            iframe = document.getElementById('bm_iframe');
            iframe.src = "http://baye.me/api/apis/";
        }
        if( tab == 'comments' ){
            iframe = document.getElementById('bm_iframe');
            iframe.src = "http://baye.me/wordpress/comments?apikey=<?php echo $api_key; ?>&token=<?php echo $token; ?>";
        }
    }
    function show_comments(label){
        iframe = document.getElementById('bm_iframe');
        iframe.src = "http://baye.me/wordpress/comments/" + label + "?apikey=<?php echo $api_key; ?>&token=<?php echo $token; ?>";
    }
</script>
<link rel="stylesheet" href='<?echo WP_CONTENT_URL . '/plugins' . bm_get_plugin_path(__FILE__); ?>/styles.css'/>
<p id="bm_panel">
    <a class="button" href='javascript:show_tab("settings");'>API 设置</a>
    <a class="button" href='javascript:show_tab("comments");'>评论管理</a>
    <a class="button" href='javascript:show_comments("unapproved");'>审核评论</a>
    <a class="button" href='javascript:show_comments("reported");'>被举报评论</a>
    <a class="button" href='javascript:show_comments("spam");'>垃圾评论</a>
    <a class="button" href='javascript:show_comments("deleted");'>回收站</a>
</p>
<iframe id='bm_iframe' src="http://baye.me/wordpress/comments?apikey=<?php echo $api_key; ?>&token=<?php echo $token; ?>" width="900" height="900">
</iframe>
<?php
}

function bm_settings(){
    $message = '设置已保存';
    if( $_SERVER['REQUEST_METHOD']=='POST' ){
        $api_key = $_POST['bm_api_key'];
        $api_secret = $_POST['bm_api_secret'];
        $seo_open = $_POST['bm_seo_open'];
        update_option('bm_api_key', $api_key);
        update_option('bm_api_secret', $api_secret);
        if( $seo_open == "on" ){
            update_option("bm_seo_open", "1");
        }else{
            update_option("bm_seo_open", "0");
        }
        echo '<div class="updated"><p>'. $message .'</p></div>';
    }
?>
<div>
<h2>设置</h2>
    <form method="post" action="">
    <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>
    <?php wp_nonce_field('update-options'); ?>
    <p>
        <input type="hidden" name="update_option"/>
        <label >API KEY :</label>
        <input style="width: 300px" type="text" name="bm_api_key" value="<?php echo get_option("bm_api_key"); ?>"></input>
    </p>
    <p>
        <label >SECRET :</label>
        <input style="width: 300px" type="text" name="bm_api_secret" value="<?php echo get_option("bm_api_secret"); ?>"></input>
    </p>
    <p>
        <input id="seo_open" type="checkbox" name="bm_seo_open" <?php if( get_option("bm_seo_open") == '1'){ echo "checked"; }?>></input>
        <label for="seo_open"><strong>开启SEO 优化</strong></label>
        <p style="color:gray">开启SEO优化后，如果检测到是搜索引擎的爬虫则将在服务端渲染出评论，让爬虫能抓到评论内容<br/>
        目前支持:百度，Google，搜狗，有道，雅虎，MSN，搜搜</p>
    </p>
    <p>
        <input type="submit" value="保存设置" class="button-primary" />
    </p>
    </form>

    <br/>

    <h3>导出WordPress 评论</h3>
    <style type="text/css">
    .finish{
        color: green;
    }
    </style>
    <div>
        <a class="button" id="bm_export_comments" >导出评论</a>
        <?php
            global $wpdb;
            $sql = "SELECT MAX(comment_ID) FROM $wpdb->comments;";
            $global_max_comment_id = $wpdb->get_var($sql);
            $last_update_id = get_option('bm_last_update_id');
            $remain = $global_max_comment_id - $last_update_id;
            if( $remain == 0 ){
                echo "<p style='color:green;'>所有评论都已同步.</p>";
            }else{
                echo "<p style='color:orange;'>已同步到第 $last_update_id 条评论，还需同步 $remain 条";
            }
        ?>
        <p id="bm_export_comments_info" style="/*border:1px solid gray;padding:5px;width:500px;*/">
        </p>
    </div>


    <script type="text/javascript">
        jQuery('#bm_export_comments').click(function(){
            jQuery(this).html("导出中 ...");
            jQuery.get("<?php echo admin_url('index.php'); ?>?bm_action=export_comments", {}, function(resp){
                jQuery('#bm_export_comments_info').append("<p>" +resp.msg + "</p>");
                if( resp.success ){
                    if( resp.goon == '1' ){
                        jQuery('#bm_export_comments').click();
                    }else{
                        jQuery('#bm_export_comments_info').append("<p class='finish'>所有评论导出完毕.</p>");
                    }
                }
            }, 'json')
            return false;
        });
    </script>
</div>
<?php }
if( is_admin() ){
        add_action("admin_menu", "bm_admin_menu");
}
?>
