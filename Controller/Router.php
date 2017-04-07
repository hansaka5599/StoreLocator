<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Controller;

use CameraHouse\StoreLocator\Model\Events as CameraHouseEvents;
use CameraHouse\StoreLocator\Model\ResourceModel\Category\Node;
use CameraHouse\StoreLocator\Model\ResourceModel\Events;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Netstarter\StoreLocator\Model\Category;
use Netstarter\StoreLocator\Model\ResourceModel\Store;
use Netstarter\StoreLocator\Model\Store as NetstarterStore;

/**
 * Class Router.
 */
class Router extends \Netstarter\StoreLocator\Controller\Router
{
    /**
     * Variable eventsResource.
     *
     * @var Events
     */
    protected $eventsResource;

    /**
     * Variable storeResource.
     *
     * @var $storeResource
     */
    protected $storeResource;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     * @param Store $storeResource
     * @param Node $nodeResource
     * @param Events $eventsResource
     */
    public function __construct(
        ActionFactory $actionFactory,
        UrlInterface $url,
        StoreManagerInterface $storeManager,
        Store $storeResource,
        Node $nodeResource,
        Events $eventsResource
    ) {
        $this->eventsResource = $eventsResource;
        parent::__construct($actionFactory, $url, $storeManager, $storeResource, $nodeResource);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * Function match.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if (preg_match('/^'. NetstarterStore::URL_PREFIX.'\/(.*)/', $identifier, $matches)) {
            $urlKey = $matches[1];

            /* store events routing */
            if (strpos($urlKey, CameraHouseEvents::URL_PREFIX, 0) === 0) {
                $eventUrlKey = str_replace(CameraHouseEvents::URL_PREFIX.'/', '', $urlKey);

                $eventId = $this->eventsResource->checkUrlKey($eventUrlKey);
                if (!$eventId) {
                    return null;
                }

                $request->setModuleName('chstores')->setControllerName('event')->setActionName('view')
                    ->setParam('event_id', $eventId);
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $eventUrlKey);

                return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
            }

            /*services routing*/
            $urlKeys = explode('/', $urlKey);

            if (sizeof($urlKeys) == 2) {
                $storeLocatorId = $this->storeResource->checkUrlKey(
                    $urlKeys[0], $this->_storeManager->getStore()->getId()
                );

                /*services landing page*/
                if ($storeLocatorId && $urlKeys[1] == 'services') {
                    $request->setModuleName('chstores')->setControllerName('services')->setActionName('index')
                        ->setParam('store_locator_id', $storeLocatorId);

                    $request->setAlias(
                        Url::REWRITE_REQUEST_PATH_ALIAS,
                        NetstarterStore::URL_PREFIX.'/'.$urlKeys[0].'/'.$urlKeys[1]
                    );

                    return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
                }

                $nodeId = $this->nodeResource->getLoadByUrlKeySelect($urlKeys[1]);

                if ($storeLocatorId && $nodeId) {
                    $request->setModuleName('chstores')->setControllerName('services')->setActionName('view')
                        ->setParam('store_locator_id', $storeLocatorId)
                        ->setParam('node_id', $nodeId);
                    $request->setAlias(
                        Url::REWRITE_REQUEST_PATH_ALIAS,
                        NetstarterStore::URL_PREFIX.'/'.$urlKeys[0].'/'.$urlKeys[1]
                    );

                    return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
                }
            }

            $storeLocatorId = $this->storeResource->checkUrlKey($urlKey, $this->_storeManager->getStore()->getId());
            if (!$storeLocatorId) {
                return null;
            }
            $request->setModuleName('stores')->setControllerName('index')->setActionName('store')
                ->setParam('store_locator_id', $storeLocatorId);
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);

            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        } elseif (preg_match('/^'. Category::URL_PREFIX.'\/(.*)/', $identifier,
            $matches)) {
            $urlKey = $matches[1];
            $nodeId = $this->nodeResource->checkUrlKey($urlKey, $this->_storeManager->getStore()->getId());
            if (!$nodeId) {
                return null;
            }
            $request->setModuleName('stores')->setControllerName('index')->setActionName('category')
                ->setParam('category_node_id', $nodeId);
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);

            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }

        return null;
    }
}
