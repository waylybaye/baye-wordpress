<?php
/*
Plugin Name: BAYE.ME Social Comments 
Plugin URI: http://baye.me 
Description: BAYE.ME 社会化评论 
Version: 0.1 
Author: Wayly.baye
Author URI: http://baye.wayly.net
*/
?>
<?php
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
        update_option('bm_api_key', $api_key);
        update_option('bm_api_secret', $api_secret);
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
        <input type="submit" value="保存设置" class="button-primary" /> 
    </p> 
    </form> 

    <br/>

    <p> <h3>导出WordPress 评论</h3> </p>
    <div>
        <a class="button" id="bm_export_comments" >导出评论</a>
        <p id="bm_export_comments_info">
        </p>
    </div>


    <script type="text/javascript">
        jQuery('#bm_export_comments').click(function(){
            jQuery.get("<?php echo admin_url('index.php'); ?>?bm_action=export_comments", {}, function(resp){
                jQuery('#bm_export_comments_info').html(resp);
            }, 'html')
            return false;
        });
    </script>
</div> 
<?php } 
if( is_admin() ){
        add_action("admin_menu", "bm_admin_menu");
}
?>
