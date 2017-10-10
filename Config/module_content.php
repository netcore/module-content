<?php

return [

    'sections' => [

    ],

    'channels' => [
        'news'    => [
            'template' => 'module-content.news.index'
        ],
        'reports' => [
            "template" => 'module-content.reports.index'
        ],
    ],

    'entries' => [

    ],

    'widgets' => [
        [
            'name'                => 'Simple text',
            'key'                 => 'simple_text',
            'frontend_template'   => 'content::module_content.widgets.simple_text.frontend',
            'backend_template'    => 'content::module_content.widgets.simple_text.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'simple_text.js',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\SimpleText::class
        ],
        [
            'name'                => 'Testimonials',
            'key'                 => 'testimonials',
            'frontend_template'   => 'content::module_content.widgets.testimonials.frontend',
            'backend_with_border' => true, // Depends on what kind of backend_template you have
            'backend_template'    => null,
            'backend_javascript'  => false,
        ],
        [
            'name'                => 'Employees',
            'key'                 => 'employees',
            'frontend_template'   => 'content::module_content.widgets.employees.frontend',
            'backend_with_border' => true, // Depends on what kind of backend_template you have
            'backend_template'    => null,
            'backend_javascript'  => false,
        ],
        [
            'name'                => 'Gallery slider',
            'key'                 => 'gallery_slider',
            'frontend_template'   => 'content::module_content.widgets.gallery_slider.frontend',
            'backend_template'    => 'content::module_content.widgets.gallery_slider.backend',
            'backend_with_border' => true, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'gallery_slider.js'
        ]
    ],

    'admin_panel' => [
        'views'        => [
            'extends' => 'layouts.admin',
            'section' => 'layouts.content'
        ],
        'translations' => [
            'index' => [
                'actions' => 'Actions',
            ]
        ]
    ]
];
