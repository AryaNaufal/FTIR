<?php

return ['mysqldump' => env('MYSQLDUMP_BINARY', 'mysqldump'), 'backup_path' => env('FTIR_BACKUP_PATH', storage_path('app/private/backups'))];
