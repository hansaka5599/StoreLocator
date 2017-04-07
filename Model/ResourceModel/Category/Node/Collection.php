<?php

namespace CameraHouse\StoreLocator\Model\ResourceModel\Category\Node;

/**
 * Class Collection.
 */
class Collection extends \Netstarter\StoreLocator\Model\ResourceModel\Category\Node\Collection
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function getNodeDataArray.
     *
     * @param $categoryId
     * @param null $storeLocatorId
     *
     * @return array
     */
    public function getNodeDataArray($categoryId, $storeLocatorId = null)
    {
        $nodes = [];

        if ($storeLocatorId) {
            $this->getSelect()->joinLeft(
                ['store_category_node' => $this->getTable('ns_store_locator_store_category_node')],
                'main_table.node_id = store_category_node.node_id AND 
                store_category_node.store_locator_id = ' .$storeLocatorId,
                [
                    'store_locator_id',
                    'service_category',
                    'dedicated_page',
                    'page_title',
                    'content_heading',
                    'content',
                    'meta_keyword',
                    'meta_data',
                    'redirect_url',
                ]
            );
        }
        $this->getSelect()
            ->where('main_table.category_id = ?', $categoryId)
            ->order('sort_order', 'asc');

        foreach ($this as $item) {
            if ($item->getLevel() == 0) {
                continue;
            }

            $node = [
                'node_id' => $item->getId(),
                'parent_node_id' => $item->getParentNodeId(),
                'name' => $item->getName(),
                'identifier' => $item->getIdentifier(),
                'selected' => $item->getStoreLocatorId() === null ? false : true,
                'text_content' => $item->getTextContent(),
                'category_icon_tile' => $item->getCategoryIconTile(),
                'service_category' => $item->getServiceCategory(),
                'dedicated_page' => $item->getDedicatedPage(),
                'page_title' => $item->getPageTitle(),
                'content_heading' => $item->getContentHeading(),
                'content' => $item->getContent(),
                'meta_keyword' => $item->getMetaKeyword(),
                'meta_data' => $item->getMetaData(),
                'redirect_url' => $item->getRedirectUrl(),
            ];

            $nodes[] = $node;
        }

        return $nodes;
    }
}
