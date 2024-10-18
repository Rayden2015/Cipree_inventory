<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\LogRecord;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;

class MySQLLoggingHandler extends AbstractProcessingHandler
{
    protected $table;

    /**
     * Reference:
     * https://github.com/markhilton/monolog-mysql/blob/master/src/Logger/Monolog/Handler/MysqlHandler.php
     */
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->table = 'error_logs';
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        if ($record['level'] >= Logger::ERROR) {
            $data = [
                'message'         => $record['message'],
                'context'         => json_encode($record['context']),
                'level'           => $record['level'],
                'level_name'      => $record['level_name'],
                'channel'         => $record['channel'],
                'record_datetime' => $record['datetime']->format('Y-m-d H:i:s'),
                'extra'           => json_encode($record['extra']),
                'formatted'       => $record['formatted'],
                'remote_addr'     => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent'      => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'created_at'      => now(),
            ];
        
        DB::connection()->table($this->table)->insert($data);
    }
}
}
