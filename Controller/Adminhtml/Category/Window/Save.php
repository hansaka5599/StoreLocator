<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml\Category\Window;

/**
 * Class Save.
 */
class Save extends \Netstarter\StoreLocator\Controller\Adminhtml\Category\Window\Save
{
    /**
     * Function aroundExecute.
     *
     * @return $this
     */
    public function aroundExecute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            if ($data && !empty($data['store_locator_id'])) {
                $model = $this->_objectManager->create('Netstarter\StoreLocator\Model\Store');
                $categoryId = $data['category_id'];
                $services = json_decode($data['store_services'], true);
                $model->saveCategories($data['store_locator_id'], $categoryId, $services);
                $result = ['error' => '', 'errorcode' => ''];
            } else {
                $result = ['error' => 'Invalid Request', 'errorcode' => ''];
            }
        } catch (\Exception $e) {
            $result = ['error' => 'Error saving nodes. '.$e->getMessage(), 'errorcode' => $e->getCode()];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($result);
    }
}
