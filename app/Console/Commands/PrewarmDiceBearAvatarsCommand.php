<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Console\Command;

class PrewarmDiceBearAvatarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:prewarm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-generate static SVG files for all DiceBear styles and existing users for instant loading';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Pre-warming style preview icons...');
        foreach (array_keys(AvatarService::getStyles()) as $style) {
            $this->output->write("  - Rendering preview for {$style}... ");
            AvatarService::renderSvg($style, 'preview');
            $this->output->writeln('<info>DONE</info>');
        }

        $this->info('Pre-warming existing user avatars...');
        $users = User::all();
        foreach ($users as $user) {
            $style = $user->avatar_style ?: 'lorelei';
            $seed = $user->avatar_seed ?: ($user->name ?: 'User-' . $user->id);
            $this->output->write("  - Rendering for {$user->name} ({$style})... ");
            AvatarService::renderSvg($style, $seed);
            $this->output->writeln('<info>DONE</info>');
        }

        $this->info('All avatar SVGs pre-warmed successfully to storage/app/public/avatars!');
        return self::SUCCESS;
    }
}
