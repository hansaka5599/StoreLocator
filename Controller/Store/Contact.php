<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Store;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class Contact.
 */
class Contact extends Action
{
    /**
     * Recipient email config path.
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email'; //contact_email_recipient_email

    /**
     * Sender email config path.
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Email template config path.
     */
    const XML_PATH_EMAIL_TEMPLATE = 'netstarter_storelocator/location/store_email_template';

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
     * Variable _objectManager.
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Contact constructor.
     *
     * @param Context              $context
     * @param TransportBuilder  $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_objectManager = $context->getObjectManager();
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Post user question.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect($this->_redirect->getRefererUrl());

            return;
        }
        if ($post) {
            //Get Store Details
            $model = $this->_objectManager->create('Netstarter\StoreLocator\Model\Store');
            $id = $this->getRequest()->getParam('store_locator_id');
            if ($id) {
                $store = $model->load($id);
                if ($store->getStoreLocatorId()) {
                    try {
                        $post['store'] = $store->getName();
                        $postObject = new DataObject();
                        $postObject->setData($post);

                        $error = false;

                        if (!\Zend_Validate::is(trim($post['first_name']), 'NotEmpty')) {
                            $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['last_name']), 'NotEmpty')) {
                            $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                            $error = true;
                        }
                        if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                            $error = true;
                        }
                        if ($error) {
                            throw new \Exception();
                        }

                        $storeScope = ScopeInterface::SCOPE_STORE;

                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier(
                                $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope)
                            )
                            ->setTemplateOptions(
                                [
                                    'area' => FrontNameResolver::AREA_CODE,
                                    'store' => Store::DEFAULT_STORE_ID,
                                ]
                            )
                            ->setTemplateVars(['data' => $postObject])
                            ->setFrom(
                                $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope)
                            )//Sender specified in configuration
                            ->addTo($store->getEmail())//Main recipient is the store
                            ->addBcc(
                                $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope)
                            )//BCC this contact to CH main office
                            ->setReplyTo($post['email'])
                            ->getTransport();

                        $transport->sendMessage();

                        $this->messageManager->addSuccessMessage(
                            __('Thanks for contacting us. We\'ll get back to you shortly.')
                        );
                        $this->_redirect($this->_redirect->getRefererUrl());
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(
                            __('We can\'t process your request right now. Sorry, that\'s all we know.')
                        );
                        $this->_redirect($this->_redirect->getRefererUrl());

                        return;
                    }
                } else {
                    $this->messageManager->addErrorMessage(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                    $this->_redirect($this->_redirect->getRefererUrl());
                }
            } else {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        }
    }
}
