<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSwitcher\Business;

use Generated\Shared\Transfer\MerchantSwitchRequestTransfer;
use Generated\Shared\Transfer\MerchantSwitchResponseTransfer;

interface MerchantSwitcherFacadeInterface
{
    /**
     * Specification:
     * - Switches ItemTransfer.productOfferReference property according to selected merchant reference.
     * - Switches ItemTransfer.merchantReference property according to selected merchant reference.
     * - Updates a quote in the database if a storage strategy is `database`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantSwitchResponseTransfer
     */
    public function switchMerchant(MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer): MerchantSwitchResponseTransfer;
}
