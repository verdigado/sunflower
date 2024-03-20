<?php

function sunflower_add_custom_gutenberg_color_palette()
{
    add_theme_support(
        'editor-color-palette',
        [[
            'name' => esc_html__('Himmel', 'sunflower'),
            'slug' => 'himmel',
            'color' => '#0BA1DD',
        ], [
            'name' => esc_html__('Tanne', 'sunflower'),
            'slug' => 'tanne',
            'color' => '#005437',
        ], [
            'name' => esc_html__('Klee', 'sunflower'),
            'slug' => 'klee',
            'color' => '#008939',
        ], [
            'name' => esc_html__('Klee-700', 'sunflower'),
            'slug' => 'klee-700',
            'color' => '#006E2E',
        ], [
            'name' => esc_html__('Grashalm', 'sunflower'),
            'slug' => 'grashalm',
            'color' => '#8ABD24',
        ], [
            'name' => esc_html__('White', 'sunflower'),
            'slug' => 'white',
            'color' => '#ffffff',
        ], [
            'name' => esc_html__('Sonne', 'sunflower'),
            'slug' => 'sonne',
            'color' => '#FFF17A',
        ], [
            'name' => esc_html__('Sand', 'sunflower'),
            'slug' => 'sand',
            'color' => '#F5F1E9',
        ], [
            'name' => esc_html__('Gray', 'sunflower'),
            'slug' => 'gray',
            'color' => '#EFF2ED',
        ], [
            'name' => esc_html__('Black', 'sunflower'),
            'slug' => 'black',
            'color' => '#201D1B',
        ]]
    );
}

add_action('after_setup_theme', 'sunflower_add_custom_gutenberg_color_palette');
