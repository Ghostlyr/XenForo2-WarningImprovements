<?php

namespace SV\WarningImprovements\Repository;

use XF\Mvc\Entity\Finder;
use XF\Repository\AbstractCategoryTree;
use XF\Tree;

class WarningCategory extends AbstractCategoryTree
{
    /**
     * @return string
     */
    protected function getClassName()
    {
        return 'SV\WarningImprovements:WarningCategory';
    }

    /**
     * @param null $categories
     * @param int $rootId
     * @param bool $excludeEmpty
     *
     * @return Tree
     */
    public function createCategoryTree($categories = null, $rootId = 0, $excludeEmpty = false)
    {
        if ($excludeEmpty)
        {
            if ($categories === null)
            {
                $categories = $this->findCategoryList()
                    ->where('warning_count', '<>', 0)
                    ->fetch();
            }
            return new Tree($categories, 'parent_category_id', $rootId);
        }

        return parent::createCategoryTree($categories, $rootId);
    }

    /**
     * @param array $extras
     * @param array $childExtras
     *
     * @return array
     */
    public function mergeCategoryListExtras(array $extras, array $childExtras)
    {
        $output = array_merge([
            'warning_count' => 0,
            'childCount' => 0
        ], $extras);

        foreach ($childExtras AS $child)
        {
            if (!empty($child['warning_count']))
            {
                $output['warning_count'] += $child['warning_count'];
            }

            $output['childCount'] += 1 + (!empty($child['childCount']) ? $child['childCount'] : 0);
        }

        return $output;
    }

    public function findCategoryParentList(\SV\WarningImprovements\Entity\WarningCategory $category = null, $with = null)
    {
        $finder = $this->finder($this->getClassName())->setDefaultOrder('rgt');
        if ($category)
        {
            $finder
                ->where('lft', '<', $category->lft)
                ->where('rgt', '>', $category->rgt);
        }
        if ($with)
        {
            $finder->with($with);
        }

        return $finder;
    }
}