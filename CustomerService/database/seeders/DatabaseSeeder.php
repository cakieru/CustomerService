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
        // 1. Create Admins / Representatives
        $admin  = User::create(['name' => 'Admin User', 'email' => 'admin@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
        $geon   = User::create(['name' => 'Geon', 'email' => 'geon@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
        $yen    = User::create(['name' => 'Yen', 'email' => 'yen@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
        $emman  = User::create(['name' => 'Emman', 'email' => 'emman@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);
        $jerard = User::create(['name' => 'Jerard', 'email' => 'jerard@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);

        // 2. Create Customers
        $customer1 = User::create(['name' => 'Charlize Casama', 'email' => 'charlize@test.com', 'password' => Hash::make('password'), 'role' => 'customer']);
        $customer2 = User::create(['name' => 'Gwen Dogelio', 'email' => 'gwen@test.com', 'password' => Hash::make('password'), 'role' => 'customer']);

        // 3. Create Sample Tickets
        Ticket::create([
            'ticket_reference' => 'TKT-1001', 
            'user_id' => $customer1->id, 
            'agent_id' => $geon->id, // Assigned to Geon
            'subject' => 'Order #54321 not received after 10 days', 
            'description' => 'I have been waiting for my order.',
            'category' => 'Shipping & Delivery', 
            'status' => 'open', 
            'priority' => 'high', 
            'due_date' => Carbon::now()->subHours(28)
        ]);

        Ticket::create([
            'ticket_reference' => 'TKT-1002', 
            'user_id' => $customer2->id, 
            'agent_id' => $yen->id, // Assigned to Yen
            'subject' => 'Received wrong item - ordered blue, got red', 
            'description' => 'Wrong color delivered.',
            'category' => 'Returns', 
            'status' => 'in-progress', 
            'priority' => 'medium', 
            'due_date' => Carbon::now()->subHours(12)
        ]);
        
        Ticket::create([
            'ticket_reference' => 'TKT-1005', 
            'user_id' => $customer1->id, 
            'agent_id' => null, // Unassigned
            'subject' => 'Product arrived damaged - broken screen', 
            'description' => 'The screen is shattered.',
            'category' => 'Refunds', 
            'status' => 'open', 
            'priority' => 'critical', 
            'due_date' => Carbon::now()->addHours(4)
        ]);
    }
}