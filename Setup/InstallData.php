<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class InstallData.
 */
class InstallData implements InstallDataInterface
{
    /**
     * Variable blockFactory.
     *
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * InstallData constructor.
     *
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * Function install.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $cmsBlocks = [
            [
                'title' => 'home-page-spotlight',
                'identifier' => 'home-page-spotlight',
                'content' => '<div class="home-page-spotlight"><a class="sp1" href="#">
                        <img title="sp1" src="{{media url=\'wysiwyg/sp1.jpg\'}}" /></a> 
                        <a class="sp2" href="#"><img title="sp2" src="{{media url=\'wysiwyg/sp2.jpg\'}}" /></a> 
                        <a class="sp3" href="#"><img title="sp3" src="{{media url=\'wysiwyg/sp3.jpg\'}}" /></a></div>',
                'is_active' => 1,
                'stores' => 0,
            ],
        ];

        $block = $this->blockFactory->create();
        foreach ($cmsBlocks as $data) {
            $block->setData($data)->save();
        }
    }
}
