<?php

return [

    'layouts' => [
        'layouts.main'  => 'Main layout',
        'layouts.contacts' => 'Contacts layout'
    ],

    'channels' => [
        'blog'  => [
            "template" => 'channels.blog.index'
        ],
        'news'    => [
            'template' => 'channels.news.index'
        ],
        'reports' => [
            "template" => 'channels.reports.index'
        ],
    ],

    /**
     *
     * Example widgets that should help you to roll your own.
     * They can be deleted if you don't need them.
     *
     * Their main purpose is to demonstrate how to configure
     * fields and other options.
     *
     */
    'widgets' => [

        [
            'name'                => 'Large slider',
            'key'                 => 'large_slider',
            'frontend_template'   => 'widgets.large_slider.frontend',
            'backend_template'    => 'content::module_content.widgets.image_blocks.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'image_blocks.js',
            'javascript_key'      => 'image_blocks',
            'backend_css'         => 'image_blocks.css',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\ImageBlock::class,
            'fields'              => [
                'image'   => [
                    'type'   => 'file',
                    'label'  => 'Image',
                    'styles' => [
                        'image_width' => 300
                    ]
                ],
                'content' => [
                    'type'  => 'textarea',
                    'label' => 'Content'
                ]
            ]
        ],

        [
            'name'                => 'Employees',
            'key'                 => 'employees',
            'frontend_template'   => 'widgets.employees.frontend',
            'backend_template'    => 'content::module_content.widgets.image_blocks.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'image_blocks.js',
            'javascript_key'      => 'image_blocks',
            'backend_css'         => 'image_blocks.css',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\ImageBlock::class,
            'fields'              => [
                'title'    => [
                    'type'  => 'text',
                    'label' => 'Name'
                ],
                'subtitle' => [
                    'type'  => 'text',
                    'label' => 'Position'
                ],
                'content'  => [
                    'type'  => 'textarea',
                    'label' => 'Description'
                ],
                'linkedin' => [
                    'type'  => 'text',
                    'label' => 'Linkedin'
                ]
            ]
        ],

        [
            'name'                => 'Text with title',
            'key'                 => 'text_with_title',
            'frontend_template'   => 'widgets.text_with_title.frontend',
            'backend_template'    => 'content::module_content.widgets.simple_text.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'simple_text.js',
            'javascript_key'      => 'simple_text',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\SimpleText::class,
            'fields'              => [
                'title' => [
                    'type'  => 'text',
                    'label' => 'Title'
                ],
                'gray_background' => [
                    'type'   => 'checkbox',
                    'label'  => 'Gray background',
                    'styles' => [
                        'not_required' => true
                    ]
                ],
            ]
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
