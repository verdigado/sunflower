<?php

if (function_exists('register_block_pattern_category')) {
    $dirs = glob(get_template_directory() . '/functions/block-patterns/*', GLOB_ONLYDIR);

    foreach ($dirs as $dir) {
        $basenameDir = basename($dir);

        register_block_pattern_category(
            'sunflower-' . $basenameDir,
            [
                'label' => esc_html__('Sunflower', 'sunflower') . '-' . ucfirst($basenameDir),
            ]
        );

        $files = glob($dir . '/*.html');
        foreach ($files as $file) {
            $basenameFile = basename($file, '.html');

            register_block_pattern(
                'sunflower/' . $basenameFile,
                [
                    'title' => ucfirst($basenameFile),
                    'categories' => ['sunflower-' . $basenameDir],
                    'content' => file_get_contents($file),
                ]
            );
        }
    }
}
