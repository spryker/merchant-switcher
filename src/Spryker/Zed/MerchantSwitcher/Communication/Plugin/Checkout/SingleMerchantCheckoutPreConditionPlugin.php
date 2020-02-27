<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSwitcher\Communication\Plugin\Checkout;

use ArrayObject;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPreConditionPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\MerchantSwitcher\MerchantSwitcherConfig getConfig()
 * @method \Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcherFacadeInterface getFacade()
 * @method \Spryker\Zed\MerchantSwitcher\Communication\MerchantSwitcherCommunicationFactory getFactory()
 */
class SingleMerchantCheckoutPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionPluginInterface
{
    /**
     * {@inheritDoc}
     * - Goes through QuoteTransfer.items and compares ItemTransfer.merchantReference with QuoteTransfer.merchantReference.
     * - If values are not equal the plugin returns a failure response with an error messages inside.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        if (!$this->getConfig()->isMerchantSwitcherEnabled()) {
            return true;
        }

        $quoteMerchantReference = $quoteTransfer->getMerchantReference();

        $checkoutErrorTransfers = [];
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if (
                $quoteMerchantReference
                && $itemTransfer->getMerchantReference()
                && $itemTransfer->getMerchantReference() !== $quoteMerchantReference
            ) {
                $checkoutErrorTransfers[] = (new CheckoutErrorTransfer())
                    ->setMessage('merchant_switcher.message.product_is_not_available')
                    ->setParameters([
                        '%product_name%' => $itemTransfer->getName(),
                        '%sku%' => $itemTransfer->getSku(),
                    ]);
            }
        }

        $checkoutResponseTransfer
            ->setIsSuccess(!$checkoutErrorTransfers)
            ->setErrors(new ArrayObject($checkoutErrorTransfers));

        return !$checkoutErrorTransfers;
    }
}
