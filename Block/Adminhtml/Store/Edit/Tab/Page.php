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
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Netstarter\Bannerslider\Model\Banner;

/**
 * Class Page.
 */
class Page extends \Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Tab\Page
{
    /**
     * Variable _wysiwygConfig.
     *
     * @var Config
     */
    protected $_wysiwygConfig;

    /**
     * Variable bannerModel.
     *
     * @var Banner
     */
    protected $bannerModel;

    /**
     * Page constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Banner $bannerModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Banner $bannerModel,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->bannerModel = $bannerModel;
        parent::__construct($context, $registry, $formFactory, $wysiwygConfig, $data);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Prepare form
     *{@inheritdoc}
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
        $fieldsetPage = $form->addFieldset('page_fieldset', ['legend' => __('Store Page Information')]);
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $fieldsetPage->addField(
            'store_image',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'store_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetPage->addField(
            'slider_id',
            'select',
            [
                'label' => __('Banner Slider'),
                'name' => 'slider_id',
                'values' => $this->bannerModel->getAvailableSlides(),
            ]
        );

        $fieldsetPage->addField(
            'store_content',
            'editor',
            [
                'name' => 'store_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'required' => false,
                'style' => 'height:36em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig,
            ]
        );
        $fieldsetPage->addField(
            'how_to_get',
            'editor',
            [
                'name' => 'how_to_get',
                'label' => __('How to Get Text Content'),
                'title' => __('How to Get Text Content'),
                'required' => false,
                'style' => 'height:36em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig,
            ]
        );
        $fieldsetPage->addField(
            'use_default_oh',
            'checkbox',
            [
                'name' => 'use_default_oh',
                'label' => __('Opening Hours'),
                'title' => __('Opening Hours'),
                'note' => '<b>Use default</b>',
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked($model->getData('use_default_oh') ? true : false);
        $fieldsetPage->addField(
            'store_opening_hours',
            'textarea',
            [
                'name' => 'store_opening_hours',
                'required' => false,
                'note' => __('0|9:00 am-5:00 pm,1|9:00 am-5:00 pm,2|9:00 am-5:00 pm,3|9:00 am-5:00 pm,4|9:00 am-5:00 pm,5|9:00 am-5:00 pm,6|9:00 am-5:00 pm<br/>
                    Each day must separated by comma, time must separated by "-", 0 = Sunday, if store is not open for a particular day it\'s possible to skip the value'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetPage->addField(
            'use_default_ho',
            'checkbox',
            [
                'name' => 'use_default_ho',
                'label' => __('Holidays'),
                'title' => __('Holidays'),
                'note' => '<b>Use default</b>',
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked($model->getData('use_default_ho') ? true : false);
        $fieldsetPage->addField(
            'store_holiday_hours',
            'textarea',
            [
                'name' => 'store_holiday_hours',
                'required' => false,
                'note' => __('2015-01-01|10:30 am-4:00 pm|New Year,2015-04-03||Good Friday<br/>
                    Each line must be like : {yyyy-mm-dd} ISO8601|{opentime}|{Holiday Name}, if store is closed please put empty value in time slot.<br/>
                    When copy paste from PDF, check the single quote which might be not UTF-8 format'),
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetPage->addField(
            'store_service_content',
            'editor',
            [
                'name' => 'store_service_content',
                'label' => __('Service Content'),
                'title' => __('Service Content'),
                'required' => false,
                'style' => 'height:36em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig,
            ]
        );

        $fieldsetMeta = $form->addFieldset('meta_fieldset', ['legend' => __('Store Meta Information')]);

        $fieldsetMeta->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetMeta->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        //Start Store Page Banners
        $fieldsetBanner = $form->addFieldset('store_page_banner_fieldset', ['legend' => __('Store Page Spot Banners')]);
        $fieldsetBanner->addField(
            'store_page_spot_image_1',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'store_page_spot_image_1',
                'label' => __('Spot Banner 1'),
                'title' => __('Spot Banner 1'),
                'required' => false,
                'disabled' => false,
            ]
        );
        $fieldsetBanner->addField(
            'store_page_alt_text_1',
            'text',
            [
                'name' => 'store_page_alt_text_1',
                'label' => __('Alt Text 1'),
                'title' => __('Alt Text 1'),
                'required' => false,
                'disabled' => false,
            ]
        );
        $fieldsetBanner->addField(
            'store_page_spot_url_1',
            'text',
            [
                'name' => 'store_page_spot_url_1',
                'label' => __('URL 1'),
                'title' => __('URL 1'),
                'required' => false,
                'disabled' => false,
            ]
        );

        $fieldsetBanner->addField(
            'store_page_spot_image_2',
            'Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Helper\Image',
            [
                'name' => 'store_page_spot_image_2',
                'label' => __('Spot Banner 2'),
                'title' => __('Spot Banner 2'),
                'required' => false,
                'disabled' => false,
            ]
        );
        $fieldsetBanner->addField(
            'store_page_alt_text_2',
            'text',
            [
                'name' => 'store_page_alt_text_2',
                'label' => __('Alt Text 2'),
                'title' => __('Alt Text 2'),
                'required' => false,
                'disabled' => false,
            ]
        );
        $fieldsetBanner->addField(
            'store_page_spot_url_2',
            'text',
            [
                'name' => 'store_page_spot_url_2',
                'label' => __('URL 2'),
                'title' => __('URL 2'),
                'required' => false,
                'disabled' => false,
            ]
        );

        $this->_eventManager->dispatch('adminhtml_store_locator_store_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

    }
}
