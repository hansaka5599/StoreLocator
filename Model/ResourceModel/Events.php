<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    CameraHouse
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace CameraHouse\StoreLocator\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;

/**
 * Class Events.
 */
class Events extends AbstractDb
{
    /**
     * Events constructor.
     *
     * @param DbContext $context
     * @param null      $connectionName
     */
    public function __construct(DbContext $context, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('ns_ch_store_locator_events', 'event_id');
    }

    /**
     * Function _beforeSave.
     *
     * @param AbstractModel $object
     *
     * @return $this
     *
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->isValidPostUrlKey($object)) {
            throw new LocalizedException(
                __('The URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericPostUrlKey($object)) {
            throw new LocalizedException(
                __('The URL key cannot be made of only numbers.')
            );
        }

        if ($this->checkUrlKey($object->getIdentifier(), $object->getEventId())) {
            throw new LocalizedException(
                __('The URL key already exists.')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     *  Check whether post url key is numeric.
     *
     * @param AbstractModel $object
     *
     * @return bool
     */
    protected function isNumericPostUrlKey(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether post url key is valid.
     *
     * @param AbstractModel $object
     *
     * @return bool
     */
    protected function isValidPostUrlKey(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Function getLoadByUrlKeySelect.
     *
     * @param $url_key
     * @param null $event_id
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getLoadByUrlKeySelect($url_key, $event_id = null)
    {
        $select = $this->getConnection()->select()->from(
            ['event' => $this->getMainTable()]
        )
            ->where(
                'event.identifier = ?',
                $url_key
            );

        if ($event_id) {
            $select->where(
                'event.event_id <> ?',
                $event_id
            );
        }

        return $select;
    }

    /**
     * Function checkUrlKey
     * check duplicate url key entered.
     *
     * @param $url_key
     * @param null $event_id
     *
     * @return string
     */
    public function checkUrlKey($url_key, $event_id = null)
    {
        $select = $this->getLoadByUrlKeySelect($url_key, $event_id);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('event.event_id')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}
