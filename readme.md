## Content management with widgets

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

1. Configure new widget in ```config/netcore/module-content.php```
2. Create custom frontend template file for that widget

And that's it! Backend will be generated automatically. You can reorder, edit, and add new widgets to pages.
Screenshot:

![screenshot](https://image.prntscr.com/image/K4IDTqVFShu_EcAUvtAu6w.png)



## Installation

Require this package with composer:

    composer require netcore/module-content

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

After installation, you should start editing ```config/netcore/module-content.php``` and put there your own widgets
according to "Adding a new widget" instructions.



## Terminology

Before working with this package, it can be helpful to understand basic terminology:

1. Entry - this is a page. It can be either a standalone page like "Terms and services", or it can belong to a channel.
For example - page "Post 1" in "Blog" channel.
2. Channel - this is a group of entries. Common examples of channels are "Blog", "News", "Reports".
3. Section - sections are currently not used anywhere and will be deleted in version 0.1.x
4. Widget - some sort of section that exists in page. Common examples are LargeSliderWidget, TestimonialsWidget, EmployeesWidget.



## Adding a new widget

1. Configure fields for new widget. Open ```config/netcore/module-content.php```. This file includes some example widgets
that should help you as a starting point for your own. You can (and should) delete them later.
2. Add new item to ```widgets``` array. In most cases you will want to customize name, key, frontend_template, javascript_key, backend_worker and fields.
However, in most cases your will not customize backend_template and backend_worker. These are usually universal.
3. Create template that you specified as ```frontend_template``` in step one.
4. All done. Backend is generated automatically.



## Adding a new channel

Channels should be added via database seeding. They are typically hardcoded, so administrator shouldn't have any user
interface to add new channels.

However, you need to configure and create a template for each of your channels in ```config/netcore/module-content.php```

