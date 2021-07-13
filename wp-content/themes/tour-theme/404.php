<?php get_header(); ?>
<!-- HERO -->
<body>
 <!-- CONTENT -->
 <div class="container">
        <h1 class="center error">Error 404</h1>
        <h2 class="center">Página no encontrada</h2>
        <span class="center">
            <a class="button" href="<?php echo get_site_url(); ?>">Ir a inicio</a>
        </span>
        <img class="404error" src="<?php echo get_template_directory_uri().'/img/sinwa404.svg'; ?>" alt="404error" />
    </div>
</body>
<?php get_footer(); ?>