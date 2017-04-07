<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Adminhtml\Category\Image;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class Renderer.
 */
class Renderer extends AbstractGroup
{
    const SCOPE_STORE = ScopeInterface::SCOPE_STORE;
    const MEDIA_QUERIES = 'bannerslider/general/mediaQueries';

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'image/renderer.phtml';

    /**
     * Variable storeManager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Variable scopeConfig.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Variable mediaQueries.
     *
     * @var $mediaQueries
     */
    protected $mediaQueries;

    /**
     * Renderer constructor.
     *
     * @param Context                  $context
     * @param GroupRepositoryInterface $groupRepository
     * @param Data                     $directoryHelper
     * @param Registry                 $registry
     * @param GroupManagementInterface $groupManagement
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param CurrencyInterface        $localeCurrency
     * @param HelperContext            $helperContext
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        GroupRepositoryInterface $groupRepository,
        Data $directoryHelper,
        Registry $registry,
        GroupManagementInterface $groupManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CurrencyInterface $localeCurrency,
        HelperContext $helperContext,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $groupRepository,
            $directoryHelper,
            $helperContext->getModuleManager(),
            $registry,
            $groupManagement,
            $searchCriteriaBuilder,
            $localeCurrency,
            $data
        );
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $helperContext->getScopeConfig();
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Add Image'), 'onclick' => 'return tierPriceControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * Function getMediaUrl.
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).'wysiwyg/';
    }
}
