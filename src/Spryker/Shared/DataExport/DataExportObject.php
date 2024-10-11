<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\DataExport;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class DataExportObject
{
    protected array $transfer;

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|array $transfer
     * @param string $fieldDelimiter
     * @param string $arrayDelimiter
     * @param string $keyRoot
     */
    public function __construct(
        AbstractTransfer|array $transfer,
        protected readonly string $fieldDelimiter = '.',
        protected readonly string $arrayDelimiter = '*',
        protected readonly string $keyRoot = '$'
    ) {
        $this->transfer = is_array($transfer) ? $transfer : $transfer->toArray(true, true);
    }

    /**
     * @param string $key
     * @param $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        $key = ltrim($key, $this->keyRoot . $this->fieldDelimiter);
        $keys = explode($this->fieldDelimiter, $key);
        $value = $this->transfer;

        return $this->getByKeys($keys, $value, $default);
    }

    /**
     * @param array $keys
     * @param mixed $value
     * @param $default
     *
     * @return mixed
     */
    protected function getByKeys(array $keys, mixed $value, $default = null): mixed
    {
        while (count($keys)) {
            $key = array_shift($keys);

            if (!is_array($value)) {
                return $default;
            }

            if ($key === $this->arrayDelimiter) {
                return array_map(fn ($item) => $this->getByKeys($keys, $item, $default), $value);
            }

            if ($key === $this->keyRoot) {
                return $this->getByKeys($keys, $this->transfer, $default);
            }

            if (!array_key_exists($key, $value)) {
                return $default;
            }

            $value = $value[$key];
        }

        return $value;
    }
}
