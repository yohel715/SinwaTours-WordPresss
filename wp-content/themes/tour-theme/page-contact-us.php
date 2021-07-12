<?php get_header(); ?>
<body>
    <!-- HERO -->
    <div class="section services">
        <div class="second_slider" style="background-image: url(<?php echo get_template_directory_uri(); ?>/img/services_img.jpg);">
            <div class="second_sliderfilter">
                <div class="container slidercontent">
                    <h1 class="about_herotitle herotitle">
                        ¡Empecemos una conversación!
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="container">
        <div class="col two bg margin extrapad" style="padding-left: 2%">
            <h1>CONTÁCTENOS</h1>
            <p>Para nosotros será un placer ayudarte</p>
            <p>
                <span class="iconify sinwa_claro" data-icon="clarity:phone-handset-solid" data-inline="true" data-width="18" data-height="18"></span>
                <a>+506 4700 5445</a>
            </p>
            <p>
                <span class="iconify sinwa_claro" data-icon="clarity:phone-handset-solid" data-inline="true" data-width="18" data-height="18"></span>
                <a>+506 2645 6975</a>
            </p>
            <p>
                <span class="iconify sinwa_claro" data-icon="mdi:map-marker" data-inline="true" data-width="18" data-height="18"></span>
                <a>Monteverde, Central Puntarenas, Costa Rica</a>
            </p>
            <p>
                <span class="iconify sinwa_claro" data-icon="ant-design:clock-circle-filled" data-inline="true" data-width="18" data-height="18"></span>
                Lunes - Viernes 10:00 a.m. - 5:00 p.m.
            </p>
            <h2 class="sinwa_claro">Síguenos</h2>
            <div style="display: flex; padding-bottom: 1rem;">
                <a class="iconify sinwa_claro" style="padding-right: 1rem;" data-icon="akar-icons:facebook-fill" data-inline="true" data-width="32" data-height="32"></a>
                <a class="iconify sinwa_claro" style="padding-right: 1rem;" data-icon="ant-design:instagram-filled" data-inline="true" data-width="32" data-height="32"></a>
                <a class="iconify sinwa_claro" style="padding-right: 1rem;" data-icon="akar-icons:linkedin-fill" data-inline="true" data-width="32" data-height="32"></a>
                <a class="iconify sinwa_claro" style="padding-right: 1rem;" data-icon="entypo-social:tripadvisor" data-inline="true" data-width="32" data-height="32"></a>
                <a class="iconify sinwa_claro" data-icon="bi:whatsapp" data-inline="true" data-width="32" data-height="32"></a>
            </div>
        </div>
        <div class="col two bg margin extrapad" style="padding: 0%">
            <iframe class="contact_location" style="border-radius: 15px" src="https://embed.waze.com/iframe?zoom=13&lat=10.325337649637497&lon=-84.83035161564554&pin=1"></iframe>
        </div>
    </div>

    <!-- FOOTER
    <div class="section">
        <div class="footer">
            <div class="container">
                <p class="center p_captions">
                    <a class="transparent">© 2021 Sinwa Tours</a>
                </p>
            </div>
        </div>
    </div> -->
</body>
<?php get_footer(); ?>