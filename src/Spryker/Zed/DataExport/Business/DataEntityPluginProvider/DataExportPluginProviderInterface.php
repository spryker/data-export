<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataExport\Business\DataEntityPluginProvider;

use Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface;

interface DataExportPluginProviderInterface
{
    /**
     * @param string $dataEntity
     * @param string|null $pluginInterface
     *
     * @return bool
     */
    public function exists(string $dataEntity, ?string $pluginInterface = null): bool;

    /**
     * @param string $dataEntity
     *
     * @throws \Spryker\Zed\DataExport\Business\Exception\DataExporterNotFoundException
     *
     * @return void
     */
    public function requireDataEntityPlugin(string $dataEntity): void;

    /**
     * @template PluginInterface
     *
     * @param string $dataEntity
     * @param class-string<PluginInterface> $pluginInterface
     *
     * @return PluginInterface
     */
    public function get(string $dataEntity, string $pluginInterface);

    /**
     * @param string $dataEntity
     *
     * @return \Spryker\Zed\DataExportExtension\Dependency\Plugin\DataEntityPluginInterface
     */
    public function getDataEntityPlugin(string $dataEntity): DataEntityPluginInterface;
}
