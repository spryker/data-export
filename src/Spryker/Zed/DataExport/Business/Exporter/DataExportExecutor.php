<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\Exporter;

use Generated\Shared\Transfer\DataExportConfigurationsTransfer;
use Generated\Shared\Transfer\DataExportConfigurationTransfer;
use Generated\Shared\Transfer\DataExportReportTransfer;
use Spryker\Service\DataExport\DataExportServiceInterface;
use Spryker\Zed\DataExport\Business\Exception\DataExporterNotFoundException;
use Spryker\Zed\DataExport\DataExportConfig;

class DataExportExecutor
{
    protected const HOOK_KEY_EXTENSION = 'extension';
    protected const HOOK_KEY_DATA_ENTITY = 'data_entity';

    /**
     * @var \Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityExporterPluginInterface[]
     */
    protected $dataEntityExporterPlugins;

    /**
     * @var \Spryker\Service\DataExport\DataExportServiceInterface
     */
    protected $dataExportService;

    /**
     * @var \Spryker\Zed\DataExport\DataExportConfig
     */
    protected $dataExportConfig;

    /**
     * @param array $dataEntityExporterPlugins
     * @param \Spryker\Service\DataExport\DataExportServiceInterface $dataExportService
     * @param \Spryker\Zed\DataExport\DataExportConfig $dataExportConfig
     */
    public function __construct(
        array $dataEntityExporterPlugins,
        DataExportServiceInterface $dataExportService,
        DataExportConfig $dataExportConfig
    ) {
        $this->dataEntityExporterPlugins = $dataEntityExporterPlugins;
        $this->dataExportService = $dataExportService;
        $this->dataExportConfig = $dataExportConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\DataExportConfigurationsTransfer $dataExportConfigurationsTransfer
     *
     * @return \Generated\Shared\Transfer\DataExportReportTransfer[]
     */
    public function exportDataEntities(DataExportConfigurationsTransfer $dataExportConfigurationsTransfer): array
    {
        $dataExportResultTransfers = [];

        $dataExportDefaultsConfigurationsTransfer = $this->getDataExportDefaultsConfiguration();
        $dataExportDefaultsConfigurationTransfer = $this->dataExportService->mergeDataExportConfigurationTransfers(
            $dataExportConfigurationsTransfer->getDefaults() ?? new DataExportConfigurationTransfer(),
            $dataExportDefaultsConfigurationsTransfer->getDefaults()
        );

        foreach ($dataExportConfigurationsTransfer->getActions() as $dataExportConfigurationTransfer) {
            $dataExportConfigurationTransfer = $this->dataExportService->mergeDataExportConfigurationTransfers(
                $dataExportDefaultsConfigurationTransfer,
                $dataExportConfigurationTransfer
            );
            $dataExportConfigurationTransfer = $this->addDataExportConfigurationActionHooks($dataExportConfigurationTransfer);

            $dataExportResultTransfers[] = $this->runExport($dataExportConfigurationTransfer);
        }

        return $dataExportResultTransfers;
    }

    /**
     * @return \Generated\Shared\Transfer\DataExportConfigurationsTransfer
     */
    protected function getDataExportDefaultsConfiguration(): DataExportConfigurationsTransfer
    {
        return $this->dataExportService->parseConfiguration(
            $this->dataExportConfig->getExportConfigurationDefaultsPath()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\DataExportConfigurationTransfer $dataExportConfigurationTransfer
     *
     * @throws \Spryker\Zed\DataExport\Business\Exception\DataExporterNotFoundException
     *
     * @return \Generated\Shared\Transfer\DataExportReportTransfer
     */
    protected function runExport(DataExportConfigurationTransfer $dataExportConfigurationTransfer): DataExportReportTransfer
    {
        foreach ($this->dataEntityExporterPlugins as $dataEntityExporterPlugin) {
            if (!$dataEntityExporterPlugin->isApplicable($dataExportConfigurationTransfer)) {
                continue;
            }

            return $dataEntityExporterPlugin->export($dataExportConfigurationTransfer);
        }

        throw new DataExporterNotFoundException(sprintf(
            'Data exporter not found for %s data entity',
            $dataExportConfigurationTransfer->getDataEntity()
        ));
    }

    /**
     * @param \Generated\Shared\Transfer\DataExportConfigurationTransfer $dataExportConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataExportConfigurationTransfer
     */
    protected function addDataExportConfigurationActionHooks(DataExportConfigurationTransfer $dataExportConfigurationTransfer): DataExportConfigurationTransfer
    {
        $dataExportConfigurationTransfer->addHook(static::HOOK_KEY_DATA_ENTITY, $dataExportConfigurationTransfer->getDataEntity());
        $dataExportConfigurationTransfer->addHook(
            static::HOOK_KEY_EXTENSION,
            $this->dataExportService->getFormatExtension($dataExportConfigurationTransfer)
        );

        return $dataExportConfigurationTransfer;
    }
}