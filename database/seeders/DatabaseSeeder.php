<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Salon;
use App\Models\SalonOpeningHour;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create an administrator.
        $admin = User::factory()->admin()->create([
            'name' => 'Koja Admin',
            'phone' => '+15550000001',
        ]);

        // 2. Create customers.
        $customers = User::factory()
            ->count(12)
            ->create();

        // 3. Create a suspended customer for moderation testing.
        $suspendedCustomer = User::factory()
            ->suspended()
            ->create();

        // 4. Create salon owners.
        $owners = User::factory()
            ->owner()
            ->count(5)
            ->create();

        // 5. Create one salon per owner.
        $statuses = [
            'approved',
            'approved',
            'pending',
            'rejected',
            'suspended',
        ];

        $salons = collect();

        foreach ($owners as $index => $owner) {
            $status = $statuses[$index];

            $factory = Salon::factory()->for($owner, 'owner');

            $salon = match ($status) {
                'approved' => $factory->approved($admin)->create(),
                'rejected' => $factory->rejected($admin)->create(),
                'suspended' => $factory->suspended($admin)->create(),
                default => $factory->create(),
            };

            $salons->push($salon);

            // 6. Create seven opening-hour records.
            foreach (range(1, 7) as $day) {
                $isClosed = $day === 7;

                SalonOpeningHour::factory()->create([
                    'salon_id' => $salon->id,
                    'day_of_week' => $day,
                    'opens_at' => $isClosed ? null : '09:00:00',
                    'closes_at' => $isClosed ? null : '18:00:00',
                    'is_closed' => $isClosed,
                ]);
            }
        }

        // 7. Customers review approved salons only.
        $approvedSalons = $salons->where('status', 'approved');

        foreach ($approvedSalons as $salon) {
            foreach ($customers as $customer) {
                Review::factory()
                    ->for($customer, 'user')
                    ->for($salon, 'salon')
                    ->create();
            }

            // A suspended user's reviews should be hidden.
            Review::factory()
                ->hidden()
                ->for($suspendedCustomer, 'user')
                ->for($salon, 'salon')
                ->create();
        }
    }
}