<?php get_header(); ?>
<!-- HERO -->
<div class="section services">
    <div class="second_slider" style="background-image: url(<?php echo get_template_directory_uri(); ?>/img/services_img.jpg);">
        <div class="second_sliderfilter">
            <div class="container slidercontent">
                <h1 class="about_herotitle herotitle">
                    Nuestros Servicios
                </h1>
            </div>
        </div>
    </div>
</div>
<!-- WHITE CARD HORIZONTAL -->
<div class="container search">
    <div class="white_card">
        <p class="oscuro bold">Busca a tu preferencia</p>
        <div class="search_content">
            <input class="search_input" type="search" autocorrect placeholder="Busca Servicios aquí" />
            <button class="iconify search_icon" data-icon="entypo:magnifying-glass" data-inline="false" width="28" />
        </div>
    </div>
</div>

<div class="section about_location">
    <div class="container">
        <h2 class="claro">Agregados recientemente</h2>
        <!--RECIENTES -->
        <div class="services_recent">
            <?php
            //get gallery
            $args = array(
                'post_type' => 'page',
                'posts_per_page' => 4,
                'post_parent' => '12',
                'order' => 'ASC',
                'orderby' => 'ID'
            );

            $the_query = new WP_Query($args);

            if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
            ?>
                    <a href="<?php the_permalink(); ?>" style="text-decoration:none;">
                        <div class="white_card">
                            <h2><?php the_title(); ?></h2>
                        </div>
                    </a>
            <?php endwhile;
            else : echo "<p> no content to show</p>";
            endif; ?>
        </div>
    </div>
</div>


<!-- SINWA FAMILY-->
<div class="section">
    <div class="container listview">
        <div class="col two tag_services_right tag_services">
            <ul class="tags">
                <li class="claro tags">Bosques y reservas biológicas</li>
                <li class="claro tags">Caminatas diaurnas</li>
                <li class="claro tags">Caminatas nocturnas</li>
                <li class="claro tags">Cultura y tradición</li>
                <li class="claro tags">Aventura y naturaleza</li>
                <li class="claro tags">Flora y fuana</li>
            </ul>
        </div>
        <div class="col two tag_services_left">
            <?php
            //get gallery
            $args = array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'post_parent' => '12',
                'order' => 'ASC',
                'orderby' => 'ID'
            );

            $the_query = new WP_Query($args);

            if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
            ?>
                    <!-- CARD SERVICES -->
                    <a href="<?php the_permalink(); ?>" style="text-decoration:none;">
                        <div class="white_card">
                            <div class="white_card_info">
                                <div class="col two">
                                    <img src="<?php the_field('gallery_image') ?>" class="imgholder services" alt="history pic">
                                </div>
                                <div class="col two">
                                    <h1><?php the_title(); ?></h1>
                                    <p style="color: #1f5f5b;"><?php the_field('category') ?></p>
                                    <p>
                                        <?php the_field('gallery_short_description') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="resumes">
                                <div class="flex">
                                    <p class="bold">Duracion</p>
                                    <p><?php the_field('duration') ?></p>
                                </div>
                                <div class="flex">
                                    <p class="bold">Horario</p>
                                    <p><?php the_field('schedule') ?></p>
                                </div>
                                <div class="flex">
                                    <p class="bold">Disponibilidad</p>
                                    <p><?php the_field('availability') ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
            <?php endwhile;
            else : echo "<p> no content to show</p>";
            endif; ?>

        </div>
    </div>
</div>
<?php get_footer(); ?>