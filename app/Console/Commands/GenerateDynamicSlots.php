<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateDynamicSlots extends Command
{
    protected $signature = 'slots:generate-dynamic {--days=30 : Number of days to generate slots for}';

    protected $description = 'Auto-generate slots using configured method (bay or template)';

    public function handle()
    {
        $days = (int) $this->option('days');
        $method = Setting::getSlotGenerationMethod();

        $this->info("🔍 Slot generation method: {$method}");
        $this->info("📅 Generating slots for next {$days} day(s)...");

        if ($method === 'bay') {
            // Bay-based generation respects:
            // - Per-bay operating hours
            // - Equipment requirements (handball capability)
            // - Customer bay assignments
            // - Individual bay capacity
            $this->newLine();
            $this->line("🚪 Using bay-based generation:");
            $this->line("   • Respects per-bay operating hours");
            $this->line("   • Considers equipment requirements");
            $this->line("   • Applies customer bay assignments");
            $this->newLine();

            $exitCode = Artisan::call('slots:generate-by-bay', ['--days' => $days], $this->getOutput());

            if ($exitCode !== 0) {
                $this->error("Bay-based generation failed with exit code: {$exitCode}");
                return $exitCode;
            }
        } else {
            // Template-based generation uses slot templates
            $this->newLine();
            $this->line("📋 Using template-based generation:");
            $this->line("   • Uses configured slot templates");
            $this->line("   • Applies slot release rules");
            $this->newLine();

            $exitCode = Artisan::call('slots:generate', ['--days' => $days], $this->getOutput());

            if ($exitCode !== 0) {
                $this->error("Template-based generation failed with exit code: {$exitCode}");
                return $exitCode;
            }
        }

        $this->newLine();
        $this->info("✅ Dynamic slot generation completed successfully!");

        return 0;
    }
}
