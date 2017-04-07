<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Store;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class Edit.
 */
class Edit extends \Netstarter\StoreLocator\Block\Adminhtml\Store\Edit
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry           $registry
     * @param Data $cameraHouseHelper
     * @param array                                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $cameraHouseHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Function _construct.
     */
    protected function _construct()
    {
        $this->_objectId = 'store_locator_id';
        $this->_blockGroup = 'Netstarter_StoreLocator';
        $this->_controller = 'adminhtml_store';

        parent::_construct();

        if ($this->_isAllowedAction('Netstarter_StoreLocator::store_locator_store_save')) {
            $this->buttonList->update('save', 'label', __('Save Store'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ],
                ],
                -100
            );
            $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
            $storeLocatorId = $this->_coreRegistry->registry('store_locator')->getStoreLocatorId();

            //Check user role and allowed user list and remove save functionality
            if ($isGenericAdmin === false) {
                $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
                if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                    $this->buttonList->remove('save');
                    $this->buttonList->remove('saveandcontinue');
                }
            }
        } else {
            $this->buttonList->remove('save');
        }
        if ($this->_isAllowedAction('Netstarter_StoreLocator::store_locator_store_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Store'));
        } else {
            $this->buttonList->remove('delete');
        }
    }
}
