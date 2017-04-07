<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Services;

use CameraHouse\StoreLocator\Model\ResourceModel\Store;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store as MagentoStore;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\Store as NetstarterStore;

/**
 * Class Register.
 */
class Register extends Action
{
    /**
     * Recipient email config path.
     */
    const XML_PATH_EMAIL_RECIPIENT = 'netstarter_storelocator/services/recipient_email';

    /**
     * Sender email config path.
     */
    const XML_PATH_EMAIL_SENDER = 'netstarter_storelocator/services/sender_email_identity';

    /**
     * Email template config path.
     */
    const XML_PATH_EMAIL_TEMPLATE = 'netstarter_storelocator/services/email_template';

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
     * @var $eventsCollectionFactory
     */
    protected $eventsCollectionFactory;

    /**
     * Variable storeResource.
     *
     * @var Store
     */
    protected $storeResource;

    /**
     * Variable storeManager.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Register constructor.
     *
     * @param Context               $context
     * @param TransportBuilder   $transportBuilder
     * @param ScopeConfigInterface  $scopeConfig
     * @param Store $storeResource
     * @param StoreManagerInterface          $storeManager
     * @param Registry                         $coreRegistry
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        Store $storeResource,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->storeResource = $storeResource;
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
            $this->_redirect('*/services/view');

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
                if (!\Zend_Validate::is(trim($post['lname']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['storeid']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['nodeid']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['service']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['identifier']), 'NotEmpty')) {
                    $error = true;
                }
                if (!\Zend_Validate::is(trim($post['store_url_key']), 'NotEmpty')) {
                    $error = true;
                }
                if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }
                if ($error) {
                    throw new \Exception();
                }

                $storeScope = ScopeInterface::SCOPE_STORE;
                $toEmail = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);

                $storeEmail = $this->storeResource->getStoreEmail($post['storeid'],
                    $this->storeManager->getStore()->getId());
                if ($storeEmail) {
                    $toEmail = $storeEmail;
                }

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                    ->setTemplateOptions(
                        [
                            'area' => FrontNameResolver::AREA_CODE,
                            'store' => MagentoStore::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                    ->addTo($toEmail)
                    ->addBcc($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->setReplyTo($post['email'])
                    ->getTransport();

                $transport->sendMessage();

                $this->messageManager->addSuccessMessage(
                    __('Your request has been submitted')
                );
                $this->_redirect(
                    NetstarterStore::URL_PREFIX.'/'.$post['store_url_key'].'/'.$post['identifier']
                );

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect(
                    NetstarterStore::URL_PREFIX.'/'.$post['store_url_key'].'/'.$post['identifier']
                );

                return;
            }
        }
    }
}
