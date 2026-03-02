<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\DataExport\Merger;

use Generated\Shared\Transfer\DataExportConfigurationTransfer;

interface DataExportConfigurationMergerInterface
{
    public function mergeDataExportConfigurationTransfers(
        ?DataExportConfigurationTransfer $primaryDataExportConfigurationTransfer,
        ?DataExportConfigurationTransfer $secondaryDataExportConfigurationTransfer
    ): DataExportConfigurationTransfer;
}
