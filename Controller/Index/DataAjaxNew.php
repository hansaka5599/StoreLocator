<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 * @package     CameraHouse_BrandPromo
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller\Index;


class DataAjaxNew  extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Netstarter\StoreLocator\Block\Stores\DataAjax
     */
    protected $dataAjax;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Netstarter\StoreLocator\Block\Stores\DataAjax $dataAjax
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \CameraHouse\StoreLocator\Block\Stores\DataAjax $dataAjax
    ) {
        $this->dataAjax = $dataAjax;
        parent::__construct($context);
    }

    /**
     * Send Store Location details as a json string
     *
     * @return string
     */
    public function execute()
    {
        $result = $this->dataAjax->getStoreLocations(true);

        return $this->getResponse()->representJson($result);
    }
}