<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    SNB
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel\Category;

/**
 * Class Node.
 */
class Node extends \Netstarter\StoreLocator\Model\ResourceModel\Category\Node
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function getCategoryNodes.
     *
     * @param $nodeIds
     *
     * @return array
     */
    public function getCategoryNodes($nodeIds)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['category_node' => $this->getMainTable()],
            ['node_id', 'name', 'request_url', 'category_icon_tile']
        )->where(
            'category_node.node_id IN (?)',
            $nodeIds
        );

        return $connection->fetchAll($select);
    }

    /**
     * Function getLoadByUrlKeySelect.
     *
     * @param $url_key
     *
     * @return string
     */
    public function getLoadByUrlKeySelect($url_key)
    {
        $select = $this->getConnection()->select()->from(
            ['node' => parent::getMainTable()]
        )
            ->where(
                'node.identifier = ?',
                $url_key
            );

        $select->reset(\Zend_Db_Select::COLUMNS)->columns('node.node_id')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}
