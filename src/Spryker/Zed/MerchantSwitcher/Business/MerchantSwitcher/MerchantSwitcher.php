<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSwitcher\Business\MerchantSwitcher;

use Generated\Shared\Transfer\MerchantProductOfferConditionsTransfer;
use Generated\Shared\Transfer\MerchantProductOfferCriteriaTransfer;
use Generated\Shared\Transfer\MerchantSwitchRequestTransfer;
use Generated\Shared\Transfer\MerchantSwitchResponseTransfer;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToCartFacadeInterface;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToMerchantProductOfferFacadeInterface;
use Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToQuoteFacadeInterface;

class MerchantSwitcher implements MerchantSwitcherInterface
{
    /**
     * @uses \Spryker\Shared\Quote\QuoteConfig::STORAGE_STRATEGY_DATABASE
     *
     * @var string
     */
    protected const STORAGE_STRATEGY_DATABASE = 'database';

    /**
     * @var \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToMerchantProductOfferFacadeInterface
     */
    protected $merchantProductOfferFacade;

    /**
     * @var \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @var \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToCartFacadeInterface
     */
    protected $cartFacade;

    /**
     * @param \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToMerchantProductOfferFacadeInterface $merchantProductOfferFacade
     * @param \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToQuoteFacadeInterface $quoteFacade
     * @param \Spryker\Zed\MerchantSwitcher\Dependency\Facade\MerchantSwitcherToCartFacadeInterface $cartFacade
     */
    public function __construct(
        MerchantSwitcherToMerchantProductOfferFacadeInterface $merchantProductOfferFacade,
        MerchantSwitcherToQuoteFacadeInterface $quoteFacade,
        MerchantSwitcherToCartFacadeInterface $cartFacade
    ) {
        $this->merchantProductOfferFacade = $merchantProductOfferFacade;
        $this->quoteFacade = $quoteFacade;
        $this->cartFacade = $cartFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantSwitchResponseTransfer
     */
    public function switchMerchantInQuote(MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer): MerchantSwitchResponseTransfer
    {
        $merchantSwitchRequestTransfer
            ->requireQuote()
            ->requireMerchantReference();

        $quoteTransfer = $merchantSwitchRequestTransfer->getQuote();
        $quoteTransfer->setMerchantReference($merchantSwitchRequestTransfer->getMerchantReference());

        if ($this->quoteFacade->getStorageStrategy() === static::STORAGE_STRATEGY_DATABASE && $quoteTransfer->getIdQuote()) {
            $quoteTransfer = $this->quoteFacade->updateQuote($quoteTransfer)->getQuoteTransfer();
        }

        return (new MerchantSwitchResponseTransfer())->setQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantSwitchResponseTransfer
     */
    public function switchMerchantInQuoteItems(MerchantSwitchRequestTransfer $merchantSwitchRequestTransfer): MerchantSwitchResponseTransfer
    {
        $merchantSwitchRequestTransfer
            ->requireQuote()
            ->requireMerchantReference();

        $quoteTransfer = $merchantSwitchRequestTransfer->getQuote();
        $merchantReference = $merchantSwitchRequestTransfer->getMerchantReference();

        $skus = [];
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $skus[] = $itemTransfer->getSku();
        }

        if ($skus === []) {
            return (new MerchantSwitchResponseTransfer())
                ->setQuote($quoteTransfer);
        }

        $merchantProductOfferConditionsTransfer = (new MerchantProductOfferConditionsTransfer())
            ->addMerchantReference($merchantReference)
            ->setSkus($skus)
            ->setIsActive(true);

        $merchantProductOfferCriteriaTransfer = (new MerchantProductOfferCriteriaTransfer())
            ->setMerchantProductOfferConditions($merchantProductOfferConditionsTransfer);

        $merchantProductOfferCollectionTransfer = $this->merchantProductOfferFacade
            ->getProductOfferCollection($merchantProductOfferCriteriaTransfer);

        $productToMerchantProductOfferMap = [];
        foreach ($merchantProductOfferCollectionTransfer->getProductOffers() as $productOfferTransfer) {
            $productToMerchantProductOfferMap[$productOfferTransfer->getConcreteSku()] = $productOfferTransfer->getProductOfferReference();
        }

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getProductOfferReference() && isset($productToMerchantProductOfferMap[$itemTransfer->getSku()])) {
                $itemTransfer
                    ->setMerchantReference($merchantReference)
                    ->setProductOfferReference($productToMerchantProductOfferMap[$itemTransfer->getSku()]);
            }
        }

        return (new MerchantSwitchResponseTransfer())
            ->setQuote($quoteTransfer);
    }
}
