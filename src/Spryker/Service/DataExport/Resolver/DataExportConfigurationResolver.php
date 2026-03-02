<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\DataExport\Resolver;

use Generated\Shared\Transfer\DataExportConfigurationsTransfer;
use Generated\Shared\Transfer\DataExportConfigurationTransfer;
use Spryker\Service\DataExport\Merger\DataExportConfigurationMergerInterface;

class DataExportConfigurationResolver implements DataExportConfigurationResolverInterface
{
    /**
     * @var \Spryker\Service\DataExport\Merger\DataExportConfigurationMergerInterface
     */
    protected $dataExportConfigurationMerger;

    public function __construct(DataExportConfigurationMergerInterface $dataExportConfigurationMerger)
    {
        $this->dataExportConfigurationMerger = $dataExportConfigurationMerger;
    }

    public function resolveDataExportActionConfiguration(
        DataExportConfigurationTransfer $dataExportActionConfigurationTransfer,
        DataExportConfigurationsTransfer $additionalDataExportConfigurationsTransfer
    ): DataExportConfigurationTransfer {
        $dataExportActionConfigurationTransfer = $this->dataExportConfigurationMerger->mergeDataExportConfigurationTransfers(
            $dataExportActionConfigurationTransfer,
            $additionalDataExportConfigurationsTransfer->getDefaults(),
        );

        $additionalDataExportActionConfigurationTransfer = $this->findDataExportActionConfigurationByDataEntity(
            $dataExportActionConfigurationTransfer->getDataEntityOrFail(),
            $additionalDataExportConfigurationsTransfer,
        );

        if (!$additionalDataExportActionConfigurationTransfer) {
            return $dataExportActionConfigurationTransfer;
        }

        return $this->dataExportConfigurationMerger->mergeDataExportConfigurationTransfers(
            $dataExportActionConfigurationTransfer,
            $additionalDataExportActionConfigurationTransfer,
        );
    }

    protected function findDataExportActionConfigurationByDataEntity(
        string $dataEntityName,
        DataExportConfigurationsTransfer $dataExportConfigurationsTransfer
    ): ?DataExportConfigurationTransfer {
        foreach ($dataExportConfigurationsTransfer->getActions() as $dataExportConfigurationTransfer) {
            if ($dataExportConfigurationTransfer->getDataEntity() === $dataEntityName) {
                return $dataExportConfigurationTransfer;
            }
        }

        return null;
    }
}
