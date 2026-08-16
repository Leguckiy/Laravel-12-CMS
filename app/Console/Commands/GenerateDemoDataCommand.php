<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Console\Command;

class GenerateDemoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:generate {--customers=20 : Number of customers to create} {--orders=50 : Number of orders to create}';

    /**
     * The console command description.
     */
    protected $description = 'Generate demo data for development and testing';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $customersCount = (int) $this->option('customers');
        $ordersCount = (int) $this->option('orders');

        $this->info('Generating demo data...');

        if ($customersCount > 0) {
            $this->generateCustomers($customersCount);
        }

        if ($ordersCount > 0) {
            if (! Customer::query()->exists()) {
                $this->error('Cannot generate orders: no customers found.');

                return self::FAILURE;
            }

            $this->generateOrders($ordersCount);
        }

        $this->newLine();
        $this->info('Demo data generation completed.');

        return self::SUCCESS;
    }

    /**
     * Generate customers.
     */
    private function generateCustomers(int $count): void
    {
        $this->info("Creating {$count} customer(s)...");

        Customer::factory()
            ->count($count)
            ->withAddress()
            ->create();

        $this->info("Created {$count} customer(s).");
    }

    /**
     * Generate orders.
     */
    private function generateOrders(int $count): void
    {
        $this->info("Creating {$count} order(s)...");

        Order::factory()
            ->count($count)
            ->create();

        $this->info("Created {$count} order(s).");
    }
}
