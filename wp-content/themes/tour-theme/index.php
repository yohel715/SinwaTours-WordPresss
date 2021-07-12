<?php get_header(); ?>
  <!-- HERO -->
  <div class="section">
    <div class="slider" style="background-image: url(<?php echo get_template_directory_uri(); ?>/img/hero_img1.jpg);">
      <div class="sliderfilter">
        <div class="container slidercontent">
          <h1 class="herotitle">
            ¡Averigüe sobre nuestros <br />
            servicios y beneficios <br />
            para usted!
          </h1>
          <div class="call">
            <span>Conoce más sobre nuestros servicios</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- BANNER -->
  <div class="section">
    <div class="landing_footer">
      <h2 class="hero_footer bold">¡Es momento de viajar!</h2>
      <p class="p_captions oscuro" style="color: #061a23 !important;">Conoce más sobre nosotros</p>
      <span class="iconify arrow_down_icon" data-icon="fluent:chevron-down-20-filled" data-inline="false"
      style="color: #061a23" widht="2rem" height="2rem"></span>
    </div>
  </div>
  <!-- PRIMERA SECCION -->
  <div class="section bg">
    <div class="container bannerfamily">
      <div class=logofam>
        <h1 class="logofam">SINWA TOUR</h1>
        <h2 class="logofamsub">Monteverde Express</h6>
      </div>
      <div class=logofam>
        <h1 class="logofam">SINWA TOUR</h1>
        <h2 class="logofamsub">Dreams Tour Operator</h6>
      </div>
      <div class=logofam>
        <h1 class="logofam">SINWA TOUR</h1>
        <h2 class="logofamsub">House Bed & Breakfast</h6>
      </div>
    </div>

    <div class="container">
      <div class="col two bg margin extrapad">
      <h1 class="side">¿Quienes somos?</h1>
        <p class="side oscuro">
          Sinwa Tours somos una micro-empresa certificada, 100% costarricense,
          familiar, joven, innovadora y muy dinámica trabajando en el área
          turística.<br /><br />
          Desde el 20 noviembre de 2009 le brindamos a todos nuestros clientes
          un servicio distinguido y personalizado.<br /><br />
          Ofrecemos experiencias innovadoras, únicas, auténticas, culturales,
          seguras,divertidas, para conocer
          mas de Costa Rica.<br /><br />
          Nuestro compromiso con los clientes es
          abarcar sus deseos y necesidades para
          brindarles un servicio de excelencia. <br/> Con el más alto
          standard basado en la ética
          profesional y la responsabilidad ambiental.
        </p>
        <br /><br />
        <!-- TODO: arreglar redireccionamiento -->
        <a class="sidebutton">Ver más</a>
        <div class="side center">
          <span
            class="iconify person"
            data-icon="fa-solid:hiking"
            data-inline="false"
          ></span>
        </div>
      </div>
      <div class="col two bg margin extrapad">
        <h1 class="side">Nuestras Certificaciones</h1>
        <p class="side two oscuro">
          Nos hemos asegurado mediante capacitaciones de que su seguridad y
          bienestar sean nuestra maxima prioridad.
        </p>
        <div class="logosdiv">
          <img class="logoc" src="<?php echo get_template_directory_uri().'/img/logo_pyme.png'; ?>" alt="Logo Pyme Costa Rica"/>
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_conducta.png'; ?>" alt="Logo" />
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_ict.png'; ?>" alt="Logo" />
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_turismo.png'; ?>" alt="Logo" />
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_bandera.png'; ?>" alt="Logo" />
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_essential.png'; ?>" alt="Logo" />
          <span></span>
          <img class="logoc"  src="<?php echo get_template_directory_uri().'/img/logo_safe.png'; ?>" alt="Logo" />
        </div>
      </div>
    </div>
  </div>

  <!-- SEGUNDA SECCION -->
  <div class="section bg">
    <div class="container">
      <h1></h1>

      <?php 
      //get gallery
      $args = array(
        'post_type' => 'page',
        'posts_per_page' => 6,
        'post_parent' => '12',
        'order' => 'ASC',
        'orderby' => 'ID'
      );

      $the_query = new WP_Query($args);

      if($the_query -> have_posts() ) : while($the_query -> have_posts()) : $the_query -> the_post();
      ?>
      <a href="<?php the_permalink(); ?>" style="text-decoration:none;">
        <div class="col three bg nopad pointer">
          <img class="imgholder" src="<?php the_field('gallery_image') ?>" alt="" />
          <h2 class="card_recents_title"><?php the_title();?></h2>
        </div>
      </a>
      <?php endwhile;
      else : echo "<p> no content to show</p>";
      endif; ?>

      <div class="group"></div>
      <h1></h1>
    </div>
  </div>

  <!-- TERCERA SECCION -->
  <div class="section servicesbg" style="background-image: url(<?php echo get_template_directory_uri(); ?>/img/services_img.jpg);">
    <div class="servicesbgfilter">
      <div class="container services_container">
        <div class="services_card">
          <h1 class="text_card">SERVICIO PERSONALIZADO</h1>
          <p class="text_card">
            Todos y cada uno de nuestros clientes recibe un servicio
            personalizado que se adapte a sus preferencias y necesidades.
          </p>
        </div>

        <div class="services_card card_center">
          <h1 class="text_card">PRECIOS ACCESIBLES</h1>
          <p class="text_card">
            Poseemos los precios más increíbles y cómodos para conocer los
            mejores lugares de Costa Rica.
          </p>
        </div>

        <div class="services_card">
          <h1 class="text_card">SERVICIO SEGURO</h1>
          <p class="text_card">
            Porque nuestra prioridad es realizar experiencias únicas y
            maravillosas, brindamos la mejor seguridad para su total
            tranquilidad.
          </p>
        </div>
      </div>

      <div class="container">
      <!-- TODO: arreglar redireccionamiento -->
        <a class="p_captions button_container">Ver más sobre nuestros servicios</a>
      </div>
    </div>
  </div>
  <?php get_footer(); ?>