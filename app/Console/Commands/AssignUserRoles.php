<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign roles to users without roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();
        
        $this->info("Found {$usersWithoutRoles->count()} users without roles");
        
        $adminRole = Role::where('name', 'admin')->first();
        
        foreach ($usersWithoutRoles as $user) {
            // Atribuir role admin por padrão (você pode personalizar isso)
            $user->assignRole($adminRole);
            $this->info("Assigned admin role to user: {$user->name} ({$user->email})");
        }
        
        $this->info('All users now have roles assigned!');
        
        return 0;
    }
}
