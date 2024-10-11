<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\Mapper;

interface DataExportMapperInterface
{
    /**
     * @param array $data
     * @param array $fieldMapping
     *
     * @return array
     */
    public function map(array $data, array $fieldMapping): array;
}
