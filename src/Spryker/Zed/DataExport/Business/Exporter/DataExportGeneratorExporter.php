<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\Exporter;

use Generated\Shared\Transfer\DataExportBatchTransfer;
use Generated\Shared\Transfer\DataExportConfigurationTransfer;
use Generated\Shared\Transfer\DataExportReportTransfer;
use Generated\Shared\Transfer\DataExportResultTransfer;
use Generator;
use Spryker\Service\DataExport\DataExportServiceInterface;
use Spryker\Zed\DataExport\Business\Mapper\DataExportMapperInterface;

class DataExportGeneratorExporter implements DataExportGeneratorExporterInterface
{
    /**
     * @var \Spryker\Service\DataExport\DataExportServiceInterface
     */
    protected $dataExportService;

    /**
     * @var \Spryker\Zed\DataExport\Business\Mapper\DataExportMapperInterface
     */
    protected $dataExportMapper;

    /**
     * @param \Spryker\Service\DataExport\DataExportServiceInterface $dataExportService
     * @param \Spryker\Zed\DataExport\Business\Mapper\DataExportMapperInterface $dataExportMapper
     */
    public function __construct(
        DataExportServiceInterface $dataExportService,
        DataExportMapperInterface $dataExportMapper
    ) {
        $this->dataExportService = $dataExportService;
        $this->dataExportMapper = $dataExportMapper;
    }

    /**
     * @param \Generator<\Generated\Shared\Transfer\DataExportBatchTransfer> $dataExportGenerator
     * @param \Generated\Shared\Transfer\DataExportConfigurationTransfer $dataExportConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataExportReportTransfer
     */
    public function exportFromGenerator(
        Generator $dataExportGenerator,
        DataExportConfigurationTransfer $dataExportConfigurationTransfer
    ): DataExportReportTransfer {
        $dataExportConfigurationTransfer->requireFields();
        $dataExportResultTransfer = (new DataExportResultTransfer())
            ->setDataEntity($dataExportConfigurationTransfer->getDataEntity())
            ->setIsSuccessful(false);

        foreach ($dataExportGenerator as $dataExportBatchTransfer) {
            assert($dataExportBatchTransfer instanceof DataExportBatchTransfer);
            $dataExportBatchTransfer->requireOffset()->requireLimit();
            $dataExportBatchTransfer->setData(
                $this->dataExportMapper
                    ->map($dataExportBatchTransfer->getData(), $dataExportConfigurationTransfer->getFields()),
            );

            $dataExportWriteResponseTransfer = $this->dataExportService->write($dataExportBatchTransfer, $dataExportConfigurationTransfer);
            $dataExportResultTransfer->setExportCount($dataExportBatchTransfer->getOffset());

            if (!$dataExportWriteResponseTransfer->getIsSuccessful()) {
                $dataExportResultTransfer->fromArray($dataExportWriteResponseTransfer->toArray(), true);

                return $this->buildDataExportReportTransfer(false, $dataExportResultTransfer);
            }

            $dataExportResultTransfer->setFileName($dataExportWriteResponseTransfer->getFileName());
        }

        return $this->buildDataExportReportTransfer(true, $dataExportResultTransfer);
    }

    /**
     * @param bool $isSuccessful
     * @param \Generated\Shared\Transfer\DataExportResultTransfer $dataExportResultTransfer
     *
     * @return \Generated\Shared\Transfer\DataExportReportTransfer
     */
    protected function buildDataExportReportTransfer(bool $isSuccessful, DataExportResultTransfer $dataExportResultTransfer): DataExportReportTransfer
    {
        return (new DataExportReportTransfer())
            ->setIsSuccessful($isSuccessful)
            ->addDataExportResult($dataExportResultTransfer);
    }
}
