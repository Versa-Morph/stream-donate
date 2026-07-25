<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing bank_name values are free text (e.g. "Bank Central Asia") from
        // before this migration; they don't match the new coded vocabulary in
        // config/banks.php. Null them out — affected streamers re-select from
        // the dropdown next time they visit Settings. One-time, unavoidable cost
        // of moving from free text to a controlled vocabulary (see design spec).
        $validCodes = array_keys(config('banks'));

        DB::table('streamers')
            ->whereNotNull('bank_name')
            ->whereNotIn('bank_name', $validCodes)
            ->update(['bank_name' => null, 'bank_account_number' => null, 'bank_account_holder' => null]);
    }

    public function down(): void
    {
        // Irreversible by design — the original free-text values are gone.
    }
};
