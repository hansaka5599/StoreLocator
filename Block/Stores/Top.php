<?php
/**
 * Copyright © 2015 Netstarter. All rights reserved.
 *
 * PHP version 5
 *
 * @category  PHP
 *
 * @author    Netstarter M2 Stack Team <contact@netstarter.com>
 * @copyright 2007-2015 NETSTARTER PTY LTD (ABN 28 110 067 96)
 * @license   licence.txt ©
 *
 * @link      http://netstarter.com.au
 */

namespace CameraHouse\StoreLocator\Block\Stores;

use Magento\Framework\View\Element\Template;
use Netstarter\Postcode\Api\PostcodeManagementInterface;
use Netstarter\StoreLocator\Helper\Data;

/**
 * StoreLocator Store View Block.
 */
class Top extends Template
{
    /**
     * Variable postcodeManage.
     *
     * @var PostcodeManagementInterface
     */
    protected $postcodeManage;

    /**
     * Variable helper.
     *
     * @var Data
     */
    protected $helper;

    /**
     * Top constructor.
     * @param Template\Context $context
     * @param PostcodeManagementInterface $postcodeManage
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PostcodeManagementInterface $postcodeManage,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->postcodeManage = $postcodeManage;
        $this->helper = $helper;
        $this->postcodeManage->setTypeCode(Data::POSTCODE_TYPE_CODE);
    }

    /**
     * Function getJsConfigs.
     *
     * @param $jsVariable
     *
     * @return mixed
     */
    public function getJsConfigs($jsVariable)
    {
        return $this->postcodeManage->getJsConfigs($jsVariable);
    }

    /**
     * Map js params.
     *
     * @return string
     */
    public function getMapParams()
    {
        $helper = $this->helper;
        $param = [
            'map' => [
                'multi_stores' => true,
                'lat' => $helper->getBaseLat(),
                'lng' => $helper->getBaseLng(),
                'zoom' => $helper->getDefaultZoom(),
                'pin_img' => $helper->getPinImg(),
                'styles' => $helper->getMapStyles(),
            ],
            'per_page' => $helper->getPerPageCount(),
            'oh' => $this->helper->getOpeningHrs(),
            'ho' => $this->helper->getHolidays(),
        ];

        return \Zend_Json::encode($param);
    }

    /**
     * Function getKey.
     *
     * @return array
     */
    public function getKey()
    {
        return $this->helper->getMapKey();
    }
}
