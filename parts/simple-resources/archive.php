<?php
get_header();

$banner = get_field('resource_center_banner', 'options');
$channelWidth = (get_field('default_channel_width', 'options') ?get_field('default_channel_width', 'options')."px" : "1200px");
$centerTitle = get_field('resource_center_title', 'options') ?: 'Resources';
$centerSubTitle = get_field('resource_center_subtitle', 'options');

if ($centerSubTitle):
    $centerSubTitle = esc_attr($centerSubTitle);
    $centerSubTitle = ("<h5 class='resource_banner__subtitle'> {$centerSubTitle} </h5>");
else:
    $centerSubTitle = "";
endif;

$banner = esc_url($banner);
$channelWidth = esc_attr($channelWidth);
$centerTitle = esc_attr($centerTitle);

echo("
<div class='resource_banner'
     style='background-image: url( {$banner} );'>
    <div class='resource--channel' style='max-width: { $channelWidth }'>
        <h1 class='resource_banner__title'>
            {$centerTitle}
        </h1>
        {$centerSubTitle}
    </div>
</div>");


if (have_rows('resource_types', 'options')) :
    while (have_rows('resource_types', 'options')) : the_row();
        $rctype = get_sub_field('single_category');
        $rc_query = KMDG\SIMPLERC\get_rc_query($rctype);
        $slug = esc_attr($rctype->slug);
        $name = esc_attr($rctype->name);
        echo("
        <section class='resource_type__section resource_type__section--{$slug}'>
            <div class='resource--channel'
                 style='max-width:{$channelWidth}'>
                <h2 class='resource_type__title resource_type__title--{$slug}'>{$name}</h2>
                <div class='resource_type__cards'>");
                    // The Loop
                    if ($rc_query->have_posts()) :
                        while ($rc_query->have_posts()) : $rc_query->the_post();
                            echo("<div class='single_resource'>");
                                include('resource-card.php');
                            echo("</div>");
                        endwhile;
                    else :
                        include('no-resources.php');
                    endif;
                echo("
                </div>
            </div>
        </section>");
    endwhile;
    echo("</div>");
endif;
get_footer();


