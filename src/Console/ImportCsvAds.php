<?php

namespace App\Console;

use App\Models\Ads;
use App\Models\AdsAdset;
use App\Models\AdsCampaign;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCsvAds extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('import-csv-ads')
            ->setDescription('Import ads data from ads.xlsx');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new Logger('import_ads');
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/import_ads.log', Logger::DEBUG));

        $filePath = __DIR__ . '/../../var/excel/ads.xlsx';

        if (!file_exists($filePath)) {
            $logger->error('File not found: ' . $filePath);
            $output->writeln('<error>File not found</error>');
            return Command::FAILURE;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $data = $spreadsheet->getActiveSheet()->toArray();

            
            $adsModel = new Ads();
            $campaignModel = new AdsCampaign();
            $groupModel = new AdsAdset();

            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Пропускаем заголовок

                $date = $row[0];
                $amount = $row[1];
                $adName = $row[3];
                $campaignName = $row[5];
                $groupName = $row[7];
                $impressions = $row[8];
                $clicks = $row[9];

                // Преобразование формата даты
                $formattedDate = \DateTime::createFromFormat('d.m.Y', $date);
                if (!$formattedDate) {
                    $logger->error("Invalid date format: {$date}");
                    $output->writeln("<error>Invalid date format: {$date}</error>");
                    continue;
                }

                $formattedDate = $formattedDate->format('Y-m-d');

                // Добавляем или обновляем данные в БД
                $campaign = $campaignModel->addSingle(['campaign_name' => $campaignName]);
                $group = $groupModel->addSingle(['group_name' => $groupName]);

                $adsModel->addSingle([
                    'date' => $formattedDate,
                    'amount' => $amount,
                    'ad_name' => $adName,
                    'campaign_id' => $campaign->id,
                    'group_id' => $group->id,
                    'impressions' => $impressions,
                    'clicks' => $clicks,
                ]);

                $logger->info("Processed ad: {$adName}");
                $output->writeln("Processed ad: {$adName}");
            }
        } catch (\Exception $e) {
            $logger->error('Error processing file: ' . $e->getMessage());
            $output->writeln('<error>Error processing file: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $logger->info('File processing completed successfully.');
        return Command::SUCCESS;
    }

    
}
