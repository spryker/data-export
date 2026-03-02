<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\DataExport\Parser;

use Generated\Shared\Transfer\DataExportConfigurationsTransfer;

interface DataExportConfigurationParserInterface
{
    public function parseConfigurationFile(string $fileName): DataExportConfigurationsTransfer;
}
