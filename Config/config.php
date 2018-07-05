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
    'widgets'  => [],

    'admin_panel' => [
        'translations' => [
            'index' => [
                'actions' => 'Actions',
            ]
        ]
    ],

    'allow_attachments' => false,

    'revisions_enabled' => false,

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

