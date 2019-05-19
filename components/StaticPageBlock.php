<?php

namespace Jkchr1s\StaticPageBlocks\Components;

use Cms\Classes\ComponentBase;
use Jkchr1s\StaticPageBlocks\Models\BlockSet;
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
                $parsed = $this->parseBlock($block);
                if (is_array($parsed)) {
                    foreach ($parsed as $pBlock) {
                        $pageBlocks[] = $pBlock;
                    }
                }
            }
        }
        $this->page['staticBlocks'] = $pageBlocks;
    }

    protected function parseBlock($block)
    {
        if (!is_array($block) || !isset($block['_group'])) {
            return null;
        }
        if ($block['_group'] === '__blockSetEmbed') {
            if (isset($block['blockSlug']) && !empty($block['blockSlug'])) {
                $bs = BlockSet::find($block['blockSlug']);
                $blocks = [];
                foreach ($bs['blocks'] as $block) {
                    $blocks = array_merge($blocks, $this->parseBlock($block));
                }
                return $blocks;
            }
            return null;
        } else {
            $blockType = BlockType::find($block['_group']);
            return $blockType
                ? [[
                    'template' => $blockType->markup,
                    'data' => $block
                ]]
                : null;
        }
    }
}
