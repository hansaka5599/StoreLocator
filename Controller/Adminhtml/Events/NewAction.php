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
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\Generic;

/**
 * Class NewAction.
 */
class NewAction extends Events
{
    /**
     * Variable session.
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param Generic $session
     */
    public function __construct(
        Context $context,
        Generic $session
    ) {
        $this->session = $session;
        $this->session->unsFormData();
        parent::__construct($context);
    }

    /**
     * Function execute.
     *
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('edit');
    }
}
