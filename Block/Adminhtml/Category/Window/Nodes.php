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
use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Json\EncoderInterface;
use Netstarter\StoreLocator\Model\ResourceModel\Category\Node\Collection;

/**
 * Class Nodes.
 */
class Nodes extends \Netstarter\StoreLocator\Block\Adminhtml\Category\Window\Nodes
{
    /**
     * Variable formFactory.
     *
     * @var $formFactory
     */
    private $formFactory;

    /**
     * Variable wysiwygConfig.
     *
     * @var $wysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Nodes constructor.
     *
     * @param Context    $context
     * @param EncoderInterface $jsonEncoder
     * @param Collection                               $nodeCollection
     * @param Config        $wysiwygConfig
     * @param Data    $cameraHouseHelper
     * @param array                                    $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Collection $nodeCollection,
        Config $wysiwygConfig,
        Data $cameraHouseHelper,
        array $data = []
    ) {
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $jsonEncoder, $nodeCollection, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    public function prepareForm()
    {
        $configServiceId = $this->cameraHouseHelper->getConfigServiceCatId();
        $categoryId = $this->getRequest()->getParam('category_id');
        if ($configServiceId != $categoryId) {
            return null;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->formFactory->create(
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
        if ($this->_isAllowedAction('Netstarter_StoreLocator::store_locator_category_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

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

        $form->setUseContainer(true);
        $this->setForm($form);

        return $form->getHtml();
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
