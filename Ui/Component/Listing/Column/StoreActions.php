<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Ui\Component\Listing\Column;

use CameraHouse\StoreLocator\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class StoreActions.
 */
class StoreActions extends \Netstarter\StoreLocator\Ui\Component\Listing\Column\StoreActions
{
    /**
     * Variable cameraHouseHelper.
     *
     * @var Data
     */
    protected $cameraHouseHelper;

    /**
     * StoreActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Data $cameraHouseHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Data $cameraHouseHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->cameraHouseHelper = $cameraHouseHelper;
        parent::__construct($context, $uiComponentFactory, $urlBuilder, $components, $data);
    }

    /**
     * Function aroundPrepareDataSource.
     *
     * @param \Netstarter\StoreLocator\Ui\Component\Listing\Column\StoreActions $subject
     * @param \Closure                                                          $proceed
     * @param array                                                             $dataSource
     *
     * @return array
     */
    public function aroundPrepareDataSource(
        \Netstarter\StoreLocator\Ui\Component\Listing\Column\StoreActions $subject,
        \Closure $proceed,
        array $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['store_locator_id'])) {
                    $windowUrl = $this->urlBuilder->getUrl(
                        constant(get_class($subject).'::URL_PATH_CATEGORY'),
                        [
                            'store_locator_id' => $item['store_locator_id'],
                        ]);

                    $item[$subject->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                constant(get_class($subject).'::URL_PATH_EDIT'),
                                [
                                    'store_locator_id' => $item['store_locator_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                constant(get_class($subject).'::URL_PATH_DELETE'),
                                [
                                    'store_locator_id' => $item['store_locator_id'],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.title }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?'),
                            ],
                        ],
                        'add_category' => [
                            'href' => 'javascript:CategoryNodes.openDialog(\''.$windowUrl.'\');',
                            'label' => __('Add Category'),
                        ],
                    ];

                    //Check user role and allowed user list and remove save functionality
                    $isGenericAdmin = $this->cameraHouseHelper->isGenericAdminUser();
                    $storeLocatorId = $item['store_locator_id'];
                    if ($isGenericAdmin === false) {
                        unset($item[$subject->getData('name')]['delete']);
                        $allowedStores = $this->cameraHouseHelper->getMyAssignedStores();
                        if (!empty($allowedStores) && !in_array($storeLocatorId, $allowedStores)) {
                            unset($item[$subject->getData('name')]['add_category']);
                            $item[$subject->getData('name')]['edit']['label'] = __('View');
                        }
                    }
                }
            }
        }

        return $dataSource;
    }
}
