<?php namespace Jkchr1s\StaticPageBlocks;

use Backend;
use Backend\Widgets\Form;
use Event;
use RainLab\Pages\Classes\Page;
use RainLab\Pages\Controllers\Index;
use System\Classes\PluginBase;

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
            'name'        => 'StaticPageBlocks',
            'description' => 'No description provided yet...',
            'author'      => 'Jkchr1s',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Event::listen('backend.form.extendFieldsBefore', function(Form $widget) {
            if (!$widget->getController() instanceof Index
                || !$widget->model instanceof Page
                || $widget->isNested) {
                return;
            }

            $widget->tabs['fields']['viewBag[customFlag]'] = [
                'tab' => 'Example',
                'type' => 'checkbox',
                'label' => 'Some custom flag'
            ];

            $widget->secondaryTabs['fields']['viewBag[blocks]'] = [
                'tab' => 'Proof of Concept',
                'type' => 'repeater',
                'cssClass' => 'secondary-tab',
                'prompt' => 'Add Block',
                'groups' => [
                    'block_paragraph' => [
                        'name' => 'Paragraph',
                        'description' => 'Adds a paragraph of text',
                        'icon' => 'icon-paragraph',
                        'fields' => [
                            'markdown' => [
                                'type' => 'markdown'
                            ]
                        ]
                    ]
                ]
            ];
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Jkchr1s\StaticPageBlocks\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'jkchr1s.staticpageblocks.some_permission' => [
                'tab' => 'StaticPageBlocks',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'staticpageblocks' => [
                'label'       => 'StaticPageBlocks',
                'url'         => Backend::url('jkchr1s/staticpageblocks/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['jkchr1s.staticpageblocks.*'],
                'order'       => 500,
            ],
        ];
    }
}
