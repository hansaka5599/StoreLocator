<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Store\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class Home.
 */
class Home extends Generic implements TabInterface
{
    /**
     * Home constructor.
     *
     * @param Context $context
     * @param Registry             $registry
     * @param FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Home Page Banners');
    }

    /**
     * Prepare title for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Home Page Banners');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Function _prepareForm.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('store_locator');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netstarter_StoreLocator::store_locator_store_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('store_');
        $fieldsetPage = $form->addFieldset('page_fieldset', ['legend' => __('Home Page Spot Banners')]);
        $fieldsetPage->addField(
            'spot_image_1',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'spot_image_1',
                'label' => __('Spot Banner 1'),
                'title' => __('Spot Banner 1'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'alt_text_1',
            'text',
            [
                'name' => 'alt_text_1',
                'label' => __('Alt Text 1'),
                'title' => __('Alt Text 1'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'spot_url_1',
            'text',
            [
                'name' => 'spot_url_1',
                'label' => __('URL 1'),
                'title' => __('URL 1'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetPage->addField(
            'spot_image_2',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'spot_image_2',
                'label' => __('Spot Banner 2'),
                'title' => __('Spot Banner 2'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'alt_text_2',
            'text',
            [
                'name' => 'alt_text_2',
                'label' => __('Alt Text 2'),
                'title' => __('Alt Text 2'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'spot_url_2',
            'text',
            [
                'name' => 'spot_url_2',
                'label' => __('URL 2'),
                'title' => __('URL 2'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetPage->addField(
            'spot_image_3',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'spot_image_3',
                'label' => __('Spot Banner 3'),
                'title' => __('Spot Banner 3'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'alt_text_3',
            'text',
            [
                'name' => 'alt_text_3',
                'label' => __('Alt Text 3'),
                'title' => __('Alt Text 3'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'spot_url_3',
            'text',
            [
                'name' => 'spot_url_3',
                'label' => __('URL 3'),
                'title' => __('URL 3'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action.
     *
     * @param string $resourceId
     *
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
