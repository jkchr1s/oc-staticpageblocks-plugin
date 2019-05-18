<?php

namespace Jkchr1s\StaticPageBlocks\Components;

use Cms\Classes\ComponentBase;
use Jkchr1s\StaticPageBlocks\Models\BlockType;

class StaticPageBlock extends ComponentBase
{
    /**
     * Returns information about this component, including name and description.
     */
    public function componentDetails()
    {
        return [
            'name' => 'Static Page Blocks',
            'description' => 'Renders static page blocks'
        ];
    }

    public function onRun()
    {
        $pageBlocks = [];
        if (
            $this->page
            && $this->page->apiBag
            && is_array($this->page->apiBag)
            && isset($this->page->apiBag['staticPage'])
            && isset($this->page->apiBag['staticPage']['blocks'])
            && is_array($this->page->apiBag['staticPage']['blocks'])
        ) {
            $blocks = $this->page->apiBag['staticPage']['blocks'];
            foreach ($blocks as $block) {
                if (!is_array($block) || !isset($block['_group'])) {
                    continue;
                }
                $blockType = BlockType::find($block['_group']);
                if ($blockType) {
                    $pageBlocks[] = [
                        'template' => $blockType->markup,
                        'data' => $block
                    ];
                }
            }
        }
        $this->page['staticBlocks'] = $pageBlocks;
    }
}
