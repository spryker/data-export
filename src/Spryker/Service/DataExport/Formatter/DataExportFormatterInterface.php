<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\DataExport\Formatter;

use Generated\Shared\Transfer\DataExportBatchTransfer;
use Generated\Shared\Transfer\DataExportConfigurationTransfer;
use Generated\Shared\Transfer\DataExportFormatResponseTransfer;

interface DataExportFormatterInterface
{
    public function formatBatch(
        DataExportBatchTransfer $dataExportBatchTransfer,
        DataExportConfigurationTransfer $dataExportConfigurationTransfer
    ): DataExportFormatResponseTransfer;

    public function getFormatExtension(DataExportConfigurationTransfer $dataExportConfigurationTransfer): ?string;
}
