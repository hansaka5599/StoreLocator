<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Events\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Magento\Cms\Model\Wysiwyg\Config;
use CameraHouse\StoreLocator\Helper\Data as ChHelper;

/**
 * Class Form.
 */
class Form extends Generic
{
    /**
     * Variable systemStore.
     *
     * @var Store
     */
    protected $systemStore;

    /**
     * Variable wysiwygConfig.
     *
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * Variable cameraHouseHelper.
     *
     * @var ChHelper
     */
    protected $cameraHouseHelper;

    /**
     * Variable session.
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * Form constructor.
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param Store       $systemStore
     * @param Config      $wysiwygConfig
     * @param ChHelper    $cameraHouseHelper
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Config $wysiwygConfig,
        ChHelper $cameraHouseHelper,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->cameraHouseHelper = $cameraHouseHelper;
        $this->session = $context->getSession();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Function _construct.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('eventItemForm');
        $this->setTitle(__('Event Information'));
        $this->setUseContainer(true);
    }

    /**
     * Function _prepareForm.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_storelocator_event');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        $fieldsetInfo = $form->addFieldset(
            'base_fieldset_info',
            ['legend' => __('Event Information')]
        );

        $fieldsetContent = $form->addFieldset(
            'base_fieldset_content',
            ['legend' => __('Event Content')]
        );

        $fieldsetMeta = $form->addFieldset(
            'base_fieldset_meta',
            ['legend' => __('Meta Data')]
        );

        $isElementDisabled = false;
        $options = $this->cameraHouseHelper->getMyAssignedStoreDropdownOptions();
        if ($model && $model->getId()) {
            $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
            $storeLocatorId = $model->getStoreLocatorId();

            if ($isGenericAdmin === false) {
                $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
                if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                    $options = $this->cameraHouseHelper->getMyAssignedStoreDropdownOptions(true);
                    $isElementDisabled = true;
                }
            }
        }

        $fieldsetInfo->addField(
            'store_locator_id',
            'select',
            [
                'name' => 'store_locator_id',
                'label' => __('Store Locator'),
                'class' => 'required-entry',
                'required' => true,
                'options' => $options,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetInfo->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'class' => 'required-entry validate-identifier',
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetInfo->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetInfo->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'class' => 'required-entry',
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => [0 => 'Disabled', 1 => 'Enabled'],
            ]
        );

        $fieldsetContent->addField(
            'page_title',
            'text',
            [
                'name' => 'page_title',
                'label' => __('Page Title'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetContent->addField(
            'content_heading',
            'text',
            [
                'name' => 'content_heading',
                'label' => __('Content Heading'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetContent->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'config' => $this->wysiwygConfig->getConfig(),
            ]
        );

        $fieldsetMeta->addField(
            'meta_keyword',
            'text',
            [
                'name' => 'meta_keyword',
                'label' => __('Meta Keyword'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetMeta->addField(
            'meta_data',
            'textarea',
            [
                'name' => 'meta_data',
                'label' => __('Meta Data'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        if ($model && $model->getId()) {
            $form->setValues($model);
            $fieldsetInfo->addField('event_id', 'hidden', ['name' => 'event_id', 'value' => $model->getEventId()]);
        }

        /*CHSE-971 and CHSE-972*/
        $formData = $this->session->getFormData();
        if ($formData) {
            $form->setValues(json_decode($formData, true));
        }

        $form->setAction($this->getUrl('ch_store_locator/events/save'));
        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
