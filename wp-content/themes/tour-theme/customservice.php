<?php 
/* Template Name: Gallery*/
get_header(); ?>
  <!-- HERO -->
  <?php while(have_posts() ): the_post();   ?>
  <div class="section">
        <div class="slider second_slider product" style="background-image: url(<?php the_field('gallery_image') ?>)">
            <div class="second_sliderfilter product">
                <div class="container slidercontent">
                    <h1 class="about_herotitle product_title herotitle">
                        <?php the_title();?>
                    </h1>
                    <p class=" about_herosubtitle claro">
                    <?php the_field('category') ?>
                </div>
            </div>
        </div>
    </div>
    <!-- BANNER -->
    <div class="section about_landing_footer">
       <div class="container">
            <div class="">
                <div class="col">
                    <h1>Sobre la zona</h1>
                    <p>
                    <?php the_content();?>
                    </p>
                </div>
            </div>
       </div>
    </div>
    <div class="container">
        <div class="white_card">
            <div class="col"></div>
                <h1><?php the_title();?></h1>
                <p>
                <?php the_field('experience') ?>
                </p>
                <div class="flex"><h2 class="m-0"><span class="iconify" data-icon="bi:calendar-check-fill" data-inline="false"></span></h2><p class="bold">Duracion</p><p><?php the_field('duration') ?></p></div>
                <div class="flex"><h2 class="m-0"><span class="iconify" data-icon="ant-design:clock-circle-filled" data-inline="false"></span></h2><p class="bold">Horario</p><p><?php the_field('schedule') ?></p></div>
                <div class="flex"><h2 class="m-0"><span class="iconify" data-icon="fluent:calendar-checkmark-20-filled" data-inline="false"></span></h2><p class="bold">Disponibilidad</p><p><?php the_field('availability') ?></p></div>
            </div>  
        </div>
    </div>
<?php endwhile; ?>

  <?php get_footer(); ?>