<?php

    namespace app\DTO;

    use App\DTO\LogsDTO;

    class LogsCollectionDTO {

        public function __construct($logs) {
            $this->logs = $logs->map(function ($log) {
               return new LogsDTO(
                $log->table_name,
                $log->row_id,
                $log->value_before,
                $log->value_after
               ); 
            });
        } 
    }