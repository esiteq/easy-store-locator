<?php

/**
 * Plugin Name:       Easy Store Locator
 * Description:       Easy Store Locator for any Wordpress based site
 * Version:           0.1
 * Created:           07-09-2016
 * Updated:           09-10-2018
 * Author:            Alex Bugrov
 * Author URI:        http://www.esiteq.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       esl
 * Domain Path:       /languages
 * Tested up to: 4.9.5
 */

class EasyStoreLocator
{
    // Replace this key with your own
    const MAPS_API_KEY = 'AIzaSyBX100YHmCt1ZiOtpIYS1S8_llq8rFLI_M';
    function __construct()
    {
        add_action('init', [$this, 'init']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 100);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        add_action('manage_store_posts_custom_column' , [$this, 'store_posts_custom_column'], 10, 2);
        add_action('admin_notices', [$this, 'admin_notices'], 10);
        add_filter('manage_store_posts_columns', [$this, 'store_posts_columns'], 10);
        add_shortcode('easy_store_locator' , [$this, 'easy_store_locator']);
    }
    //
    function required_plugins_active()
    {
        if (is_plugin_active('advanced-custom-fields/acf.php') && is_plugin_active('advanced-custom-fields-google-map-extended/acf-google-map-extended.php'))
        {
            return true;
        }
        return false;
    }
    //
    function admin_notices()
    {
        if ($this->required_plugins_active())
        {
            return;
        }
?>
    <div class="error notice">
        <p><?php _e('Plugins <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields</a> and <a href="https://wordpress.org/plugins/advanced-custom-fields-google-map-extended/" target="_blank">ACF: Google Map Extended</a> must be installed and activated before you can use Easy Store Locator', 'cnext'); ?></p>
    </div>
<?php
    }
    //
    function wp_enqueue_scripts()
    {
        wp_enqueue_script('esl-frontend', plugins_url('js/frontend.js', __file__), ['jquery']);
        wp_enqueue_script('googlemaps-api', '//maps.googleapis.com/maps/api/js?v=3&callback=initMap&libraries=places&key='. self::MAPS_API_KEY, ['esl-frontend'], false, true);
        wp_enqueue_style('esl-frontend',  plugins_url('css/frontend.css', __file__));
    }
    //
    function easy_store_locator($atts, $content)
    {
        ob_start();
        require_once(plugin_dir_path(__file__). '/templates/shortcode.php');
        return ob_get_clean();
    }
    //
    function store_posts_columns($columns)
    {
        $columns['featured_image'] = __('Thumbnail', 'cnext');
        $columns['esl_location'] = __('Location', 'cnext');
        return $columns;
    }
    //
    function store_posts_custom_column($column, $post_id)
    {
        if ($column == 'featured_image')
        {
            $thumb = get_the_post_thumbnail($post_id, array(64,64));
            echo $thumb;
        }
        if ($column == 'esl_location')
        {
            $location = get_post_meta($post_id, 'esl_location', true);
            if (is_array($location) && isset($location['address']))
            {
                echo $location['address'];
            }
            else
            {
                echo '<b style="color:red">Not set!</b>';
            }
        }
    }
    //
    function admin_enqueue_scripts()
    {
        wp_deregister_script('googlemaps-api');
        wp_register_script('googlemaps-api', '//maps.googleapis.com/maps/api/js?v=3&libraries=places&key='. self::MAPS_API_KEY, [], false, true);
        wp_enqueue_style('esl-backend',  plugins_url('css/backend.css', __file__));        
    }
    //
    function admin_print_scripts()
    {
        global $wp_scripts;
        foreach( $wp_scripts->queue as $handle )
        {
            $src = $wp_scripts->registered[$handle]->src;
            //$src = str_replace($domain, '', $src); // replace domain
            echo '<!-- script: ', $src, '-->'. "\n";
            $wp_scripts->registered[$handle]->src = $src;
        }
    }
    //
    function init()
    {
        $labels = [
		'name'               => __('Stores', 'esl'),
		'singular_name'      => __('Store', 'esl'),
		'menu_name'          => __('Store Locator', 'esl'),
		'name_admin_bar'     => __('Stores', 'esl'),
		'add_new'            => __('Add New', 'esl'),
		'add_new_item'       => __('Add New Store', 'esl'),
		'new_item'           => __('New Store', 'esl'),
		'edit_item'          => __('Edit Store', 'esl'),
		'view_item'          => __('View Store', 'esl'),
		'all_items'          => __('All Stores', 'esl'),
		'search_items'       => __('Search Stores', 'esl'),
		'parent_item_colon'  => __('Parent Stores:', 'esl'),
		'not_found'          => __('No Stores found', 'esl'),
		'not_found_in_trash' => __('No Stores found in Trash', 'esl')
        ];
        $args = [
		'labels'             => $labels,
        'description'        => __('Manage Stores list', 'esl'),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => ['slug' => 'store'],
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon'          => 'dashicons-cart'
        ];
        register_post_type('store', $args);
        register_taxonomy('store_category', 'store',
        [
            'labels' =>
            [
                'name' => __('Store Category', 'esl'),
                'add_new_item' => __('Add New Store Category', 'esl'),
                'new_item_name' => __('New Store Category', 'esl')
            ],
            'hierarchical' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'store-categories']
        ]);
    }
    //
    function get_categories()
    {
        $categories = get_terms('store_category', ['hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
        return $categories;
    }
    //
    function print_category_options()
    {
        $cat = isset($_GET['category']) ? intval($_GET['category']) : 0;
        $categories = $this->get_categories();
        $html = '<option value="0">'. __('Any', 'esl'). '</option>';
        foreach ($categories as $category)
        {
            $selected = ($category->term_id == $cat) ? ' selected="selected"' : '';
            $html .= '<option value="'. $category->term_id. '"'. $selected. '>'. esc_html($category->name). '</option>';
        }
        echo $html;
    }
    //
    function get_category()
    {
        return isset($_GET['category']) ? intval($_GET['category']) : 0;
    }
    //
    function get_address()
    {
        return isset($_GET['address']) ? $_GET['address'] : '';
    }
    //
    function get_lat()
    {
        return isset($_GET['lat']) ? floatval($_GET['lat']) : 51.524303406495996;
    }
    //
    function get_lng()
    {
        return isset($_GET['lng']) ? floatval($_GET['lng']) : -0.16009506542968666;
    }
    //
    function get_radius()
    {
        return isset($_GET['radius']) ? floatval($_GET['radius']) : 10;
    }
    //
    function get_stores()
    {
        $args = ['post_type'=>'store', 'post_status'=>'publish', 'posts_per_page'=>-1];
        if ($this->get_category() > 0)
        {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'store_category',
                    'field' => 'id',
                    'terms' => $this->get_category(),
                    'include_children' => true
                ]
            ];
        }
        $posts = get_posts($args);
        $stores = [];
        foreach ($posts as $store)
        {
            $location = get_post_meta($store->ID, 'esl_location', true);
            $terms = wp_get_post_terms($store->ID, 'store_category');
            $newstore =
            [
                'id' => $store->ID,
                'lat' => floatval($location['lat']),
                'lng' => floatval($location['lng']),
                'address' => esc_js($location['address']),
                'title' => esc_js($store->post_title),
                'description' => str_replace("'", "\'", str_replace("\n", '<br />', trim($store->post_content))),
                'excerpt' => esc_js($store->post_excerpt),
                'thumb' => str_ireplace("'", "\'", get_the_post_thumbnail($store->ID, 'full')),
                'link' => get_permalink($store->ID),
                'navigation' => 'https://www.google.com/maps/search/?api=1&query='. urlencode($location['address']),
                'open_hours' => get_field('esl_open', $store->ID),
                'website' => get_field('esl_website', $store->ID),
                'email' => get_field('esl_email', $store->ID),
                'phone' => get_field('esl_telephone', $store->ID)
            ];
            if (is_array($terms) && count($terms)>0)
            {
                $term = current($terms);
                $newstore['category'] = $term->name;
                $icon = get_field('esl_cat_icon', $term->taxonomy . '_' . $term->term_id);
                if (is_array($icon))
                {
                    $newstore['icon'] = $icon['url'];
                }
                else
                {
                    $newstore['icon'] = '';
                }
            }
            $stores[$store->ID] = $newstore;
        }
        return $stores;
    }
    //
}
$ESL = new EasyStoreLocator;
?>