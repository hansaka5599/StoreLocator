<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml\Category;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save.
 */
class Save extends \Netstarter\StoreLocator\Controller\Adminhtml\Category\Save
{
    /**
     * Function aroundExecute.
     *
     * @return $this
     */
    public function aroundExecute()
    {
        $data = $this->getRequest()->getPostValue('category');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $node = $this->_objectManager->create(
                'Netstarter\StoreLocator\Model\Category\Node'
            );

            if (!empty($data['nodes_data'])) {
                try {
                    $nodesData = $this->_objectManager->get(
                        'Magento\Framework\Json\Helper\Data'
                    )->jsonDecode(
                        $data['nodes_data']
                    );
                } catch (\Zend_Json_Exception $e) {
                    $nodesData = [];
                }
            } else {
                $nodesData = [];
            }
            if (!empty($data['removed_nodes'])) {
                $removedNodes = explode(',', $data['removed_nodes']);
            } else {
                $removedNodes = [];
            }

            $data['in_detail_page'] = isset($data['in_detail_page']) ? 1 : 0;
            $data['in_detail_page_side_pane'] = isset($data['in_detail_page_side_pane']) ? 1 : 0;
            $data['in_main_page_side_pane'] = isset($data['in_main_page_side_pane']) ? 1 : 0;

            if ($nodesData) {
                foreach ($nodesData as $nodeDataItem) {
                    if ($nodeDataItem['name'] == '') {
                        unset($nodesData[$nodeDataItem['node_id']]);
                    }
                }
            }

            $node->collectTree($nodesData, $removedNodes, $data['url_prefix']);

            $model = $this->_objectManager->create('Netstarter\StoreLocator\Model\Category');
            $id = $this->getRequest()->getParam('category_id');
            if ($id) {
                $model->load($id);
            }

            try {
                $model->setData($data);
                $this->_eventManager->dispatch(
                    'ns_store_locator_category_prepare_save',
                    ['store' => $model, 'request' => $this->getRequest()]
                );
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved this category.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['category_id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the category.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit',
                ['category_id' => $this->getRequest()->getParam('category_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
