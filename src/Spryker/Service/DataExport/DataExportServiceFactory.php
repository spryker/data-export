<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\DataExport;

use Spryker\Service\DataExport\Dependency\External\DataExportToCsvFormatterInterface;
use Spryker\Service\DataExport\Dependency\Service\DataExportToUtilDataReaderServiceInterface;
use Spryker\Service\DataExport\Formatter\DataExportCsvFormatter;
use Spryker\Service\DataExport\Formatter\DataExportFormatter;
use Spryker\Service\DataExport\Formatter\DataExportFormatterInterface;
use Spryker\Service\DataExport\Mapper\DataExportConfigurationMapper;
use Spryker\Service\DataExport\Mapper\DataExportConfigurationMapperInterface;
use Spryker\Service\DataExport\Merger\DataExportConfigurationMerger;
use Spryker\Service\DataExport\Merger\DataExportConfigurationMergerInterface;
use Spryker\Service\DataExport\Parser\DataExportConfigurationParserInterface;
use Spryker\Service\DataExport\Parser\DataExportConfigurationYamlParser;
use Spryker\Service\DataExport\Resolver\DataExportConfigurationResolver;
use Spryker\Service\DataExport\Resolver\DataExportConfigurationResolverInterface;
use Spryker\Service\DataExport\Resolver\DataExportPathResolver;
use Spryker\Service\DataExport\Resolver\DataExportPathResolverInterface;
use Spryker\Service\DataExport\Writer\DataExportLocalWriter;
use Spryker\Service\DataExport\Writer\DataExportWriter;
use Spryker\Service\DataExport\Writer\DataExportWriterInterface;
use Spryker\Service\DataExport\Writer\FormattedDataExportWriterInterface;
use Spryker\Service\DataExport\Writer\OutputStreamFormattedDataExportWriter;
use Spryker\Service\Kernel\AbstractServiceFactory;

/**
 * @method \Spryker\Service\DataExport\DataExportConfig getConfig()
 */
class DataExportServiceFactory extends AbstractServiceFactory
{
    public function createDataExportConfigurationYamlParser(): DataExportConfigurationParserInterface
    {
        return new DataExportConfigurationYamlParser(
            $this->getUtilDataReaderService(),
            $this->createDataExportConfigurationMapper(),
        );
    }

    public function createDataExportConfigurationMapper(): DataExportConfigurationMapperInterface
    {
        return new DataExportConfigurationMapper();
    }

    public function createDataExportConfigurationResolver(): DataExportConfigurationResolverInterface
    {
        return new DataExportConfigurationResolver($this->createDataExportConfigurationMerger());
    }

    public function createDataExportConfigurationMerger(): DataExportConfigurationMergerInterface
    {
        return new DataExportConfigurationMerger();
    }

    public function createDataExportWriter(): DataExportWriterInterface
    {
        return new DataExportWriter(
            $this->getDataExportConnectionPlugins(),
            $this->createDataExportFormatter(),
            $this->createDataExportLocalWriter(),
        );
    }

    public function createDataExportFormatter(): DataExportFormatterInterface
    {
        return new DataExportFormatter(
            $this->getDataExportFormatterPlugins(),
            $this->createDataExportCsvFormatter(),
        );
    }

    public function createDataExportCsvFormatter(): DataExportFormatterInterface
    {
        return new DataExportCsvFormatter($this->getCsvFormatter());
    }

    public function createDataExportPathResolver(): DataExportPathResolverInterface
    {
        return new DataExportPathResolver();
    }

    public function createDataExportLocalWriter(): DataExportWriterInterface
    {
        return new DataExportLocalWriter(
            $this->createDataExportFormatter(),
            $this->createDataExportPathResolver(),
            $this->getConfig(),
        );
    }

    public function createOutputStreamFormattedDataExportWriter(): FormattedDataExportWriterInterface
    {
        return new OutputStreamFormattedDataExportWriter();
    }

    public function getUtilDataReaderService(): DataExportToUtilDataReaderServiceInterface
    {
        return $this->getProvidedDependency(DataExportDependencyProvider::SERVICE_UTIL_DATA_READER);
    }

    /**
     * @return array<\Spryker\Service\DataExportExtension\Dependency\Plugin\DataExportFormatterPluginInterface>
     */
    public function getDataExportFormatterPlugins(): array
    {
        return $this->getProvidedDependency(DataExportDependencyProvider::DATA_EXPORT_FORMATTER_PLUGINS);
    }

    /**
     * @return array<\Spryker\Service\DataExportExtension\Dependency\Plugin\DataExportConnectionPluginInterface>
     */
    public function getDataExportConnectionPlugins(): array
    {
        return $this->getProvidedDependency(DataExportDependencyProvider::DATA_EXPORT_CONNECTION_PLUGINS);
    }

    public function getCsvFormatter(): DataExportToCsvFormatterInterface
    {
        return $this->getProvidedDependency(DataExportDependencyProvider::CSV_FORMATTER);
    }
}
