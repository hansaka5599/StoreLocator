<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    SNB
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\Category;

/**
 * Class Node.
 */
class Node extends \Netstarter\StoreLocator\Model\Category\Node
{
    const URL_PREFIX_SERVICES = 'services';

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function prepareNodeData.
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareNodeData(array $data)
    {
        return [
            'node_id' => strpos($data['node_id'], '_') === 0 ? null : intval($data['node_id']),
            'name' => $data['name'],
            'identifier' => $data['identifier'],
            'category_id' => $data['category_id'],
            'text_content' => $data['text_content'],
            'category_icon_tile' => isset($data['category_icon_tile']) ? $data['category_icon_tile'] : null,
            'level' => intval($data['level']),
            'sort_order' => intval($data['sort_order']),
            'request_url' => null,
        ];
    }
}
