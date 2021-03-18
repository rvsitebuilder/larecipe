<?php

use Rvsitebuilder\Manage\Lib\ConfigLib;


//TODO: why language option is required?
$larecipeLanguagesPublished = ConfigLib::getDbConfig('rvsitebuilder/larecipe.languages.published');
$languagesPublished = $larecipeLanguagesPublished ? array_map('trim', explode(',', $larecipeLanguagesPublished)) : ['en'];

$larecipeVersionsPublished = ConfigLib::getDbConfig('rvsitebuilder/larecipe.versions.published');
$versionsPublished = $larecipeVersionsPublished ? array_map('trim', explode(',', $larecipeVersionsPublished)) : [];


return [
    'name' => 'Larecipe',
    /*
    |--------------------------------------------------------------------------
    | Documentation Routes
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of the LaRecipe docs basic route
    | where you can specify the url of your documentations, the location
    | of your docs and the landing page when a user visits /docs route.
    |
    |
    */

    'docs' => [
        'route' => '/docs',
        'path' => '/public/storage/larecipe/docs',
        'landing' => 'en/overview',
    ],

    /*
    |--------------------------------------------------------------------------
    | Documentation Versions
    |--------------------------------------------------------------------------
    |
    | Here you may specify and set the versions and the default (latest) one
    | of your documentation's versions where you can redirect the user to.
    | Just make sure that the default version is in the published list.
    |
    |
    */

    'versions' => [
        'default' => ConfigLib::getDbConfig('rvsitebuilder/larecipe.versions.default', ''),
        'published' => $versionsPublished,
    ],

    'languages' => [
        'default' => ConfigLib::getDbConfig('rvsitebuilder/larecipe.languages.default', 'en'),
        'published' => $languagesPublished,
    ],

    'github' => ConfigLib::getDbConfig('rvsitebuilder/larecipe.github', ''),
    'github_show' => ConfigLib::getDbConfig('rvsitebuilder/larecipe.github_show', false),

    /*
    |--------------------------------------------------------------------------
    | Documentation Settings
    |--------------------------------------------------------------------------
    |
    | These options configure the additional behaviors of your documentation
    | where you can limit the access to only authenticated users in your
    | system. It is false initially so that guests can view your docs.
    |
    |
    */

    'settings' => [
        'auth' => false,
        'ga_id' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Obviously rendering markdown at the back-end is costly especially if
    | the rendered files are massive. Thankfully, caching is considered
    | as a good option to speed up your app when having high traffic.
    |
    | Caching period unit: minutes
    |
    */

    'cache' => [
        'enabled' => false,
        'period' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    |
    | Here you can add configure the search functionality of your docs.
    | You can choose the default engine of your search from the list
    | However, you can also enable/disable the search's visibility
    |
    | Supported Search Engines: 'algolia', 'internal'
    |
    */

    'search' => [
        'enabled' => false,
        'default' => 'internal',
        'engines' => [
            'internal' => [
                'index' => ['h2', 'h3'],
            ],
            'algolia' => [
                'key' => '',
                'index' => '',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Appearance
    |--------------------------------------------------------------------------
    |
    | Here you can add configure the appearance of your docs. For example,
    | you can set the primary and secondary colors that will give your
    | documentation a unique look. You can set the fav of your docs.
    |
    |
    */

    'ui' => [
        'code_theme' => 'dark', // or: light
        'fav' => '',     // eg: fav.png
        'fa_v4_shims' => true, // Add FontAwesome v4 shims prevent BC break
        'colors' => [
            'primary' => '#787AF6',
            'secondary' => '#47a6ef',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    |
    | These options configure the SEO settings of your docs. You can set the
    | author, the description and the keywords. Also, LaRecipe by default
    | sets the canonical link to the viewed page's link automatically.
    |
    |
    */

    'seo' => [
        'author' => '',
        'description' => '',
        'keywords' => '',
        'og' => [
            'title' => '',
            'type' => 'article',
            'url' => '',
            'image' => '',
            'description' => '',
        ],
    ],

    /*
   |--------------------------------------------------------------------------
   | Forum
   |--------------------------------------------------------------------------
   |
   | Giving a chance to your users to post their questions or feedback
   | directly on your docs, is pretty nice way to engage them more.
   | However, you can also enable/disable the forum's visibility.
   |
   | Supported Services: 'disqus'
   |
   */

    'forum' => [
        'enabled' => false,
        'default' => 'disqus',
        'services' => [
            'disqus' => [
                'site_name' => ConfigLib::getDbConfig('rvsitebuilder/larecipe.forum.services.disqus.site_name', ''),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Components and Packages
    |--------------------------------------------------------------------------
    |
    | Once you create a new asset or theme, its directory will be
    | published under `larecipe-components` folder. However, If
    | you want a different location, feel free to change it.
    |
    |
    */

    'packages' => [
        'path' => 'larecipe-components',
    ],
    'blade-parser' => [
        'regex' => [
            'code-blocks' => [
                'match' => '/\<pre\>(.|\n)*?<\/pre\>/',
                'replacement' => '<code-block>',
            ]
        ]
    ]
];
