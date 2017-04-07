<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml\Events;

use CameraHouse\StoreLocator\Controller\Adminhtml\Events;

/**
 * Class Index.
 */
class Index extends Events
{
    /**
     * Function execute.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->initResultPage();
        $resultPage->getConfig()->getTitle()->prepend(__('Events'));

        return $resultPage;
    }
}
