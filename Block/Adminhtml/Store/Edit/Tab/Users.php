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

use CameraHouse\StoreLocator\Model\Users as CameraHouseUsers;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\User\Model\UserFactory;

/**
 * Class Users.
 */
class Users extends Generic implements TabInterface
{
    /**
     * Variable adminSession.
     *
     * @var Session
     */
    protected $adminSession;

    /**
     * Variable userFactory.
     *
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * Variable cameraHouseUsersModel.
     *
     * @var CameraHouseUsers
     */
    protected $cameraHouseUsersModel;

    /**
     * Users constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param UserFactory $userFactory
     * @param Session $adminSession
     * @param CameraHouseUsers $cameraHouseUsersModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        UserFactory $userFactory,
        Session $adminSession,
        CameraHouseUsers $cameraHouseUsersModel,
        array $data = []
    ) {
        $this->userFactory = $userFactory;
        $this->adminSession = $adminSession;
        $this->cameraHouseUsersModel = $cameraHouseUsersModel;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Store Managers');
    }

    /**
     * Prepare title for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Store Managers');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $currentRoleId = $this->adminSession->getUser()->getRole()->getRoleId();
        if ($currentRoleId == '1') {
            return true;
        } else {
            return false;
        }
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

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('store_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Store Managers'), 'class' => 'fieldset-wide']
        );

        $adminUsers = $this->getOptionArray();

        $fieldset->addField(
            'user_ids',
            'multiselect',
            [
                'name' => 'user_ids[]',
                'label' => __('Store Managers'),
                'title' => __('Store Managers'),
                'required' => false,
                'values' => $adminUsers,
                'disabled' => false,
            ]
        );

        $assignedUsers = $this->getAllowedUsers();
        $form->setValues($assignedUsers);
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

    /**
     * Function getOptionArray.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $adminUserCollection = $this->userFactory->create()->getCollection();
        $optionsArray = $adminUserCollection->getData();
        $options[] = ['label' => '', 'value' => ''];
        if (!empty($optionsArray)) {
            foreach ($optionsArray as $user) {
                if ($user['is_active'] == '1') {
                    $options[] = ['value' => $user['user_id'], 'label' => $user['firstname'].' '.$user['lastname']];
                }
            }
        }

        return $options;
    }

    /**
     * Function getAllowedUsers.
     *
     * @return array
     */
    public function getAllowedUsers()
    {
        $storeLocatorId = $this->_coreRegistry->registry('store_locator')->getStoreLocatorId();
        $collection = $this->cameraHouseUsersModel->getCollection()
            ->addFieldToFilter('store_locator_id', $storeLocatorId);
        $allowedUsers = $collection->getData();
        $userList = ['user_ids' => []];
        if (!empty($allowedUsers)) {
            foreach ($allowedUsers as $user) {
                $userList['user_ids'][] = $user['user_id'];
            }
        }

        return $userList;
    }
}
