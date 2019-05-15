<?php

namespace Jkchr1s\StaticPageBlocks\Models;

use Cms\Classes\CmsObject;
use Cms\Classes\Content;
use File;
use Validator;

class BlockType extends Content
{
    /**
     * @var string The container name associated with the model, eg: pages.
     */
    protected $dirName = 'content/block-types';

    /**
     * @var bool Wrap code section in PHP tags.
     */
    protected $wrapCode = false;

    /**
     * @var array Properties that can be set with fill()
     */
    protected $fillable = [
        'title',
        'slug',
        'markup',
        'settings',
        'blocks',
    ];

    /**
     * @var array List of attribute names which are not considered "settings".
     */
    protected $purgeable = ['parsedMarkup', 'placeholders'];

    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [
        'title' => 'required',
        'slug'   => ['required', 'regex:/^[a-z0-9\/_\-\.]*$/i', 'uniqueBlockSlug']
    ];

    /**
     * @var array The array of custom attribute names.
     */
    public $attributeNames = [
        'title' => 'title',
        'slug' => 'slug',
        'blocks' => 'blocks',
    ];

    //
    // CMS Object
    //

    /**
     * Sets the object attributes.
     * @param array $attributes A list of attributes to set.
     */
    public function fill(array $attributes)
    {
        parent::fill($attributes);

        /*
         * When the page is saved, copy setting properties to the view bag.
         * This is required for the back-end editors.
         */
        if (array_key_exists('settings', $attributes) && array_key_exists('viewBag', $attributes['settings'])) {
            $this->getViewBag()->setProperties($attributes['settings']['viewBag']);
            $this->fillViewBagArray();
        }
    }

    /**
     * Returns the attributes used for validation.
     * @return array
     */
    protected function getValidationAttributes()
    {
        return $this->getAttributes() + $this->viewBag;
    }

    /**
     * Validates the object properties.
     * Throws a ValidationException in case of an error.
     */
    public function beforeValidate()
    {
        $blockTypes = Content::listInTheme($this->theme, true);

        Validator::extend('uniqueBlockSlug', function($attribute, $value, $parameters) use ($blockTypes) {
            $value = trim(strtolower($value));

            foreach ($blockTypes as $blockType) {
                /** @var CmsObject $blockType */
                if (
                    $blockType->getBaseFileName() !== $this->getBaseFileName() &&
                    strtolower($blockType->getViewBag()->property('slug')) == $value
                ) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Triggered before a new object is saved.
     */
    public function beforeCreate()
    {
        $this->fileName = $this->generateFilenameFromCode();
    }

    /*
     * Generate a file name based on the URL
     */
    protected function generateFilenameFromCode()
    {
        $dir = rtrim($this->getFilePath(''), '/');

        $fileName = trim(str_replace('/', '-', $this->slug), '-');
        if (strlen($fileName) > 200) {
            $fileName = substr($fileName, 0, 200);
        }

        if (!strlen($fileName)) {
            $fileName = 'index';
        }

        $curName = trim($fileName).'.htm';
        $counter = 2;

        while (File::exists($dir.'/'.$curName)) {
            $curName = $fileName.'-'.$counter.'.htm';
            $counter++;
        }

        return $curName;
    }

}
