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
use CameraHouse\StoreLocator\Helper\Data;
use CameraHouse\StoreLocator\Model\EventsFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Session\Generic;

/**
 * Class Save.
 */
class Save extends Events
{
    /**
     * Variable eventsFactory.
     *
     * @var EventsFactory
     */
    protected $eventsFactory;

    /**
     * Variable session.
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param EventsFactory $eventsFactory
     * @param Data $cameraHouseHelper
     * @param Generic $session
     */
    public function __construct(
        Context $context,
        EventsFactory $eventsFactory,
        Data $cameraHouseHelper,
        Generic $session
    ) {
        $this->eventsFactory = $eventsFactory;
        $this->session = $session;
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context);
    }

    /**
     * Function execute.
     *
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('event_id');
            $model = $this->eventsFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Event no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Event.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['event_id' => $model->getEventId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // save data in session

                // redirect to edit form
                $this->session->setFormData(json_encode($data));

                return $resultRedirect->setPath('*/*/edit', $data);
            }
        }

        return $resultRedirect->setPath('*/*/');
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
        $storeLocatorId = $this->getRequest()->getPostValue('store_locator_id');

        //Check user role and allowed user list and remove save functionality
        if ($isGenericAdmin === false) {
            $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
            if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
