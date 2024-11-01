<?php
setup_postdata($post);

if (has_post_thumbnail()):
    $featured_image = get_post_thumbnail_id();
    $image_style = get_field('image_style');
    $image_position = get_field('image_position');
    $styles = 'background-size: ' . $image_style . '; background-position: center ' . $image_position . ';';
else:
    $featured_image = get_field('default_rc_image', 'options');
    $image_style = 'contain';
    $image_position = null;
    $styles = 'background-size: ' . $image_style . '; background-position: center ' . $image_position . ';';
endif;

$featured_image = $featured_image ? wp_get_attachment_image_src($featured_image, 'large')[0] : KMDG\SIMPLERC\plugin_url_helper("dist/img/default.png") ;

$title = get_the_title();
$terms = get_the_terms($post->ID, 'resource-category');

if (!empty($terms[0])):
    $term = $terms[0];
    $type = get_field('singular_resource_name', $term);
else:
    $type = 'Resource';
endif;

$action_text = get_field('action_text');
if ($action_text):
    $action_text = KMDG\SIMPLERC\kmdg_trim($action_text,30);
else:
    $action_text = "Learn More";
endif;

$download_or_link = get_field('download_or_link');
$file_download = get_field('file_download');
$resource_link = get_field('resource_link');
$title = KMDG\SIMPLERC\kmdg_trim($title);

if ($download_or_link == 'link'):
    $link = $resource_link;
else:
    $link = $file_download;
endif;

$image_style = esc_attr($image_style);
$featured_image = esc_url($featured_image);
$styles = esc_attr($styles);
$type = esc_attr($type);
$title = esc_attr($title);
$link = esc_url($link);
$action_text = esc_attr($action_text);


echo("
<div class='resource_card'>
    <div class='resource_card__wrap'>
        <div class='resource_card__top'>
            <div class='resource_card__img_wrap resource_card__img_wrap--{$image_style}'>
                <div class='resource_card__ft_img' style='background-image:url('{$featured_image}'); style='{$styles}'>
                </div>
            </div>
        </div>
        <div class='resource_card__middle'>
            <div class='resource_card__type'>
                {$type}
            </div>
            <div class='resource_card__title'>
                {$title}
            </div>
        </div>
        <div class='resource_card__bottom'>
            <div class='resource_card__cta'>
                <a href='{$link}' target='_blank' rel='noopener noreferrer'>
                    {$action_text}
                </a>
            </div>
        </div>
    </div>
</div>");
wp_reset_postdata();