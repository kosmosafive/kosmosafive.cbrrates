<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Cli\Command;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\ObjectException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\Type;
use Psr\Container\NotFoundExceptionInterface;
use Kosmosafive\CBRRates\Service\ApiServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateDailyRates extends Command
{
    protected const string DATE_FORMAT = 'Y-m-d';

    protected function configure(): void
    {
        $this
            ->setName('kosmosafive.cbrrates:update-daily-rates')
            ->setDescription('Update daily rates')
            ->addArgument('from', InputArgument::REQUIRED, 'Date \ Date from (YYYY-MM-DD)')
            ->addArgument('to', InputArgument::OPTIONAL, 'Date to (YYYY-MM-DD)')
        ;
    }

    /**
     * @throws ObjectException
     * @throws ObjectNotFoundException
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $dateFrom = $this->parseDate($from);
        $dateTo = ($to) ? $this->parseDate($to) : clone $dateFrom;

        $tomorrow = (new Type\Date())->add('1 day');
        if ($dateTo->getTimestamp() > $tomorrow->getTimestamp()) {
            $dateTo = clone $tomorrow;
        }

        if ($dateTo->getTimestamp() < $dateFrom->getTimestamp()) {
            throw new InvalidArgumentException('Wrong date range: ' . $from . ' - ' . $to);
        }

        $apiService = ServiceLocator::getInstance()->get(ApiServiceInterface::class);

        $date = clone $dateFrom;
        while ($date->getTimestamp() <= $dateTo->getTimestamp()) {
            $updateResult = $apiService->updateDailyRates($date);
            if (!$updateResult->isSuccess()) {
                throw new RuntimeException(
                    'Failed to update daily rates: '
                    . implode('; ', $updateResult->getErrorMessages())
                );
            }

            $output->writeln('Daily rates updated: <info>'.$date->format(self::DATE_FORMAT).'</info>');

            $date->add('1 day');
        }

        return Command::SUCCESS;
    }

    /**
     * @throws ObjectException
     */
    protected function parseDate(string $dateString): Type\Date
    {
        $date = new Type\Date($dateString, self::DATE_FORMAT);

        if ($date->format(self::DATE_FORMAT) !== $dateString) {
            throw new InvalidArgumentException('Wrong date format: ' . $dateString);
        }

        return $date;
    }
}
