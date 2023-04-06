<?php

declare(strict_types=1);
namespace FetchApiData;

/**
 * Manange cache data.
 **/
class TransientCache
{

    /**
     * Transient key
     *
     * @var string
     */
    private $transientKey;

    /**
     * TransientCache constructor
     *
     * @param string $transientKey
     */
    public function __construct(string $transientKey)
    {
        $this->transientKey = $transientKey;
    }


    /**
     * Set data in transient cache for the 1 hours.
     *
     * @param  string  $transientData  Data to be stored in cache
     */
    public function addTransientData(string $transientData)
    {
        set_transient($this->transientKey, $transientData, HOUR_IN_SECONDS);
    }

    /**
     * Get transient cache data.
     *
     * @return  string  $transientData  Store cache data
     */
    public function receiveTransientData()
    {
        $transientData = get_transient($this->transientKey);
        return $transientData;
    }

    /**
     * Clear transient cache data.
     * 
     * @return boolean $deleted true if deleted otherwise false.
     */
    public function removeTransientData()
    {
        $deleted = delete_transient($this->transientKey);
        return $deleted;
    }
}
