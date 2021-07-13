<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <?php
      wp_head();
    ?>
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,700;1,900&display=swap"
      rel="stylesheet"
    />
    <title><?php bloginfo( 'name' ); ?></title>
  </head>
<body <?php body_class(); ?>>
<div class="hold">
  <div class="header">
    <div class="container">
      <div id="logo">
        <h1 class="logotext">
          <span
            class="iconify"
            data-icon="mdi:billboard"
            data-inline="false"
          ></span>
          <?php bloginfo( 'name' ); ?>
        </h1>
      </div>
      <input class="menu-btn" type="checkbox" id="menu-btn" />
      <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
      <?php wp_nav_menu( array('theme_location' => 'primary', 'menu_class' => 'nav', 'container' => 'ul', )); ?>
    </div>
  </div>
</div>