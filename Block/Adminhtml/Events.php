<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Events.
 */
class Events extends Container
{
    /**
     * Function _construct.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_events';
        $this->_blockGroup = 'CameraHouse_StoreLocator';
        $this->_headerText = __('Manage Store Events');
        $this->_addButtonLabel = __('Add New Event');
        parent::_construct();
    }
}
