<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Adminhtml\Store;

use CameraHouse\StoreLocator\Helper\Data as CameraHouseHelperData;
use Magento\Backend\App\Action;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Netstarter\StoreLocator\Helper\Data;

/**
 * Class Save.
 */
class Save extends \Netstarter\StoreLocator\Controller\Adminhtml\Store\Save
{
    /**
     * Variable filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Variable fileUploaderFactory.
     *
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * Variable storeLocatorHelper.
     *
     * @var Data
     */
    protected $storeLocatorHelper;

    /**
     * Variable cameraHouseHelper.
     *
     * @var CameraHouseHelperData
     */
    protected $cameraHouseHelper;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param Data $storeLocatorHelper
     * @param CameraHouseHelperData $cameraHouseHelper
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        Data $storeLocatorHelper,
        CameraHouseHelperData $cameraHouseHelper,
        TypeListInterface $cacheTypeList
    ) {
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeLocatorHelper = $storeLocatorHelper;
        $this->cameraHouseHelper = $cameraHouseHelper;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context, $filesystem, $fileUploaderFactory);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function execute.
     *
     * @return $this
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Netstarter\StoreLocator\Model\Store');

            $id = $this->getRequest()->getParam('store_locator_id');
            if ($id) {
                $model->load($id);
            }

            if (empty($data['url_key'])) {
                $data['url_key'] = strtolower(str_replace(' ', '-', $data['name']));
            }

            try {
                $data = $this->setImage($data);
                $data = $this->setSpotLightImages($data);
                $data = $this->setStorePageSpotLightImages($data);

                $data['is_new'] = isset($data['is_new']) ? 1 : 0;
                if (isset($data['use_default_oh'])) {
                    $data['use_default_oh'] = 1;
                    $data['store_opening_hours'] = null;
                } else {
                    $data['use_default_oh'] = 0;
                }
                if (isset($data['use_default_ho'])) {
                    $data['use_default_ho'] = 1;
                    $data['store_holiday_hours'] = null;
                } else {
                    $data['use_default_ho'] = 0;
                }
                $model->setData($data);

                $this->_eventManager->dispatch(
                    'ns_store_locator_store_prepare_save',
                    ['store' => $model, 'request' => $this->getRequest()]
                );
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved this store.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit',
                        ['store_locator_id' => $model->getId(), '_current' => true]);
                }

                /**
                 * Start invalidating cache for this store.
                 */
                $types = ['full_page', 'store_locator'];
                foreach ($types as $type) {
                    $this->cacheTypeList->cleanType($type);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the store.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit',
                ['store_locator_id' => $this->getRequest()->getParam('store_locator_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Save Home page CTA spot banners.
     *
     * @param $data
     *
     * @return mixed
     */
    protected function setSpotLightImages($data)
    {
        $uploader1 = $this->uploadFile('spot_image_1');
        $uploader2 = $this->uploadFile('spot_image_2');
        $uploader3 = $this->uploadFile('spot_image_3');

        // if no images were set - nothing to do
        /*if ($uploader1==null && $uploader2==null && $uploader3==null) {
            return $data;
        }*/
        if (!isset($data['spot_image_1'])) {
            $data['spot_image_1'] = null;
        }
        if (!isset($data['spot_image_2'])) {
            $data['spot_image_2'] = null;
        }
        if (!isset($data['spot_image_3'])) {
            $data['spot_image_3'] = null;
        }

        $image_1 = $data['spot_image_1'];
        if (is_array($image_1) && !empty($image_1['delete'])) {
            $data['spot_image_1'] = '';
        } elseif ($uploader1==null) {
            unset($data['spot_image_1']);
        } else {
            $data['spot_image_1'] = $uploader1;
        }

        $image_2 = $data['spot_image_2'];
        if (is_array($image_2) && !empty($image_2['delete'])) {
            $data['spot_image_2'] = '';
        } elseif ($uploader1==null) {
            unset($data['spot_image_2']);
        } else {
            $data['spot_image_2'] = $uploader2;
        }

        $image_3 = $data['spot_image_3'];
        if (is_array($image_3) && !empty($image_3['delete'])) {
            $data['spot_image_3'] = '';
        } elseif ($uploader1==null) {
            unset($data['spot_image_3']);
        } else {
            $data['spot_image_3'] = $uploader3;
        }

        return $data;
    }

    /**
     * Upload file
     * @param $input
     * @return Magento\MediaStorage\Model\File\Uploader
     */
    public function uploadFile($input)
    {
        try {
            $destinationFolder = $this->getDestinationPath();
            $uploader = $this->fileUploaderFactory->create(['fileId' => $input]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($destinationFolder);
            return $result['file'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get destination path
     * @return string
     */
    public function getDestinationPath()
    {
        return $this->filesystem->getDirectoryRead(
                    DirectoryList::MEDIA
                )->getAbsolutePath(
                    constant(get_class($this->storeLocatorHelper).'::LOCATION_IMAGE_PATH').'/'
                );
    }

    /**
     * Save Store detail page CTA spot banners.
     *
     * @param $data
     *
     * @return mixed
     */
    protected function setStorePageSpotLightImages($data)
    {
        $uploader1 = $this->uploadFile('store_page_spot_image_1');
        $uploader2 = $this->uploadFile('store_page_spot_image_2');

        if (!isset($data['store_page_spot_image_1'])) {
            $data['store_page_spot_image_1'] = null;
        }
        if (!isset($data['store_page_spot_image_2'])) {
            $data['store_page_spot_image_2'] = null;
        }

        $image_1 = $data['store_page_spot_image_1'];
        if (is_array($image_1) && !empty($image_1['delete'])) {
            $data['store_page_spot_image_1'] = '';
        } elseif ($uploader1==null) {
            unset($data['store_page_spot_image_1']);
        } else {
            $data['store_page_spot_image_1'] = $uploader1;
        }

        $image_2 = $data['store_page_spot_image_2'];
        if (is_array($image_2) && !empty($image_2['delete'])) {
            $data['store_page_spot_image_2'] = '';
        } elseif ($uploader2==null) {
            unset($data['store_page_spot_image_2']);
        } else {
            $data['store_page_spot_image_2'] = $uploader2;
        }

        return $data;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        //Check user role and allowed user list and remove save functionality
        $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
        $storeLocatorId = $this->getRequest()->getPostValue('store_locator_id');

        //Check user role and allowed user list and remove save functionality
        if ($isGenericAdmin === false) {
            $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
            if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                return false;
            } else {
                return $this->_authorization->isAllowed('Netstarter_StoreLocator::store_locator_store_save');
            }
        } else {
            return $this->_authorization->isAllowed('Netstarter_StoreLocator::store_locator_store_save');
        }
    }
}
