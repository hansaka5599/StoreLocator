<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Block\Stores;

class View extends \Netstarter\StoreLocator\Block\Stores\View
{
    /**
     * get seo text.
     *
     * @return array
     */
    public function aroundGetSEOText()
    {
        if ($this->inCategory) {
            $text = ''; //Return blank if it is a category
        } else {
            $text = $this->helper->getConfigValue('map/seo_text');
        }

        return $text;
    }
}
