<?php
add_action('wp_enqueue_scripts', 'add_css');
function add_css()
{
   wp_register_style('first', get_stylesheet_directory_uri().'/assets/css/style.css', array(),'1.1','all');
   wp_enqueue_style( 'first');
}

add_action('wp_enqueue_scripts', 'add_script');
function add_script()
{
   wp_register_script('js-header', get_stylesheet_directory_uri().'/assets/js/script.js', array(), '1', true);
   wp_enqueue_script('js-header');
   wp_register_script('icons', 'https://code.iconify.design/1/1.0.7/iconify.min.js', array(), '1.0.7', true);
   wp_enqueue_script('icons');
}

add_theme_support( 'menus' );

add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);

function special_nav_class ($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
    $classes[] = 'active ';
  }
  return $classes;
}

//support featured image
add_theme_support('post-thumbnails');

