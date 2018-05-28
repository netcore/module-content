## Content management with widgets v2.1

Idea behind this package is that many webpages are constructed from different sections, that
look different in frontend, but they can use identical UI and database structure in backend.
For example, a typical landing page might consist from following sections:

1. Large slider with images. Each image has large title and smaller subtitle.
2. A grid of services provided by webpage. Each item in grid has image, title and subtitle.
3. Testimonials. Each item has image, person's name, and content.
4. Brands. Each item has image and link to external website.
5. Employees. Each item has image, name, position, age and link to Linkedin.

We call these sections "widgets". Each of these look very different in frontend, but backend can be made universal.
That's because we typically need the same fields over and over again - image, title, subtitle, content.
Any not so common fields, like links, buttons or other non-standard information is stored in a JSON format.

In order to add a new widget to your page, you would perform only the following actions:

1. Seed new widget in database
2. Create custom frontend template file for that widget

And that's it! Backend will be generated automatically. You can reorder, edit, and add new widgets to pages.
Screenshot:

![screenshot](https://image.prntscr.com/image/K4IDTqVFShu_EcAUvtAu6w.png)


## Pre-installation

This package is part of Netcore CMS ecosystem and is only functional in a project that has following packages
installed:

1. https://github.com/netcore/netcore
2. https://github.com/netcore/module-admin
3. https://github.com/netcore/module-translate

## Installation

Require this package with composer:

    composer require netcore/module-content:2.1.*

Publish config, assets, migrations. Migrate and seed:

    php artisan module:publish-config Content
    php artisan module:publish Content
    php artisan module:publish-migration Content
    php artisan migrate
    php artisan module:seed Content

Package also comes with basic ResolverController.php and routes for showing static pages and channels.
It will work for many simple pages. If you need something more sophisticated or custom, you will need to implement your own Resolver.
However, in most cases add this to your RouteServiceProvider.php (typically in ```mapeWebRoutes()``` method:

    Route::group([
        'middleware' => ['web'],
        'namespace'  => null,
    ], function (Router $router) {
        \Modules\Content\Http\ResolverRouter::register($router);
    });

After installation, you should config ```config/netcore/module-content.php``` file.


## Updating to new version

Let's imagine you have installed version v2.1.0, but a couple of days later v2.1.1 is released.
Because we are working with https://github.com/nWidart/laravel-modules, simply doing
```composer require netcore/module-content``` might not be enough, because there might be
changes in assets and/or migrations. Therefore, to install new version, you should run all the following commands:

    composer update netcore/module-content
    php artisan module:publish Content
    php artisan module:publish-migration Content
    php artisan migrate

## Terminology

Before working with this package, it can be helpful to understand basic terminology:

1. Entry - this is a page. It can be either a standalone page like "Terms and services", or it can belong to a channel.
For example - page "Post 1" in "Blog" channel.
2. Channel - this is a group of entries. Common examples of channels are "Blog", "News", "Reports".
3. Section - sections are currently not used anywhere and will be deleted in version 0.1.x
4. Widget - some sort of section that exists in page. Common examples are LargeSliderWidget, TestimonialsWidget, EmployeesWidget.

It is better to keep everything in separated seed files, because it can get messy after some time. We recommend to create WidgetSeeder, ChannelSeeder, PageSeeder

## Adding a new widget

1. Start with widget seeding
```
content()->storeWidgets([
            'Employees widget' => [
                'is_enabled'    => 1,
                'widget_fields' => [
                    'Block title'       => [ // These are our employees
                        'type' => 'text',
                    ],
                    'Block description' => [ // We have many different employees, you can see every one of them here.
                        'type' => 'textarea',
                    ],
                ],
                'item_fields'   => [
                    'Name'        => [ // John Doe
                        'type' => 'text',
                    ],
                    'Job title'   => [ // Backend developer
                        'type' => 'text',
                    ],
                    'Description' => [ // My job is to do some stuff.
                        'type' => 'textarea',
                    ],
                    'Picture'     => [ // Picture of John Doe
                        'type' => 'file',
                        'options' => [ // options are optional
                            'width'  => 555, // image width
                            'height' => 200, //image heght
                        ]
                    ],
                ],
                'options'       => []
            ]
        ]);
```
Widget fields are meant for widget main information. For example, title.
Item fields are meant for items which will be stored in widget. You can store multiple items in one widget.
You can customize widget options, available options:

```
'options'       => [
    'frontend_template'   => 'widgets.text_with_title.frontend', // by default "widgets.{widget_name}.frontend"
    'backend_template'    => 'content::module_content.widgets.widget_blocks.backend', 
    'backend_with_border' => false, // Depends on what kind of backend_template you have
    'backend_javascript'  => 'widget_blocks.js',
    'javascript_key'      => 'widget_blocks',
    'backend_css'         => 'widget_blocks.css',
    'is_empty'            => false,
    'content'             => 'This text is shown when widget has no input data'
    'has_template'        => true,
    'backend_worker'      => \Modules\Content\Widgets\BackendWorkers\WidgetBlock::class,
    'max_items_count'     => 1, // put "0" if you want to allow unlimmited amount of items
]
```

You can create empty widget, which won't be configurable from Content section in admin panel, but you can display any data in frontend template. This is useful if you want to display forum thread list. Here is an example widget:

```
content()->storeWidgets([
    'Forum threads' => [
        'is_enabled'    => 1,
        'options' => [
            'is_empty' => true,
            'content' => 'This widget is configurable from <a href="link-to-section">Forum section</a>'
        ]
    ]   
]);
```

2. In most cases you will want to customize name, key, frontend_template, javascript_key, backend_worker and fields.
However, in most cases your will not customize backend_template and backend_worker. These are usually universal.
3. Create template that you specified as ```frontend_template``` in step one.
4. All done. Backend is generated automatically.

## Creating pages and seeding widget content

You can seed multiple pages per channel. By default there is always default channel (Main) which contains static pages, but there is cases where you would want to create another channel for News.

Below there is an example for seeding pages per channel

```
  content()->storePages([
            'static'   => [
                [
                    'name'        => 'Home page',
                    'layout'      => 'layouts.main',
                    'is_active'   => 1,
                    'is_homepage' => 1,
                    'data'        => $homepageData
                ]
            ],
            'news' => [
                // news pages
            ],
        ]);
```

$homepageData looks like

```
$homepageData = [
            'widgets'     => [
                [
                    'widget'     => 'Employees widget',
                    'main_items' => [
                        'block_title'       => 'Get to know our employees',
                        'block_description' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam consequatur, esse libero omnis sapiente sequi sint sit totam. Magni, ratione.</p>',
                    ],
                    'items'      => [
                        [
                            'name'        => 'John Doe',
                            'job_title'   => 'Backend developer',
                            'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque, quam?</p>',
                            'picture'     => resource_path('seed_widgets/employees_widget/1.jpg')
                        ],
                        [
                            'name'        => 'Doe John',
                            'job_title'   => 'Backend developer 2',
                            'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque, quam? 2</p>',
                            'picture'     => resource_path('seed_widgets/employees_widget/2.jpg')
                        ],
                    ]
                ]
            ],
            'attachments' => [ // optional, used for news channel 
                resource_path('seed_widgets/articles_images/1.jpg'),
                resource_path('seed_widgets/articles_images/2.jpg'),
            ],
            'entry_data'  => [ // optional, used for news channel
                'article_content' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab accusamus ad aperiam architecto consequuntur corporis cum dignissimos dolorem doloribus, ea earum eius enim error esse fugit hic id illum impedit, labore minus mollitia necessitatibus non obcaecati optio placeat quas quasi quia quod similique sit sunt suscipit temporibus, tenetur ut voluptas voluptate voluptatum! Alias dolores ea eaque eos eveniet, explicabo facilis ipsam iste nemo nobis optio placeat quae reprehenderit rerum tempore totam vero? Ipsam laboriosam nostrum officia perferendis quidem similique sunt! Adipisci architecto asperiores cum, ducimus, facere facilis id itaque nobis nostrum officia omnis repellat rerum soluta vel, veniam vero voluptatibus.</p>'
            ]
        ];
```


## Adding a new channel

If you want to create new channels, for example, you may want to create news channel where you would store news articles.

```
$channels = content()->storeChannels([
            [
                'layout'            => 'layouts.main',
                'is_active'         => 1,
                'slug'              => 'news',
                'name'              => 'News',
                'allow_attachments' => 1, // attachments may be used for storing pictures of news
                'fields'            => [ // may add multiple fields, for example "Author"
                    'Article content' => [
                        'type' => 'textarea',
                    ],
                    'Vote count' => [
                        'type'      => 'text',
                        'is_global' => 1 //Channel entries will have global fields, which won't be translateable
                    ],
                ]
            ],
        ]);
```

However, you need to configure and create a template for each of your channels in ```config/netcore/module-content.php```

## Main information about versions

0.1 branch contains initial version of package. It has very limited multi-language support and it is mainly intended for use in not-translatable websites.

1.0 properly supports translatable websites. SimpleText and ImageBlocks widgets are deleted in favor of more universal WidgetBlock widget.
Additionally, fields for widget items are no longer stored as JSON.

2.0 
TODO