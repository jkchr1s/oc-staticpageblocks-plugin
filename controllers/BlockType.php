<?php

namespace Jkchr1s\StaticPageBlocks\Controllers;

use Backend\Classes\Controller;
use Flash;
use Input;
use Jkchr1s\StaticPageBlocks\Models\BlockType as BlockTypeModel;
use Lang;
use October\Rain\Exception\ApplicationException;

class BlockType extends Controller
{
//    use InspectableContainer;

    public $requiredPermissions = ['acme.blog.rainlab.pages.manage_pages'];

    public $implement = [
        'Backend\Behaviors\FormController',
    ];

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->pageTitle = 'Block Types';
    }

    public function getBlockTypes()
    {
        return BlockTypeModel::all();
    }

    public function onDelete()
    {
        $checked = Input::post('checked');
        if (is_array($checked)) {
            foreach ($checked as $slug) {
                $blockType = BlockTypeModel::find($slug);
                if (!empty($blockType)) {
                    if (!$blockType->delete()) {
                        throw new ApplicationException("Unable to remove block type " . $slug);
                    }
                }
            }
        }

        Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        return $this->refreshList();
    }

    protected function refreshList()
    {
        return [
            '#list-blocktype' => $this->makePartial('list_table')
        ];
    }

//    public function onGetInspectorConfiguration()
//    {
//        // Load and use some values from the posted form
//        //
//        $someValue = Request::input('someValue');
//
//        return [
//            'configuration' => [
//                'properties' => [],
//                'title'       => 'Inspector title',
//                'description' => 'Inspector description'
//            ]
//        ];
//    }
}
