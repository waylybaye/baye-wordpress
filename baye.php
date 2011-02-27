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
function bm_comments_open($open){
    return false;
}
function bm_comments_template($value){
	return dirname(__FILE__) . '/comments.php';
}

add_filter("comments_open", "bm_comments_open");
add_filter("comments_template", "bm_comments_template");

function bm_admin_menu(){
    add_options_page('BAYE.ME 管理页面', 'BAYE.ME', 'administrator', 'baye_me_admin', 'bm_admin');
}
function bm_admin(){
?>
<div>
<h2>设置</h2>
    <form method="post" action="options.php">
    <?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>
    <?php wp_nonce_field('update-options'); ?>
    <p>
        <label >API KEY :</label>
        <input type="text" name="bm_apikey" value="<?php echo get_option("bm_apikey"); ?>"></input>
    </p> 
    <p>
        <label >SECRET :</label>
        <input type="text" name="bm_secret" ><?php echo get_option("bm_secret") ; ?></input>
    </p> 

    <p> 
        <input type="hidden" name="action" value="update" /> 
        <input type="hidden" name="page_options" value="display_copyright_text" /> 
        <input type="submit" value="保存设置" class="button-primary" /> 
    </p> 
    </form> 
</div> 
            
<?php } 
if( is_admin() ){
        add_action("admin_menu", "bm_admin_menu");
}
?>
