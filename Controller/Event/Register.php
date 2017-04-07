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

use CameraHouse\StoreLocator\Model\Events;
use CameraHouse\StoreLocator\Model\ResourceModel\Events\CollectionFactory;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Netstarter\StoreLocator\Model\Store as NetstarterStore;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Register.
 */
class Register extends Action
{
    /**
     * Recipient email config path.
     */
    const XML_PATH_EMAIL_RECIPIENT = 'netstarter_storelocator/events/recipient_email';

    /**
     * Sender email config path.
     */
    const XML_PATH_EMAIL_SENDER = 'netstarter_storelocator/events/sender_email_identity';

    /**
     * Email template config path.
     */
    const XML_PATH_EMAIL_TEMPLATE = 'netstarter_storelocator/events/email_template';

    /**
     * Variable transportBuilder.
     *
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Variable scopeConfig.
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Variable eventsCollectionFactory.
     *
     * @var CollectionFactory
     */
    protected $eventsCollectionFactory;

    /**
     * Variable storeManager
     * @var
     */
    protected $storeManager;

    /**
     * Register constructor.
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $eventsCollectionFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $eventsCollectionFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->eventsCollectionFactory = $eventsCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Function execute.
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/event/view');

            return;
        }
        if ($post) {
            try {
                $postObject = new DataObject();
                $postObject->setData($post);

                $error = false;

                if (!\Zend_Validate::is(trim($post['fname']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['eventid']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['lname']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['event']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['identifier']), 'NotEmpty')) {
                    $error = true;
                }
                if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }
                if ($error) {
                    throw new \Exception();
                }

                $storeScope = ScopeInterface::SCOPE_STORE;

                $eventCollection = $this->eventsCollectionFactory->create();
                $eventCollection->addFieldToSelect('*');
                $eventCollection->addFieldToFilter('main_table.status', 1);
                $eventCollection->addFieldToFilter('event_id', $post['eventid']);
                $eventCollection->setPageSize(1);

                $storeLocatorTable = 'ns_store_locator';

                $eventCollection->getSelect()->joinLeft(
                    ['sl' => $storeLocatorTable],
                    'sl.store_locator_id = main_table.store_locator_id',
                    ['sl.email']
                );

                $toEmail = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
                if ($eventCollection) {
                    foreach ($eventCollection as $event) {
                        $toEmail = $event->getEmail();
                    }
                }

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                    ->setTemplateOptions(
                        [
                            'area' => FrontNameResolver::AREA_CODE,
                            'store' => Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                    ->addTo($toEmail)
                    ->addBcc($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->setReplyTo($post['email'])
                    ->getTransport();

                $transport->sendMessage();

                /*
                $this->messageManager->addSuccessMessage(
                    __('Thanks for registering with this event.')
                );
                */
                /*
                $this->_redirect(
                    NetstarterStore::URL_PREFIX.'/'.
                    Events::URL_PREFIX.'/'.
                    $post['identifier'], ['?success=1' => '1']
                );
                */
                $redirectUrl = $this->storeManager->getStore()->getUrl(NetstarterStore::URL_PREFIX.'/' . Events::URL_PREFIX . '/' . $post['identifier']).'?param=eventsuccess';

                $this->getResponse()->setRedirect($redirectUrl);

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect(
                    NetstarterStore::URL_PREFIX.'/'.
                    Events::URL_PREFIX.'/'.
                    $post['identifier']
                );

                return;
            }
        }
    }
}
