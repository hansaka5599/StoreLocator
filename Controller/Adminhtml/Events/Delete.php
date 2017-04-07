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
use CameraHouse\StoreLocator\Model\EventsFactory;
use CameraHouse\StoreLocator\Model\ResourceModel\Events as CameraHouseEvents;
use Magento\Backend\App\Action\Context;

/**
 * Class Delete.
 */
class Delete extends Events
{
    /**
     * Variable eventsFactory.
     *
     * @var EventsFactory
     */
    protected $eventsFactory;

    /**
     * Variable eventResource.
     *
     * @var CameraHouseEvents
     */
    protected $eventResource;

    /**
     * Delete constructor.
     *
     * @param Context                  $context
     * @param EventsFactory        $eventsFactory
     * @param CameraHouseEvents $eventResource
     */
    public function __construct(
        Context $context,
        EventsFactory $eventsFactory,
        CameraHouseEvents $eventResource
    ) {
        $this->eventsFactory = $eventsFactory;
        $this->eventResource = $eventResource;
        parent::__construct($context);
    }

    /**
     * Delete action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('event_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $event = $this->eventsFactory->create();
                $this->eventResource->load($event, $id);
                $this->eventResource->delete($event);
                // display success message
                $this->messageManager->addSuccessMessage(__('Event been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {

                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['event_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
