<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\Mapper;

use Spryker\Shared\DataExport\DataExportObject;

class DataExportMapper implements DataExportMapperInterface
{
    /**
     * @param array<array> $data
     * @param array<string> $fieldMapping
     *
     * @return array
     */
    public function map(array $data, array $fieldMapping): array
    {
        return array_map(fn ($item) => $this->mapItem($this->mapItemToDataExportObject($item), $fieldMapping), $data);
    }

    /**
     * @param array $item
     *
     * @return \Spryker\Shared\DataExport\DataExportObject
     */
    protected function mapItemToDataExportObject(array $item): DataExportObject
    {
        return new DataExportObject($item);
    }

    /**
     * @param \Spryker\Shared\DataExport\DataExportObject $dataExportObject
     * @param array $fields
     *
     * @return array
     */
    protected function mapItem(DataExportObject $dataExportObject, array $fields): array
    {
        $result = [];

        foreach ($fields as $pathKey) {
            $explodedKey = explode(':', $pathKey);
            $exportKey = $explodedKey[0];
            $pathKey = $explodedKey[1] ?? $explodedKey[0];

            if (str_contains($exportKey, '*')) {
                $result = array_merge($result, $this->mapArray($exportKey, $pathKey, $dataExportObject));

                continue;
            }

            $result[$exportKey] = $dataExportObject->get($pathKey);
        }

        return $result;
    }

    /**
     * @param string $exportKey
     * @param string $pathKey
     * @param \Spryker\Shared\DataExport\DataExportObject $data
     *
     * @return array
     */
    public function mapArray(string $exportKey, string $pathKey, DataExportObject $data): array
    {
        $result = [];
        $prefix = substr($exportKey, 0, strpos($exportKey, '*'));
        $postfix = substr($exportKey, strpos($exportKey, '*') + 1);

        $res = $data->get($pathKey);

        if (!is_array($res)) {
            $result[$prefix . '0' . $postfix] = $res;

            return $result;
        }

        foreach ($res as $key => $item) {
            $result[$prefix . $key . $postfix] = $item;
        }

        return $result;
    }
}
