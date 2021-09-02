<?php

// Registering the Rod's WIDGET
function rod_register_widget()
{
    register_widget('rod_widget');
}
add_action('widgets_init', 'rod_register_widget');

class rod_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            // widget ID
            'rod_widget',
            // widget name
            __("Rod's Stuff Widget", 'rod_widget_domain'),
            // widget description
            array('description' => __('Rod Stuff Display API data', 'rod_widget_domain'),)
        );
    }

    public function widget($args, $instance)
    {

        $title = apply_filters('widget_title', "Rod's API Widget");
        if (!empty($title)) :
            echo $args['before_title'] . $title . $args['after_title'];
        endif;

        // Call the same function that populates the My Account to populate the data in the widget
        rod_stuff_content();
    }

    public function form($instance)
    {
        if (isset($instance['title'])) :
            $title = $instance['title'];
        else :
            $title = __("Rod's API Stuff", 'rod_widget_domain');
?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
<?php
        endif;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}
