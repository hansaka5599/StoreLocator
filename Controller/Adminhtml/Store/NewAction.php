<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml\Store;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class NewAction.
 */
class NewAction extends \Netstarter\StoreLocator\Controller\Adminhtml\Store\NewAction
{
    /**
     * Variable resultForwardFactory.
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * NewAction constructor.
     *
     * @param Context               $context
     * @param ForwardFactory $resultForwardFactory
     * @param Data             $cameraHouseHelper
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        Data $cameraHouseHelper
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $resultForwardFactory);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        //Check user role and allowed user list and remove save functionality
        $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
        if ($isGenericAdmin === false) {
            return false;
        } else {
            return $this->_authorization->isAllowed('Netstarter_StoreLocator::store_locator_store_save');
        }
    }
}
