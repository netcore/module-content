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
        'testimonials'   => [
            'frontend_template' => 'module_content.widgets.testimonials',
            'backend_template'  => null
        ],
        'employees'      => [
            'frontend_template' => 'module_content.widgets.gallery_slider',
            'backend_template'  => null
        ],
        'gallery_slider' => [
            'frontend_template' => 'module_content.widgets.gallery_slider.frontend',
            'backend_template'  => 'module_content.widgets.gallery_slider.backend'
        ]
    ],
    
    'admin_panel' => [
        'views' => [
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
