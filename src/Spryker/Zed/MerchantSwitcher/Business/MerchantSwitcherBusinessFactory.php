<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSwitcher\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\MerchantSwitcher\Business\MerchantInQuoteValidator\MerchantInQuoteValidator;
use Spryker\Zed\MerchantSwitcher\Business\MerchantInQuoteValidator\MerchantInQuoteValidatorInterface;
use Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcher\MerchantSwitcher;
use Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcher\MerchantSwitcherInterface;
use Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcher\WishlistMerchantSwitcher;
use Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcher\WishlistMerchantSwitcherInterface;
use Spryker\Zed\MerchantSwitcher\Business\WishlistValidator\MerchantWishlistValidator;
use Spryker\Zed\MerchantSwitcher\Business\WishlistValidator\MerchantWishlistValidatorInterface;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToCartFacadeInterface;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToMerchantProductOfferFacadeInterface;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToQuoteFacadeInterface;
use Spryker\Zed\MerchantSwitcher\MerchantSwitcherDependencyProvider;

/**
 * @method \Spryker\Zed\MerchantSwitcher\MerchantSwitcherConfig getConfig()
 */
class MerchantSwitcherBusinessFactory extends AbstractBusinessFactory
{
    public function createMerchantSwitcher(): MerchantSwitcherInterface
    {
        return new MerchantSwitcher(
            $this->getMerchantProductOfferFacade(),
            $this->getQuoteFacade(),
            $this->getCartFacade(),
        );
    }

    public function createWishlistMerchantSwitcher(): WishlistMerchantSwitcherInterface
    {
        return new WishlistMerchantSwitcher(
            $this->getMerchantProductOfferFacade(),
        );
    }

    public function createMerchantWishlistValidator(): MerchantWishlistValidatorInterface
    {
        return new MerchantWishlistValidator();
    }

    public function getQuoteFacade(): MerchantSwitcherToQuoteFacadeInterface
    {
        return $this->getProvidedDependency(MerchantSwitcherDependencyProvider::FACADE_QUOTE);
    }

    public function getCartFacade(): MerchantSwitcherToCartFacadeInterface
    {
        return $this->getProvidedDependency(MerchantSwitcherDependencyProvider::FACADE_CART);
    }

    public function getMerchantProductOfferFacade(): MerchantSwitcherToMerchantProductOfferFacadeInterface
    {
        return $this->getProvidedDependency(MerchantSwitcherDependencyProvider::FACADE_MERCHANT_PRODUCT_OFFER);
    }

    public function createMerchantInQuoteValidator(): MerchantInQuoteValidatorInterface
    {
        return new MerchantInQuoteValidator();
    }
}
