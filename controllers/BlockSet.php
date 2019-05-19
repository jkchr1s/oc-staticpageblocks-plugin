<?php

namespace Jkchr1s\StaticPageBlocks\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Input;
use Jkchr1s\StaticPageBlocks\Classes\BlockTypeWidgetizer;
use Jkchr1s\StaticPageBlocks\Models\BlockSet as BlockSetModel;
use Lang;
use October\Rain\Exception\ApplicationException;

class BlockSet extends Controller
{
    public $requiredPermissions = ['rainlab.pages.manage_pages'];

    public $implement = [
        'Backend\Behaviors\FormController',
    ];

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('RainLab.Pages', 'pages', 'blocksets');
    }

    public function index()
    {
        $this->pageTitle = 'Block Sets';
    }

    public function onDelete()
    {
        $checked = Input::post('checked');
        if (is_array($checked)) {
            foreach ($checked as $slug) {
                $blockSet = BlockSetModel::find($slug);
                if (!empty($blockSet)) {
                    if (!$blockSet->delete()) {
                        throw new ApplicationException("Unable to remove block set " . $slug);
                    }
                }
            }
        }

        Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        return $this->refreshList();
    }

    public function getBlockSets()
    {
        return BlockSetModel::all();
    }

    /**
     * Called before the form fields are defined.
     * @param Backend\Widgets\Form $host The hosting form widget
     * @return void
     */
    public function formExtendFieldsBefore($host)
    {
        $host->fields['blocks']['groups'] = BlockTypeWidgetizer::repeaterGroups();
    }

    protected function refreshList()
    {
        return [
            '#list-blockset' => $this->makePartial('list_table')
        ];
    }

}
