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
