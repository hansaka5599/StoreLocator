<?php
/**
 * Copyright © 2017 Netstarter. All rights reserved.
 *
 * @category   CameraHouse
 * @package    CameraHouse_StoreLocator
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 *
 * @link        http://netstarter.com.au
 */

namespace CameraHouse\StoreLocator\Block\Head\Additional;

use Magento\Framework\View\Element\Template;

class Canonical extends Template
{

    protected $request;
    protected $urlInterface;

    public function __construct(Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        array $data)
    {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->urlInterface = $context->getUrlBuilder();
    }

    public function getCanonicalUrl() {

        $moduleName = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action     = $this->getRequest()->getActionName();
        $route      = $this->getRequest()->getRouteName();

        if ($moduleName == 'stores' && $controller == 'index' && $action == 'store' && $route == 'stores') {
            return "<link  rel='canonical' href='".$this->urlInterface->getCurrentUrl()."' />";
        } else {
            return 'FALSE';
        }

    }

}