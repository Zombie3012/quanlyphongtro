<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('app:baseline-existing-database {--database= : Named database connection to inspect} {--apply : Record the verified existing schema as migrated}')]
#[Description('Safely baseline a complete legacy schema so only pending migrations are applied')]
class BaselineExistingDatabase extends Command
{
    /** @var array<string, list<string>> */
    private const REQUIRED_COLUMNS = [
        'users' => ['id', 'username', 'name', 'email', 'password', 'role', 'property_id'],
        'properties' => ['id', 'name', 'address', 'phone', 'description', 'status'],
        'rooms' => ['id', 'property_id', 'room_number', 'price', 'area', 'status'],
        'tenants' => ['id', 'property_id', 'full_name', 'phone', 'cccd', 'address'],
        'contracts' => ['id', 'property_id', 'room_id', 'tenant_id', 'start_date', 'end_date', 'monthly_rent', 'deposit', 'status'],
        'invoices' => ['id', 'contract_id', 'month', 'electricity', 'water', 'service_fee', 'other_fee', 'total', 'status'],
    ];

    /** @var list<string> */
    private const BASELINE_MIGRATIONS = [
        '0001_01_01_000000_create_users_table',
        '0001_01_01_000001_create_cache_table',
        '0001_01_01_000002_create_jobs_table',
        '2006_07_03_080734_create_properties_table',
        '2006_07_03_080800_add_property_fields',
        '2006_07_03_080820_create_rental_core_tables',
        '2006_07_03_080900_add_user_account_fields',
        '2006_07_03_081050_add_property_id_to_rooms_table',
        '2006_07_03_081343_add_property_id_to_tenants_table',
        '2006_07_03_081404_add_property_id_to_contracts_table',
        '2006_07_03_081625_add_property_id_to_users_table',
        '2006_07_03_081800_finalize_property_scopes',
    ];

    public function handle(): int
    {
        $connectionName = $this->option('database');

        if (! is_string($connectionName) || $connectionName === '') {
            $this->components->error('Pass --database with the connection to inspect. No database was changed.');

            return self::FAILURE;
        }

        try {
            $connection = DB::connection($connectionName);

            if (! in_array($connection->getDriverName(), ['mysql', 'mariadb'], true)) {
                $this->components->error('Only a MySQL/MariaDB connection can be baselined.');

                return self::FAILURE;
            }

            $schema = $connection->getSchemaBuilder();
            $missing = [];

            foreach (self::REQUIRED_COLUMNS as $table => $requiredColumns) {
                if (! $schema->hasTable($table)) {
                    $missing[] = "missing table {$table}";

                    continue;
                }

                $columns = $schema->getColumnListing($table);

                foreach (array_diff($requiredColumns, $columns) as $column) {
                    $missing[] = "missing column {$table}.{$column}";
                }
            }

            $cacheTables = ['cache', 'cache_locks'];
            $existingCacheTables = array_filter(
                $cacheTables,
                fn (string $table): bool => $schema->hasTable($table),
            );

            if (count($existingCacheTables) === 1) {
                $missing[] = 'cache/cache_locks tables are only partially present';
            } elseif (count($existingCacheTables) === 2) {
                foreach (['cache' => ['key', 'value', 'expiration'], 'cache_locks' => ['key', 'owner', 'expiration']] as $table => $columns) {
                    foreach (array_diff($columns, $schema->getColumnListing($table)) as $column) {
                        $missing[] = "missing column {$table}.{$column}";
                    }
                }
            } elseif (config('cache.default') === 'database') {
                $missing[] = 'database cache is configured but cache tables do not exist';
            }

            $jobTables = ['jobs', 'job_batches', 'failed_jobs'];
            $existingJobTables = array_filter(
                $jobTables,
                fn (string $table): bool => $schema->hasTable($table),
            );

            if ($existingJobTables !== [] && count($existingJobTables) !== count($jobTables)) {
                $missing[] = 'jobs/job_batches/failed_jobs tables are only partially present';
            }

            if (count($existingJobTables) === count($jobTables)) {
                $requiredJobColumns = [
                    'jobs' => ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'],
                    'job_batches' => ['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids', 'options', 'created_at', 'cancelled_at', 'finished_at'],
                    'failed_jobs' => ['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'],
                ];

                foreach ($requiredJobColumns as $table => $columns) {
                    foreach (array_diff($columns, $schema->getColumnListing($table)) as $column) {
                        $missing[] = "missing column {$table}.{$column}";
                    }
                }
            }

            if (config('session.driver') === 'database' && ! $schema->hasTable('sessions')) {
                $missing[] = 'database sessions are configured but the sessions table does not exist; use file sessions or create that table separately';
            }

            if ($missing !== []) {
                $this->components->error('The existing schema does not match the expected application schema: '.implode('; ', $missing));

                return self::FAILURE;
            }

            if ($schema->hasTable('migrations') && $connection->table('migrations')->exists()) {
                $this->components->error('The migrations table already contains records. Inspect its history manually; no changes were made.');

                return self::FAILURE;
            }

            $migrationNames = self::BASELINE_MIGRATIONS;

            if (count($existingCacheTables) === 0) {
                $migrationNames = array_values(array_diff($migrationNames, ['0001_01_01_000001_create_cache_table']));
            }

            if (count($existingJobTables) === 0) {
                $migrationNames = array_values(array_diff($migrationNames, ['0001_01_01_000002_create_jobs_table']));
            }

            if (in_array('rent_amount', $schema->getColumnListing('invoices'), true)) {
                $migrationNames[] = '2026_10_22_000000_add_rent_amount_snapshot_to_invoices';
            }

            if (! $this->option('apply')) {
                $this->components->info('Schema checks passed. Dry run only; no records or tables were written.');
                $this->line('Validated connection: '.$connectionName.' ('.$connection->getDatabaseName().')');
                $this->line('Would baseline '.count($migrationNames).' existing migrations.');
                $this->line('If this is the intended database and a restorable backup exists, rerun with --apply.');

                return self::SUCCESS;
            }

            if (! $schema->hasTable('migrations')) {
                $schema->create('migrations', function (Blueprint $table): void {
                    $table->id();
                    $table->string('migration');
                    $table->unsignedInteger('batch');
                });
            }

            $batch = ((int) $connection->table('migrations')->max('batch')) + 1;
            $rows = array_map(
                fn (string $migration): array => ['migration' => $migration, 'batch' => $batch],
                $migrationNames,
            );
            $connection->table('migrations')->insert($rows);

            $this->components->info('Existing schema baselined. Pending migrations were not executed.');
            $this->line('Run `php artisan migrate --database='.$connectionName.' --pretend` to inspect pending schema changes.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->components->error('Baseline check failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
