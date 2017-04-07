<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Event;

use CameraHouse\StoreLocator\Model\ResourceModel\Events\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;

/***
 * Class View
 * @package CameraHouse\StoreLocator\Controller\Event
 */
class View extends Action
{
    /**
     * Variable resultForwardFactory.
     *
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Variable eventsCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $eventsCollectionFactory;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * View constructor.
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param CollectionFactory $eventsCollectionFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        CollectionFactory $eventsCollectionFactory,
        Registry $coreRegistry
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->eventsCollectionFactory = $eventsCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Function execute
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function execute()
    {
        $eventId = $this->getRequest()->getParam('event_id', $this->getRequest()->getParam('id', false));

        if (!$eventId) {
            $resultForward = $this->resultForwardFactory->create();

            return $resultForward->forward('noroute');
        }

        $eventCollection = $this->eventsCollectionFactory->create();
        $eventCollection->addFieldToSelect('*');
        $eventCollection->addFieldToFilter('main_table.status', 1);
        $eventCollection->addFieldToFilter('event_id', $eventId);
        $eventCollection->setPageSize(1);

        $storeLocatorTable = 'ns_store_locator';

        $eventCollection->getSelect()->joinLeft(
            ['sl' => $storeLocatorTable],
            'sl.store_locator_id = main_table.store_locator_id',
            [
                'sl.name',
                'sl.url_key',
                'sl.heading_name',
                'sl.phone',
                'sl.email',
                'sl.fax',
                'sl.street',
                'sl.street2',
                'sl.suburb',
                'sl.postcode',
            ]
        );

        if ($eventCollection) {
            $this->coreRegistry->register('current_store_event', $eventCollection->getFirstItem()->getData());
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
