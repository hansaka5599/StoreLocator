<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Category\Window;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Form.
 */
class Form extends Generic
{
    /**
     * Variable wysiwygConfig.
     *
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry             $registry
     * @param FormFactory     $formFactory
     * @param Config       $wysiwygConfig
     * @param Data   $cameraHouseHelper
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Data $cameraHouseHelper,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Function _prepareForm.
     *
     * @return $this|null
     */
    public function _prepareForm()
    {
        $configServiceId = $this->cameraHouseHelper->getConfigServiceCatId();
        $configPrintShopId = $this->cameraHouseHelper->getConfigPrintShopCatId();

        $categoryId = $this->getRequest()->getParam('category_id');
        if ($configServiceId != $categoryId && $configPrintShopId != $categoryId) {
            return null;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);

        /*
         * Checking if user have permissions to save information
         */

        //Removing permission check condition to allow all users to edit CHSE-1117
        $isElementDisabled = false;
        /*if ($this->_isAllowedAction('Netstarter_StoreLocator::store_locator_category_save')) {
            $isElementDisabled = false;
        }*/

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Service Details')]);

        $fieldset->addField(
            'service_category',
            'select',
            [
                'name' => 'service_category',
                'label' => __('Service Category'),
                'title' => __('Service Category'),
                'required' => true,
                'options' => [0 => 'Please Select'],
                'class' => 'required-entry',
                'note' => 'Please Select each and every service to update content data',
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'dedicated_page',
            'select',
            [
                'name' => 'dedicated_page',
                'label' => __('Dedicated Page'),
                'title' => __('Dedicated Page'),
                'required' => true,
                'class' => 'required-entry',
                'options' => [0 => 'No', 1 => 'Yes'],
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'page_title',
            'text',
            [
                'name' => 'page_title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'content_heading',
            'text',
            [
                'name' => 'content_heading',
                'label' => __('Content Heading'),
                'title' => __('Content Heading'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig(),
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'meta_keyword',
            'text',
            [
                'name' => 'meta_keyword',
                'label' => __('Meta Keyword'),
                'title' => __('Meta Keyword'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'meta_data',
            'textarea',
            [
                'name' => 'meta_data',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        if ($configPrintShopId == $categoryId) {
            $fieldset->addField(
                'redirect_url',
                'text',
                [
                    'name' => 'redirect_url',
                    'label' => __('Redirect Url'),
                    'title' => __('Redirect Url'),
                    'required' => false,
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @param $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
