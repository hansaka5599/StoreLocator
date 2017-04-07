<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Category\Edit;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Form.
 */
class Form extends Generic
{
    /**
     * Variable categoryModel.
     *
     * @var mixed
     */
    protected $categoryModel;

    /**
     * Variable jsonEncoder.
     *
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * Variable _systemStore.
     *
     * @var Store
     */
    protected $_systemStore;

    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * Form constructor.
     *
     * @param Context  $context
     * @param Registry              $registry
     * @param FormFactory      $formFactory
     * @param EncoderInterface $jsonEncoder
     * @param Store        $systemStore
     * @param Data    $cameraHouseHelper
     * @param array                                    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        EncoderInterface $jsonEncoder,
        Store $systemStore,
        Data $cameraHouseHelper,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->setTemplate('category/edit.phtml');
        $this->cameraHouseHelper = $cameraHouseHelper;
        $this->categoryModel = $this->_coreRegistry->registry('store_locator_category');
    }

    /**
     * Function showSubCategoryLayer.
     *
     * @return bool
     */
    public function showSubCategoryLayer()
    {
        $catId = $this->categoryModel->getCategoryId();

        return !empty($catId) ? true : false;
    }

    /**
     * Function getSubCategoryForm.
     *
     * @return string
     */
    public function getSubCategoryForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_sub_category_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setHtmlIdPrefix('sub_cat_');
        $form->setUseContainer(true);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Sub Category Details')]);

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'onchange' => 'hierarchyNodes.nodeChanged()',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('Request Url'),
                'title' => __('Request Url'),
                'onchange' => 'hierarchyNodes.nodeChanged()',
                'required' => true,
                'class' => 'validate-identifier',
            ]
        );
        $fieldset->addField(
            'text_content',
            'textarea',
            [
                'name' => 'text_content',
                'label' => __('Text Content'),
                'title' => __('Text Content'),
                'onchange' => 'hierarchyNodes.nodeChanged()',
                'required' => false,
            ]
        );

        /*Icon and Tile Images - only available for service category*/

        $configServiceId = $this->cameraHouseHelper->getConfigServiceCatId();
        if ($this->categoryModel->getCategoryId() == $configServiceId) {
            $fieldset->addField(
                'category_icon',
                'image',
                [
                    'name' => 'category_icon',
                    'label' => __('Category Icon'),
                    'title' => __('Category Icon'),
                    'onchange' => 'hierarchyNodes.nodeChanged()',
                ]
            );

            $form->getElement(
                'category_icon'
            )->setRenderer(
                $this->getLayout()->createBlock('CameraHouse\StoreLocator\Block\Adminhtml\Category\Image\Renderer')
            );

            $fieldset->addField(
                'category_icon_tile',
                'hidden',
                [
                    'id' => 'category_icon_tile',
                    'name' => 'category_icon_tile',
                    'onchange' => 'hierarchyNodes.nodeChanged()',
                    'required' => true,
                ]
            );
        }

        return $form->getHtml();
    }

    /**
     * Retrieve buttons HTML for Pages Tree.
     *
     * @return string
     */
    public function getTreeButtonsHtml()
    {
        return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'new_node_button',
                    'label' => __('Add Node...'),
                    'onclick' => 'hierarchyNodes.newNodePage()',
                    'class' => 'add',
                ]
            )
            ->toHtml();
    }

    /**
     * Retrieve Buttons HTML for Page Properties form.
     *
     * @return string
     */
    public function getPagePropertiesButtons()
    {
        $buttons = [];
        $buttons[] = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'delete_node_button',
                    'label' => __('Remove from tree.'),
                    'onclick' => 'hierarchyNodes.deleteNodePage()',
                    'class' => 'delete',
                ]
            )
            ->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'cancel_node_button',
                    'label' => __('Cancel'),
                    'onclick' => 'hierarchyNodes.cancelNodePage()',
                    'class' => 'cancel',
                ]
            )
            ->toHtml();
        $buttons[] = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'save_node_button',
                    'label' => __('Save'),
                    'onclick' => 'hierarchyNodes.saveNodePage()',
                    'class' => 'save',
                ]
            )
            ->toHtml();

        return implode(' ', $buttons);
    }

    /**
     * Prepare translated label 'Save' for button used in Js.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getButtonSaveLabel()
    {
        return __('Add to tree.');
    }

    /**
     * Function getNodesJson.
     *
     * @return string
     */
    public function getNodesJson()
    {
        $nodes = $this->categoryModel->getNodesData();

        return $this->jsonEncoder->encode($nodes);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Store Category Details')]);

        if ($this->categoryModel->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }
        $fieldset->addField('removed_nodes', 'hidden', ['name' => 'removed_nodes']);
        $fieldset->addField('node_id', 'hidden', ['name' => 'node_id']);
        $fieldset->addField('nodes_data', 'hidden', ['name' => 'nodes_data']);
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'url_prefix',
            'text',
            [
                'name' => 'url_prefix',
                'label' => __('Url Prefix'),
                'title' => __('Url Prefix'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'link_type',
            'select',
            [
                'name' => 'link_type',
                'label' => __('Input Type'),
                'title' => __('Input Type'),
                'required' => true,
                'values' => [0 => 'Url', 1 => 'Checkbox'],
                'note' => 'Note : Categories display type in front end',
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'in_detail_page',
            'checkbox',
            [
                'name' => 'in_detail_page',
                'label' => __('Display in store detail page content'),
                'title' => __('Display in store detail page content'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked($this->categoryModel->getData('in_detail_page') ? true : false);

        $fieldset->addField(
            'in_detail_page_side_pane',
            'checkbox',
            [
                'name' => 'in_detail_page_side_pane',
                'label' => __('Display in store detail page side pane'),
                'title' => __('Display in store detail page side pane'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked($this->categoryModel->getData('in_detail_page_side_pane') ? true : false);

        $fieldset->addField(
            'in_main_page_side_pane',
            'checkbox',
            [
                'name' => 'in_main_page_side_pane',
                'label' => __('Display in store main page side pane'),
                'title' => __('Display in store main page side pane'),
                'required' => false,
                'disabled' => $isElementDisabled,
            ]
        )->setIsChecked($this->categoryModel->getData('in_main_page_side_pane') ? true : false);

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $this->categoryModel->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                'in_detail_page',
                'show'
            )->addFieldMap(
                'link_type',
                'type'
            )->addFieldDependence(
                'show',
                'type',
                1
            )
        );

        $form->setValues($this->categoryModel->getData());
        $form->setFieldNameSuffix('category');
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
