<?php namespace Jkchr1s\StaticPageBlocks;

use Backend;
use Backend\Widgets\Form;
use Event;
use Jkchr1s\StaticPageBlocks\Classes\BlockTypeWidgetizer;
use Log;
use RainLab\Pages\Classes\Page;
use RainLab\Pages\Controllers\Index;
use System\Classes\PluginBase;
use Twig\Extension\StringLoaderExtension;

/**
 * StaticPageBlocks Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Static Page Blocks',
            'description' => 'Enables Blocks and Headless CMS API for Static Pages',
            'author'      => 'jkchr1s',
            'icon'        => 'icon-cubes'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        // add twig extension for parsing templates from string
        Event::Listen('cms.page.beforeDisplay', function ($controller, $url, $page) {
            if (!$controller->getTwig()->hasExtension('template_from_string')) {
                $controller->getTwig()->addExtension(new StringLoaderExtension);
            }
        });

        // extend Static Pages menu
        Event::listen('backend.menu.extendItems', function($manager) {
            $manager->addSideMenuItems('RainLab.Pages', 'pages', [
                'blocksets' => [
                    'label' => 'Block Sets',
                    'icon' => 'icon-cubes',
                    'url' => Backend::url('jkchr1s/staticpageblocks/blockset'),
                    'attributes' => [
                        'onclick' => 'window.location.href="' . Backend::url('jkchr1s/staticpageblocks/blockset') . '"'
                    ],
                    'permissions' => ['rainlab.pages.manage_pages']
                ],
                'blocktypes' => [
                    'label' => 'Block Types',
                    'icon' => 'icon-cube',
                    'url' => Backend::url('jkchr1s/staticpageblocks/blocktype'),
                    'attributes' => [
                        'onclick' => 'window.location.href="' . Backend::url('jkchr1s/staticpageblocks/blocktype') . '"'
                    ],
                    'permissions' => ['cms.manage_layouts']
                ]
            ]);
        });

        // extend Static Pages page form
        Event::listen('backend.form.extendFieldsBefore', function(Form $widget) {
            if (!$widget->getController() instanceof Index
                || !$widget->model instanceof Page
                || $widget->isNested) {
                return;
            }

            $widget->tabs['fields']['viewBag[headless]'] = [
                'tab' => 'Headless API',
                'type' => 'checkbox',
                'label' => 'Enable headless API',
                'comment' => 'Allows API requests to fetch the block builder contents.'
            ];

            $widget->secondaryTabs['fields']['viewBag[blocks]'] = [
                'tab' => 'Block Builder',
                'type' => 'repeater',
                'cssClass' => 'secondary-tab',
                'prompt' => 'Add Block',
                'groups' => BlockTypeWidgetizer::repeaterGroups()
            ];
        });
    }

    public function registerPageSnippets()
    {
        return [
            'Jkchr1s\StaticPageBlocks\Components\StaticPageBlock' => 'staticPageBlock'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [];
    }
}
