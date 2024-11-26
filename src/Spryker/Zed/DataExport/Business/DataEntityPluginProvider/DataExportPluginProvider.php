<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\DataEntityPluginProvider;

use Spryker\Zed\DataExport\Business\Exception\DataExporterNotFoundException;
use Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityExporterPluginInterface;
use Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityGeneratorPluginInterface;
use Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface;
use Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityReaderPluginInterface;

class DataExportPluginProvider implements DataExportPluginProviderInterface
{
    /**
     * @var array<string, array<string, \Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface>>
     */
    protected array $plugins = [];

    /**
     * @param array<\Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityExporterPluginInterface> $dataEntityExporterPlugins
     * @param array<\Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityGeneratorPluginInterface> $dataExportDataEntityGeneratorPlugins
     * @param array<\Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityReaderPluginInterface> $dataExportDataEntityReaderPlugins
     */
    public function __construct(
        array $dataEntityExporterPlugins,
        array $dataExportDataEntityGeneratorPlugins,
        array $dataExportDataEntityReaderPlugins
    ) {
        $this->extendPluginsWithDataEntityPlugin($dataEntityExporterPlugins, DataEntityExporterPluginInterface::class);
        $this->extendPluginsWithDataEntityPlugin($dataExportDataEntityGeneratorPlugins, DataEntityGeneratorPluginInterface::class);
        $this->extendPluginsWithDataEntityPlugin($dataExportDataEntityReaderPlugins, DataEntityReaderPluginInterface::class);
    }

    /**
     * @param string $dataEntity
     * @param string|null $pluginInterface
     *
     * @return bool
     */
    public function exists(string $dataEntity, ?string $pluginInterface = null): bool
    {
        return $pluginInterface !== null
            ? isset($this->plugins[$dataEntity][$pluginInterface])
            : isset($this->plugins[$dataEntity]);
    }

    /**
     * @param string $dataEntity
     *
     * @throws \Spryker\Zed\DataExport\Business\Exception\DataExporterNotFoundException
     *
     * @return void
     */
    public function requireDataEntityPlugin(string $dataEntity): void
    {
        if (!$this->exists($dataEntity)) {
            throw new DataExporterNotFoundException(sprintf('Data export plugin not found for %s data entity', $dataEntity));
        }
    }

    /**
     * @template PluginInterface
     *
     * @param string $dataEntity
     * @param class-string<PluginInterface> $pluginInterface
     *
     * @return PluginInterface
     */
    public function get(string $dataEntity, string $pluginInterface)
    {
        return $this->plugins[$dataEntity][$pluginInterface];
    }

    /**
     * @param string $dataEntity
     *
     * @return \Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface
     */
    public function getDataEntityPlugin(string $dataEntity): DataEntityPluginInterface
    {
        return reset($this->plugins[$dataEntity]);
    }

    /**
     * @param array<\Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface> $plugins
     * @param string $pluginInterface
     *
     * @return void
     */
    protected function extendPluginsWithDataEntityPlugin(array $plugins, string $pluginInterface): void
    {
        foreach ($plugins as $dataEntityPlugin) {
            $this->plugins[$dataEntityPlugin->getDataEntity()] = [$pluginInterface => $dataEntityPlugin];
        }
    }
}
