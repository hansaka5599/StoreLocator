<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Events;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit.
 */
class Edit extends Container
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
        parent::__construct($context, $data);
    }

    /**
     * Init class.
     */
    protected function _construct()
    {
        $this->_objectId = 'event_id';
        $this->_controller = 'adminhtml_events';
        $this->_blockGroup = 'CameraHouse_StoreLocator';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Event'));
        $this->buttonList->update('delete', 'label', __('Delete Event'));

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ],
            ],
            10
        );

        if ($this->_coreRegistry->registry('current_storelocator_event')) {
            $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
            $storeLocatorId = $this->_coreRegistry->registry('current_storelocator_event')->getStoreLocatorId();

            //Check user role and allowed user list and remove save functionality
            if ($isGenericAdmin === false) {
                $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
                if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                    $this->buttonList->remove('save');
                    $this->buttonList->remove('delete');
                    $this->buttonList->remove('save_and_continue');
                }
            }
        }
    }
}
