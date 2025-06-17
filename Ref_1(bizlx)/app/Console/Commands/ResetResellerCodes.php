<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResetResellerCodes extends Command
{
    protected $signature = 'reseller:reset-codes';
    protected $description = 'Reset all reseller usernames starting from RES001';

    public function handle()
    {
        DB::beginTransaction();
    
        try {
            $users = User::where('user_role', 3)
                ->orderBy('id', 'asc')
                ->get();
    
            if ($users->isEmpty()) {
                $this->warn("⚠️ No resellers found with user_role = 3");
                DB::rollBack();
                return;
            }
    
            // $count = 1;
            $count = 1;
    
            foreach ($users as $user) {
                $newUsername = 'RES' . str_pad($count, 3, '0', STR_PAD_LEFT);
                $oldUsername = $user->username;
    
                $this->info("🔄 User ID: {$user->id} | Old: {$oldUsername} ➜ New: {$newUsername}");
    
                // Update username
                $user->username = $newUsername;
                $user->save();
    
                // Save to history
                DB::table('username_history')->insert([
                    'user_id' => $user->id,
                    'old_username' => $oldUsername,
                    'new_username' => $newUsername,
                    'changed_at' => now(),
                ]);
    
                $count++;
            }
    
            DB::commit();
            $this->info("✅ All reseller usernames updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Exception: " . $e->getMessage());
        }
    }
    
    
    
}
