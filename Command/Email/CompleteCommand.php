<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Command\Email;

use Line\VerifiedPurchase\Api\EmailNotificationInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to send Email notification to the buyer
 *
 * Example for Order with id 1905:
 *  ```
 *  bin/magento line:verified-purchase:notify-complete 1905
 *  ```
 */
class CompleteCommand extends Command
{
    protected const COMMAND_NAME = 'line:verified-purchase:notify-complete';
    protected const ARG_ORDER_ID = 'id';

    /**
     * @var VerifyPurchaseRepositoryInterface
     */
    private VerifyPurchaseRepositoryInterface $repository;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var EmailNotificationInterface
     */
    private EmailNotificationInterface $notification;

    /**
     * @var Emulation
     */
    private Emulation $emulation;

    /**
     * @var State
     */
    private State $state;

    /**
     * Class constructor
     *
     * @param VerifyPurchaseRepositoryInterface $repository
     * @param OrderRepositoryInterface $orderRepository
     * @param EmailNotificationInterface $notification
     * @param State $state
     * @param Emulation $emulation
     */
    public function __construct(
        VerifyPurchaseRepositoryInterface $repository,
        OrderRepositoryInterface $orderRepository,
        EmailNotificationInterface $notification,
        State $state,
        Emulation $emulation
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->notification = $notification;
        $this->emulation = $emulation;
        $this->state = $state;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $description = 'Sends a "Complete" notification email to the given Order Id';

        $this->setName(self::COMMAND_NAME)
            ->setDescription($description)
            ->setDefinition([
                new InputArgument(
                    self::ARG_ORDER_ID,
                    InputArgument::REQUIRED,
                    'Order Id'
                )
            ]);

        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $id = (int) $input->getArgument(self::ARG_ORDER_ID);

            $order = $this->orderRepository->get($id);
            $sid = $order->getStoreId();

            $verification = $this->repository->getByIncrementId(
                $order->getIncrementId()
            );

            // Simulate environment for email sending props
            $this->state->setAreaCode(Area::AREA_FRONTEND);
            $this->emulation->startEnvironmentEmulation($sid);

            $result = $this->notification->notifyComplete($verification);

            if (!$result) {
                $output->writeln(
                    'Email could not be sent for Order ' . $id
                    . ' to ' . $verification->getBuyerEmail()
                );
                $output->writeln('Check the module log files');
            } else {
                $output->writeln(
                    'Email successfully sent for Order ' . $id
                    . ' to ' . $verification->getBuyerEmail()
                );
            }

            return Cli::RETURN_SUCCESS;

        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($exception->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
