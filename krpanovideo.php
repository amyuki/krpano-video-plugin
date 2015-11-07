<?php
/*
Plugin Name: Krpano Video Embed
Description: Embed a 360 degree video to post/page with krpano.
Version:  1.0
Author: Yuki Cheung
Author URI: http://szeching.com/
*/
add_action('admin_init','krpanovideo_tinymce_button');
add_action('init','register_shortcodes');
add_action('wp_head','krpanovideo_head');

function krpanovideo_head(){
	print('<script src="'.get_bloginfo('wpurl').'/wp-content/plugins/krpano-video-plugin/krpano/embedpano.js"></script>
		<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/krpano-video-plugin/style.css" type="text/css" />
		');
}

function krpanovideo_tinymce_button(){
	if(current_user_can('edit_posts') && current_user_can('edit_pages')){
		add_filter('mce_buttons','krpanovideo_register_tinymce_buttons');
		add_filter('mce_external_plugins','krpanovideo_add_tinymce_buttons');
	}
}

function krpanovideo_register_tinymce_buttons($buttons){
	array_push($buttons, 'krpanovideo');
	return $buttons;
}

function krpanovideo_add_tinymce_buttons($plugin_array){
	$plugin_array['krpanovideo'] = plugins_url('/js/buttons.js',__FILE__);
	return $plugin_array;
}

function register_shortcodes(){
	add_shortcode('krpanovideo', 'krpanovideo_function');
}

function krpanovideo_function($atts,$content){
	extract(shortcode_atts(array(
			'ioscover'=>'',
			'iosvideo'=>'',
			'noioscover'=>'',
			'noiosvideo'=>'',
		),$atts));
	if (!file_exists('wp-content/plugins/krpano-video-plugin/krpano/xml/'.$content.'.xml')){
	$filename = fopen('wp-content/plugins/krpano-video-plugin/krpano/xml/'.$content.'.xml', 'w');
	$txt = '<krpano version="1.18" bgcolor="0x000000">
	<!-- the videoplayer interface skin -->
	<include url="../skin/videointerface.xml" />

	<!-- include the videoplayer plugin and load the video (use a low res video for iOS) -->
	<plugin name="video"
	        url.flash="%SWFPATH%/plugins/videoplayer.swf"
	        url.html5="%SWFPATH%/plugins/videoplayer.js"

	        posterurl.ios="%SWFPATH%/video/video-1024x512-poster.jpg"
	        videourl.ios="%SWFPATH%/video/video-1024x512.mp4"

	        posterurl.no-ios="%SWFPATH%/video/video-1920x960-poster.jpg"
	        videourl.no-ios="%SWFPATH%/video/video-1920x960.mp4"

	        pausedonstart="true"
	        loop="true"
	        enabled="false"
	        zorder="0"
	        align="center" ox="0" oy="0"

	        width.no-panovideosupport="100%"
	        height.no-panovideosupport="prop"

	        onloaded="videointerface_setup_interface(get(name)); setup_video_controls();"
	        onvideoready="videointerface_videoready();"
	        />

	<!-- custom control setup - add items for selecting videos with a different resolution/quality -->
	<action name="setup_video_controls">
		<!-- add  items to the control menu of the videointerface skin -->
		videointerface_addmenuitem(configmenu, vqtitle, \'Select Video Quality\', true, videointerface_toggle_configmenu() );
		videointerface_addmenuitem(configmenu, q1, \'1024x512\',  false, change_video_file(q1, \'%SWFPATH%/video/video-1024x512.mp4\'); );
		videointerface_addmenuitem(configmenu, q2, \'1920x960\',  false, change_video_file(q2, \'%SWFPATH%/video/video-1920x960.mp4\'); );

		<!-- select/mark the current video (see the initial videourl attribute) -->
		if(device.ios,
			videointerface_selectmenuitem(configmenu, q1);
		  ,
			videointerface_selectmenuitem(configmenu, q2);
		  );
	</action>

	<!-- change the video file, but try keeping the same playback position -->
	<action name="change_video_file">
		plugin[video].playvideo(\'%CURRENTXML%/%2\', null, get(plugin[video].ispaused), get(plugin[video].time));
		videointerface_deselectmenuitem(configmenu, q1);
		videointerface_deselectmenuitem(configmenu, q2);
		videointerface_selectmenuitem(configmenu, %1);
	</action>


	<!-- the panoramic video image -->
	<image devices="panovideosupport">
		<sphere url="plugin:video" />
	</image>


	<!-- set the default view - a light fisheye projection -->
	<view hlookat="0" vlookat="0" fovtype="DFOV" fov="130" fovmin="75" fovmax="150" fisheye="0.35" />

</krpano>';
	fwrite($filename,$txt);}
	return '<div id="pano" style="width:100%;height:450px;"><noscript><table style="width:100%;height:450px;"><tr style="vertical-align:middle;"><td><div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div></td></tr></table></noscript>
	<script>embedpano({swf:"'.get_bloginfo('wpurl').'/wp-content/plugins/krpano-video-plugin/krpano/krpano.swf",xml:"'.get_bloginfo('wpurl').'/wp-content/plugins/krpano-video-plugin/krpano/xml/'.$content.'.xml", target:"pano", html5:(document.domain ? "prefer" : "auto"), passQueryParameters:true});</script>';
}

?>