<?php
/*
  Plugin Name: YouTube widget responsive
  Description: Widgets responsive to embed youtube in your sidebar, with all available options.
  Author: StefanoAI
  Version: 0.3
  Author URI: http://www.stefanoai.com
 */

class YouTubeResponsive extends \WP_Widget {

    public function __construct() {
        parent::__construct(
                'youtube_responsive', // Base ID
                'YouTube Responsive', // Name
                array('description' => YOUTUBE_description,) // Args
        );
    }

    static function wp_head() {
        wp_enqueue_script('jquery');
    }

    static function wp_footer() {
        ?>
        <script type="text/javascript">
            function AI_responsive_widget() {
                jQuery('iframe.StefanoAI-youtube-responsive').each(function() {
                    var width = jQuery(this).parent().innerWidth();
                    jQuery(this).css('width', width + "px");
                    jQuery(this).css('height', width / (16 / 9) + "px");
                });
            }
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function() {
                    AI_responsive_widget();
                });
                jQuery(window).resize(function() {
                    AI_responsive_widget();
                });
            }
        </script>
        <?php
    }

    function widget($args, $instance) {
        global $youtube_id;
        extract($args);
        preg_match('/\?v=([^&]+)/', $instance['video'], $m);
        $idvideo = !empty($m[1]) ? $m[1] : $instance['video'];
        if (!empty($idvideo)) {
            $url = !empty($instance['privacy']) && $instance['privacy'] == '1' ? '//www.youtube-nocookie.com/embed/' : '//www.youtube.com/embed/';
            $rel = !empty($instance['suggested']) && $instance['suggested'] == '1' ? '' : '&rel=0';
            $time = (!empty($instance['start_m']) ? intval($instance['start_m']) * 60 : 0) + (!empty($instance['start_s']) ? intval($instance['start_s']) : 0);
            $endtime = (!empty($instance['end_m']) ? intval($instance['end_m']) * 60 : 0) + (!empty($instance['end_s']) ? intval($instance['end_s']) : 0);
            $start = (!empty($time)) ? "&start=$time" : "";
            $end = !empty($endtime) ? "&end=$endtime" : '';
            @$id = ++$youtube_id;
            $autoplay = !empty($instance['autoplay']) ? '&autoplay=1' : '';
            $cc_load = !empty($instance['cc_load']) ? '&cc_load_policy=1' : '';
            $cc_lang = !empty($instance['cc_lang']) ? '&hl=' . $instance['cc_lang'] : '';
            $allowfullscreen = !empty($instance['allowfullscreen']) ? 'allowfullscreen="true"' : '';

            @$widget = "<iframe id='$id' class='StefanoAI-youtube-responsive' width='160' height='90' src='$url$idvideo?$autoplay$rel$cc_lang$cc_load$start$end' frameborder='0' $allowfullscreen></iframe>";
            $title = apply_filters('widget_title', $instance['title']);
            echo $before_widget;
            echo $before_title . $title . $after_title;
            echo $widget;
            echo $after_widget;
        }
    }

    function update($new_instance, $old_instance) {
        //save widget settings
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['video'] = strip_tags($new_instance['video']);
        $instance['start_m'] = strip_tags($new_instance['start_m']);
        $instance['start_s'] = strip_tags($new_instance['start_s']);
        $instance['end_m'] = strip_tags($new_instance['end_m']);
        $instance['end_s'] = strip_tags($new_instance['end_s']);
        $instance['cc_lang'] = strip_tags($new_instance['cc_lang']);
        $instance['cc_load'] = !empty($new_instance['cc_load']) ? $new_instance['cc_load'] : 0;
        $instance['autoplay'] = !empty($new_instance['autoplay']) ? $new_instance['autoplay'] : 0;
        $instance['privacy'] = !empty($new_instance['privacy']) ? $new_instance['privacy'] : 0;
        $instance['suggested'] = !empty($new_instance['suggested']) ? $new_instance['suggested'] : 0;
        $instance['allowfullscreen'] = !empty($new_instance['allowfullscreen']) ? $new_instance['allowfullscreen'] : 0;
        return $instance;
    }

    function form($instance) {
        $title = (isset($instance['title'])) ? $instance['title'] : '';
        $video = (isset($instance['video'])) ? $instance['video'] : '';
        $autoplay = (isset($instance['autoplay'])) ? $instance['autoplay'] : 0;
        $suggested = (isset($instance['suggested'])) ? $instance['suggested'] : 0;
        $privacy = (isset($instance['privacy'])) ? $instance['privacy'] : 0;
        $start_m = (isset($instance['start_m'])) ? $instance['start_m'] : 0;
        $start_s = (isset($instance['start_s'])) ? $instance['start_s'] : 0;
        $end_m = (isset($instance['end_m'])) ? $instance['end_m'] : 0;
        $end_s = (isset($instance['end_s'])) ? $instance['end_s'] : 0;
        $cc_load = (isset($instance['cc_load'])) ? $instance['cc_load'] : 0;
        $allowfullscreen = (isset($instance['allowfullscreen'])) ? $instance['allowfullscreen'] : 0;
        $cc_lang = (isset($instance['cc_lang'])) ? $instance['cc_lang'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo YOUTUBE_title ?>: </label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('video'); ?>"><?php echo YOUTUBE_video ?>: </label> 
            <input class="widefat" id="<?php echo $this->get_field_id('video'); ?>" name="<?php echo $this->get_field_name('video'); ?>" type="text" value="<?php echo esc_attr($video); ?>" />
        </p>
        <p>
            <label><?php echo YOUTUBE_start_time ?>: </label> 
            <input class="widefat" style="width:30px;display: inline-block;" id="<?php echo $this->get_field_id('start_m'); ?>" name="<?php echo $this->get_field_name('start_m'); ?>" type="text" value="<?php echo esc_attr($start_m); ?>" /> <?php echo YOUTUBE_minutes ?>
            <input class="widefat" style="width:30px;display: inline-block;" id="<?php echo $this->get_field_id('start_s'); ?>" name="<?php echo $this->get_field_name('start_s'); ?>" type="text" value="<?php echo esc_attr($start_s); ?>" /> <?php echo YOUTUBE_seconds ?>
        </p>
        <p>
            <label><?php echo YOUTUBE_end_time ?>: </label> 
            <input class="widefat" style="width:30px;display: inline-block;" id="<?php echo $this->get_field_id('end_m'); ?>" name="<?php echo $this->get_field_name('end_m'); ?>" type="text" value="<?php echo esc_attr($end_m); ?>" /> <?php echo YOUTUBE_minutes ?>
            <input class="widefat" style="width:30px;display: inline-block;" id="<?php echo $this->get_field_id('end_s'); ?>" name="<?php echo $this->get_field_name('end_s'); ?>" type="text" value="<?php echo esc_attr($end_s); ?>" /> <?php echo YOUTUBE_seconds ?>
        </p>
        <p>
            <input  id="<?php echo $this->get_field_id('cc_load'); ?>" name="<?php echo $this->get_field_name('cc_load'); ?>" type="checkbox" value="1" <?php echo esc_attr($cc_load) == "1" ? 'checked' : ''; ?> />
            <label for="<?php echo $this->get_field_id('cc_load'); ?>"><?php echo YOUTUBE_cc_load ?> </label> <br/>
            <label for="<?php echo $this->get_field_id('cc_lang'); ?>"><?php echo YOUTUBE_cc_lang ?>: </label> 
            <input maxlength="2" style="width:30px" class="widefat" id="<?php echo $this->get_field_id('cc_lang'); ?>" name="<?php echo $this->get_field_name('cc_lang'); ?>" type="text" value="<?php echo esc_attr($cc_lang); ?>" />
        </p>
        <p>
            <input  id="<?php echo $this->get_field_id('allowfullscreen'); ?>" name="<?php echo $this->get_field_name('allowfullscreen'); ?>" type="checkbox" value="1" <?php echo esc_attr($allowfullscreen) == "1" ? 'checked' : ''; ?> />
            <label for="<?php echo $this->get_field_id('allowfullscreen'); ?>"><?php echo YOUTUBE_allowfullscreen ?> </label> 
        </p>
        <p>
            <input  id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>" type="checkbox" value="1" <?php echo esc_attr($autoplay) == "1" ? 'checked' : ''; ?> />
            <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php echo YOUTUBE_autoplay ?> </label> 
        </p>
        <p>
            <input  id="<?php echo $this->get_field_id('privacy'); ?>" name="<?php echo $this->get_field_name('privacy'); ?>" type="checkbox" value="1" <?php echo esc_attr($privacy) == "1" ? 'checked' : ''; ?> />
            <label for="<?php echo $this->get_field_id('privacy'); ?>"><?php echo YOUTUBE_privacy ?> </label> 
        </p>
        <p>
            <input  id="<?php echo $this->get_field_id('suggested'); ?>" name="<?php echo $this->get_field_name('suggested'); ?>" type="checkbox" value="1" <?php echo esc_attr($suggested) == "1" ? 'checked' : ''; ?> />
            <label for="<?php echo $this->get_field_id('suggested'); ?>"><?php echo YOUTUBE_suggested ?> </label> 
        </p>
        <?php
    }

}

if (file_exists(plugin_dir_path(__FILE__) . "lang/" . WPLANG . '.php')) {
    include_once plugin_dir_path(__FILE__) . "lang/" . WPLANG . '.php';
} else {
    include_once plugin_dir_path(__FILE__) . "lang/en_US.php";
}

function register_youtuberesponsive_widgets() {
    register_widget('YouTubeResponsive');
}

add_action('widgets_init', 'register_youtuberesponsive_widgets');
add_action('wp_footer', array('YouTubeResponsive', 'wp_footer'), 99);
add_action('wp_head', array('YouTubeResponsive', 'wp_head'), 99);
