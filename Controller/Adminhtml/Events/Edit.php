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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException as NoSuchEntityException;
use Magento\Framework\Registry;

/**
 * Class Edit.
 */
class Edit extends Events
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable eventsFatory.
     *
     * @var EventsFactory
     */
    protected $eventsFatory;

    /**
     * Variable session.
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * Variable eventResource.
     *
     * @var CameraHouseEvents
     */
    protected $eventResource;

    /**
     * Edit constructor.
     *
     * @param Context                  $context
     * @param Registry                          $coreRegistry
     * @param EventsFactory        $eventsFatory
     * @param CameraHouseEvents $eventResource
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        EventsFactory $eventsFatory,
        CameraHouseEvents $eventResource
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->eventsFatory = $eventsFatory;
        $this->eventResource = $eventResource;
        $this->session = $context->getSession();
        parent::__construct($context);
    }

    /**
     * Function execute.
     *
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam('event_id');

        if ($itemId) {
            try {
                $event = $this->eventsFatory->create();
                $this->eventResource->load($event, $itemId);
                $this->coreRegistry->register('current_storelocator_event', $event);
                $pageTitle = sprintf('%s', $event->getPageTitle());
            } catch (NoSuchEntityException $e) {
                $this->session->unsRuleData();
                $this->messageManager->addErrorMessage(__('This media item no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

                return $resultRedirect->setPath('ch_store_locator/*/');
            }
        } else {
            $pageTitle = __('New Event');
        }

        $breadcrumb = $itemId ? __('Edit Event') : __('New Event');
        $resultPage = $this->initResultPage();
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('Events'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
