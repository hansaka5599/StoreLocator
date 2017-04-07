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

use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Config;

/**
 * Class Info.
 */
class Info extends \Netstarter\StoreLocator\Block\Adminhtml\Store\Edit\Tab\Info
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Prepare form.
     *
     * @return $this
     *               {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Netstarter\StoreLocator\Model\Store */
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
        $formData = $model->getData();
        $countries = $this->country->toOptionArray(false, 'US');
        if (!isset($formData['country_id'])) {
            $formData['country_id'] = $this->_scopeConfig->getValue(
                Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                ScopeInterface::SCOPE_STORE
            );
        }

        $regionCollection = $this->regionFactory->create()->getCollection()->addCountryFilter(
            $formData['country_id']
        );

        $regions = $regionCollection->toOptionArray();
        if (count($regions) == 0) {
            array_unshift(
                $regions,
                ['title ' => null, 'value' => null, 'label' => __('Please select a region, state or province.')]
            );
        }

        $fieldsetContact = $form->addFieldset('contact_fieldset', ['legend' => __('Store Contact Information')]);

        $fieldsetContact->addField(
            'heading_name',
            'text',
            [
                'name' => 'heading_name',
                'label' => __('Heading Name (H1)'),
                'title' => __('Heading Name  (H1)'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetContact->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetContact->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetContact->addField(
            'fax',
            'text',
            [
                'name' => 'fax',
                'label' => __('Fax'),
                'title' => __('Fax'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetContact->addField(
            'is_new',
            'checkbox',
            [
                'name' => 'is_new',
                'label' => __('Is New'),
                'title' => __('Is New'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked(isset($formData['is_new']) && $formData['is_new'] ? true : false);

        $fieldsetAddress = $form->addFieldset('address_fieldset', ['legend' => __('Store Address Information')]);
        $fieldsetAddress->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('Street 1'),
                'title' => __('Street 1'),
                'required' => true,
                'note' => __('Address line 1'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'street2',
            'text',
            [
                'name' => 'street2',
                'label' => __('Street 2'),
                'title' => __('Street 2'),
                'required' => false,
                'note' => __('Address line 2'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'suburb',
            'text',
            [
                'name' => 'suburb',
                'label' => __('Suburb'),
                'title' => __('Suburb'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'region_id',
            'select',
            [
                'name' => 'region_id',
                'label' => __('State'),
                'title' => __('State'),
                'required' => true,
                'values' => $regions,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
                'values' => $countries,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'full_address',
            'label',
            [
                'name' => 'full_address',
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html' => $this->getAfterHtml(),
            ]
        );

        $fieldsetAddress->addField(
            'latitude',
            'text',
            [
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetAddress->addField(
            'longitude',
            'text',
            [
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetSubdomain = $form->addFieldset('subdomain_fieldset', ['legend' => __('Sub Domain Information')]);
        $fieldsetSubdomain->addField(
            'subdomain',
            'text',
            [
                'name' => 'subdomain',
                'label' => __('Sub Domain'),
                'title' => __('Sub Domain'),
                'required' => true,
                'note' => __('i.e. erina.camerahouse.com.au'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetSubdomain->addField(
            'subdomain_priority',
            'text',
            [
                'name' => 'subdomain_priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldsetHeaderMenu = $form->addFieldset('headermenu_fieldset', ['legend' => __('Header Menu URLs')]);
        $fieldsetHeaderMenu->addField(
            'photo_creation_url',
            'text',
            [
                'name' => 'photo_creation_url',
                'label' => __('Photo Creations'),
                'title' => __('Photo Creations'),
                'required' => false,
                'note' => __('Destination URL for the store'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetHeaderMenu->addField(
            'digital_print_url',
            'text',
            [
                'name' => 'digital_print_url',
                'label' => __('Digital Prints'),
                'title' => __('Digital Prints'),
                'required' => false,
                'note' => __('Destination URL for the store'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetHeaderMenu->addField(
            'event_photography_url',
            'text',
            [
                'name' => 'event_photography_url',
                'label' => __('Event Photography'),
                'title' => __('Event Photography'),
                'required' => false,
                'note' => __('Destination URL for the store'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldsetHeaderMenu->addField(
            'latest_offers_url',
            'text',
            [
                'name' => 'latest_offers_url',
                'label' => __('Latest Offers'),
                'title' => __('Latest Offers'),
                'required' => false,
                'note' => __('Destination URL for the store'),
                'disabled' => $isElementDisabled,
            ]
        );

        $this->_eventManager->dispatch('adminhtml_store_locator_store_edit_tab_info_prepare_form', ['form' => $form]);

        $form->setValues($formData);
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('Netstarter_StoreLocator::store/info/js.phtml')
        );

        //return parent::_prepareForm();
    }
}
