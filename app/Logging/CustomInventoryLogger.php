<?php

namespace App\Logging;

use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord; // Import LogRecord

class CustomInventoryLogger extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
{
    try {
        DB::table('update_inventory_item')->insert([
            'user_id' => $record->context['user_id'] ?? null,
            'before_InventoryItemDetail_edit' => json_encode($record->context['before_inventory_item_detail_edit'] ?? []),
            'after_InventoryItemDetail_edit' => json_encode($record->context['after_inventory_item_detail_edit'] ?? []),
            'new_amount' => $record->context['new_amount'] ?? null,
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {
        // Handle error logging
        \Log::error('Failed to log inventory update to database', [
            'error' => $e->getMessage(),
            'record' => $record,
        ]);
    }
}


    // Implementing the __invoke method to create a handler instance
    public function __invoke(array $config)
    {
        return new Logger($config['name'], [$this]); // Pass the handler to the logger
    }
}
