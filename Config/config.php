<?php

return [

    'resolver_template' => null, // leave null to use default

    'layouts' => [
        'layouts.main'     => 'Main layout',
        'layouts.contacts' => 'Contacts layout'
    ],

    'channels' => [
        'blog'    => [
            'template' => 'channels.blog.index'
        ],
        'news'    => [
            'template' => 'channels.news.index'
        ],
        'reports' => [
            'template' => 'channels.reports.index'
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
    'widgets'  => [

        [
            'name'                => 'Large slider',
            'key'                 => 'large_slider',
            'frontend_template'   => 'widgets.large_slider.frontend',
            'backend_template'    => 'content::module_content.widgets.widget_blocks.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'widget_blocks.js',
            'javascript_key'      => 'widget_blocks',
            'backend_css'         => 'widget_blocks.css',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\WidgetBlock::class,
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
            'backend_template'    => 'content::module_content.widgets.widget_blocks.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'widget_blocks.js',
            'javascript_key'      => 'widget_blocks',
            'backend_css'         => 'widget_blocks.css',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\WidgetBlock::class,
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
            'backend_template'    => 'content::module_content.widgets.widget_blocks.backend',
            'backend_with_border' => false, // Depends on what kind of backend_template you have
            'backend_javascript'  => 'widget_blocks.js',
            'javascript_key'      => 'widget_blocks',
            'backend_css'         => 'widget_blocks.css',
            'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\WidgetBlock::class,
            'max_items_count'     => 1,
            'fields'              => [
                'title'           => [
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
        'translations' => [
            'index' => [
                'actions' => 'Actions',
            ]
        ]
    ],

    'allow_attachments' => false,

    'revisions_enabled' => true,

    'meta_tags' => [
        [
            'property' => 'og:type'
        ],
        [
            'property' => 'og:title'
        ],
        [
            'property' => 'og:description'
        ],
        [
            'property' => 'og:image'
        ],
        [
            'property' => 'twitter:title'
        ],
        [
            'property' => 'twitter:description'
        ],
        [
            'property' => 'twitter:image'
        ],
        [
            'name' => 'keywords'
        ],
        [
            'name' => 'description'
        ],
    ]
];

