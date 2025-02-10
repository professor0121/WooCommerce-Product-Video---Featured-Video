 <?php
/**
 * Plugin Name: WooCommerce Featured Video
 * Plugin URI:  https://yourwebsite.com/
 * Description: Adds support for featured video and featured image for WooCommerce products.
 * Version:     1.3
 * Author:      Abhishek kushwaha
 * Author URI:  https://yourwebsite.com/
 * License:     GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}




// Enqueue Tailwind CSS
function wcfv_enqueue_tailwind() {
    wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
    wp_enqueue_media(); // Enqueue WordPress Media Library
    wp_enqueue_script('wcfv-admin-script', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'wcfv_enqueue_tailwind');

// Register Meta Box
function wcfv_add_featured_video_meta_box() {
    add_meta_box(
        'wcfv_featured_video',
        __('Featured Media', 'woocommerce'),
        'wcfv_featured_video_callback',
        'product',
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'wcfv_add_featured_video_meta_box');

// Meta Box Callback
function wcfv_featured_video_callback($post) {
    $selected_option = get_post_meta($post->ID, '_wcfv_featured_media_type', true);
    $media_url = get_post_meta($post->ID, '_wcfv_featured_media_url', true);
    ?>
    <p>
        <label for="wcfv_featured_media_type" class="block text-sm font-medium text-gray-700">Media Type</label>
        <select id="wcfv_featured_media_type" name="wcfv_featured_media_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
            <option value="image" <?php selected($selected_option, 'image'); ?>>Image</option>
            <option value="youtube" <?php selected($selected_option, 'youtube'); ?>>YouTube</option>
            <option value="vimeo" <?php selected($selected_option, 'vimeo'); ?>>Vimeo</option>
            <option value="mp4" <?php selected($selected_option, 'mp4'); ?>>MP4 URL</option>
            <option value="upload" <?php selected($selected_option, 'upload'); ?>>Upload Video</option>
        </select>
    </p>
    <p>
        <label for="wcfv_featured_media_url" class="block text-sm font-medium text-gray-700">Select Media</label>
        <input type="text" id="wcfv_featured_media_url" name="wcfv_featured_media_url" value="<?php echo esc_attr($media_url); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
        <button type="button" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded select-media-button">Select from Media</button>
    </p>
    <?php
}

// Save Meta Box Data
function wcfv_save_featured_media($post_id) {
    if (isset($_POST['wcfv_featured_media_type'])) {
        update_post_meta($post_id, '_wcfv_featured_media_type', sanitize_text_field($_POST['wcfv_featured_media_type']));
    }
    if (isset($_POST['wcfv_featured_media_url'])) {
        update_post_meta($post_id, '_wcfv_featured_media_url', sanitize_text_field($_POST['wcfv_featured_media_url']));
    }
}
add_action('save_post', 'wcfv_save_featured_media');

// Remove WooCommerce Featured Image and Replace with Selected Media
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

function wcfv_replace_featured_media() {
    global $post;
    $media_type = get_post_meta($post->ID, '_wcfv_featured_media_type', true);
    $media_url = get_post_meta($post->ID, '_wcfv_featured_media_url', true);
    
    
    
    echo '<div class="woocommerce-featured-media-new">';
    if ($media_type == 'image') {
        echo '<img src="' . esc_url($media_url) . '" class="w-full h-64 object-cover">';
    } elseif ($media_type == 'youtube' || $media_type == 'vimeo') {
        echo '<iframe class="w-full h-64" src="' . esc_url($media_url) . '" frameborder="0" allowfullscreen></iframe>';
    } elseif ($media_type == 'mp4' || $media_type == 'upload') {
        echo '<video class="w-full h-64" controls><source src="' . esc_url($media_url) . '" type="video/mp4"></video>';
    }
    echo '</div>';
    ?>
  
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let gallery = document.querySelector('.woocommerce-product-gallery__image--placeholder');
            let featuredMedia = document.querySelector('.woocommerce-featured-media-new');
            let productImage=gallery.lastElementChild;
            productImage.style.display="none";
            
            if (gallery && featuredMedia) {
                gallery.prepend(featuredMedia); // Append featured media as first child
                console.log( lastItem);
            }
            
            
        });
    </script>
    <?php

}
add_action('woocommerce_before_single_product_summary', 'wcfv_replace_featured_media', 20);


 

// Add JavaScript for Media Selector
add_action('admin_footer', function() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            $('.select-media-button').click(function(e) {
                e.preventDefault();
                var mediaUploader = wp.media({
                    title: 'Choose Media',
                    button: { text: 'Select' },
                    multiple: false
                }).on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#wcfv_featured_media_url').val(attachment.url);
                }).open();
            });
        });
    </script>
    <?php
});
