<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderRepository;
use Magento\Setup\Exception;
use Magento\Store\Model\StoreManagerInterface;

class StoreConfigResolver
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var SessionQuote
     */
    protected $sessionQuote;

    /**
     * @param OrderRepository $orderRepository
     * @param RequestHttp $request
     * @param SessionQuote $sessionQuote
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        OrderRepository $orderRepository,
        RequestHttp $request,
        SessionQuote $sessionQuote,
        StoreManagerInterface $storeManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->sessionQuote = $sessionQuote;
        $this->storeManager = $storeManager;
    }

    /**
     * Get store id for config values
     *
     * @return int|null
     *
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        $currentStoreId = null;

        $orderId = $this->request->getParam('order_id');

        if ($orderId) {
            try {
                return $this->orderRepository->get($orderId)->getStoreId();
            } catch (NoSuchEntityException $exception) {
                throw new NoSuchEntityException();
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        $currentStoreIdInAdmin = $this->sessionQuote->getStoreId();

        if (!$currentStoreIdInAdmin) {
            $currentStoreId = $this->storeManager->getStore()->getId();
        }

        return $currentStoreId ?: $currentStoreIdInAdmin;
    }
}
