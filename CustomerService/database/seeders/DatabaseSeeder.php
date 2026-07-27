<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admins / Representatives (won't crash if they already exist)
        $admin  = User::firstOrCreate(['email' => 'admin@test.com'],  ['name' => 'Admin User', 'password' => Hash::make('password'), 'role' => 'admin']);
        $louise = User::firstOrCreate(['email' => 'louise@test.com'], ['name' => 'Louise Lane Parin', 'password' => Hash::make('password'), 'role' => 'admin']);
        $geon   = User::firstOrCreate(['email' => 'geon@test.com'],   ['name' => 'Geon Amparo', 'password' => Hash::make('password'), 'role' => 'admin']);
        $emman  = User::firstOrCreate(['email' => 'emman@test.com'],  ['name' => 'Emmanuel Aragon', 'password' => Hash::make('password'), 'role' => 'admin']);
        $jerard = User::firstOrCreate(['email' => 'jerard@test.com'], ['name' => 'Jerard Baluyot', 'password' => Hash::make('password'), 'role' => 'admin']);

        // Update names in case they changed
        User::where('email', 'admin@test.com')->update(['name' => 'Admin User']);
        User::where('email', 'louise@test.com')->update(['name' => 'Louise Lane Parin']);
        User::where('email', 'geon@test.com')->update(['name' => 'Geon Amparo']);
        User::where('email', 'emman@test.com')->update(['name' => 'Emmanuel Aragon']);
        User::where('email', 'jerard@test.com')->update(['name' => 'Jerard Baluyot']);

        // 2. Create Customers
        $customer1 = User::firstOrCreate(['email' => 'charlize@test.com'], ['name' => 'Charlize Casama', 'password' => Hash::make('password'), 'role' => 'customer']);
        $customer2 = User::firstOrCreate(['email' => 'gwen@test.com'], ['name' => 'Gwen Dogelio', 'password' => Hash::make('password'), 'role' => 'customer']);

        // 3. Create Sample Tickets (only if they don't exist)
        if (!Ticket::where('ticket_reference', 'TKT-1001')->exists()) {
            Ticket::create([
                'ticket_reference' => 'TKT-1001', 
                'user_id' => $customer1->id, 
                'agent_id' => $geon->id,
                'subject' => 'Order #54321 not received after 10 days', 
                'description' => 'I have been waiting for my order.',
                'category' => 'Shipping & Delivery', 
                'status' => 'open', 
                'priority' => 'high', 
                'priority_level' => 'High',
                'due_date' => Carbon::now()->subHours(28)
            ]);
        }

        if (!Ticket::where('ticket_reference', 'TKT-1002')->exists()) {
            Ticket::create([
                'ticket_reference' => 'TKT-1002', 
                'user_id' => $customer2->id, 
                'agent_id' => $louise->id,
                'subject' => 'Received wrong item - ordered blue, got red', 
                'description' => 'Wrong color delivered.',
                'category' => 'Returns', 
                'status' => 'in-progress', 
                'priority' => 'medium', 
                'priority_level' => 'Medium',
                'due_date' => Carbon::now()->subHours(12)
            ]);
        }
        
        if (!Ticket::where('ticket_reference', 'TKT-1005')->exists()) {
            Ticket::create([
                'ticket_reference' => 'TKT-1005', 
                'user_id' => $customer1->id, 
                'agent_id' => null,
                'subject' => 'Product arrived damaged - broken screen', 
                'description' => 'The screen is shattered.',
                'category' => 'Refunds', 
                'status' => 'open', 
                'priority' => 'critical', 
                'priority_level' => 'Critical',
                'due_date' => Carbon::now()->addHours(4)
            ]);
        }
    }
}