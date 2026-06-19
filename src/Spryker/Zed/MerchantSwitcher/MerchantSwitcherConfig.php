<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSwitcher;

use Spryker\Zed\Kernel\AbstractBundleConfig;

/**
 * @method \Spryker\Shared\MerchantSwitcher\MerchantSwitcherConfig getSharedConfig()
 */
class MerchantSwitcherConfig extends AbstractBundleConfig
{
    protected const string CHECKOUT_ERROR_TYPE = 'MerchantProductUnavailable';

    /**
     * @api
     *
     * @return bool
     */
    public function isMerchantSwitcherEnabled(): bool
    {
        return $this->getSharedConfig()->isMerchantSwitcherEnabled();
    }

    /**
     * Specification:
     * - Returns the error type identifier used in CheckoutErrorTransfer for errors produced by this module.
     *
     * @api
     */
    public function getCheckoutErrorType(): string
    {
        return static::CHECKOUT_ERROR_TYPE;
    }
}
