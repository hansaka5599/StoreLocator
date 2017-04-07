<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Events.
 */
class Events extends Action
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Check current user permission on resource and privilege.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CameraHouse_StoreLocator::ch_store_locator_events');
    }

    /**
     * Function execute.
     */
    public function execute()
    {
    }

    /**
     * Initialize action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('CameraHouse_StoreLocator::ch_store_locator_events')
            ->addBreadcrumb(__('Events'), __('Events'));

        return $resultPage;
    }
}
