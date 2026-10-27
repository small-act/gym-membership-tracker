<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use JsonException;

class DataTransferController extends Controller
{
    /**
     * Operational tables included in backups.
     * User accounts, passwords, sessions, and Laravel system tables
     * are deliberately excluded.
     */
    private const TABLES = [
        'membership_plans',
        'members',
        'memberships',
        'attendances',
        'payments',
    ];

    public function index()
    {
        $this->authorizeAccess(request());

        return view('data-transfer.index');
    }

    public function export(Request $request)
    {
        $this->authorizeAccess($request);

        $data = [];

        foreach (self::TABLES as $table) {
            if (!Schema::hasTable($table)) {
                abort(500, "Required table [{$table}] does not exist.");
            }

            $data[$table] = DB::table($table)
                ->orderBy('id')
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();
        }

        $backup = [
            'format' => 'gym-membership-tracker-backup',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'tables' => $data,
        ];

        $filename = 'gym-tracker-backup-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(
            function () use ($backup) {
                echo json_encode(
                    $backup,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
                );
            },
            $filename,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    public function import(Request $request)
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'backup_file' => [
                'required',
                'file',
                'mimes:json,txt',
                'max:20480',
            ],
            'confirmation' => ['required', 'in:RESTORE'],
        ]);

        $file = $validated['backup_file'];
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || trim($contents) === '') {
            throw ValidationException::withMessages([
                'backup_file' => 'The uploaded backup is empty or could not be read.',
            ]);
        }

        // Remove a UTF-8 BOM if one exists at the start of the file.
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);

        try {
            $backup = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw ValidationException::withMessages([
                'backup_file' => 'JSON parsing failed: '
                    . $exception->getMessage()
                    . '. File size received: '
                    . strlen($contents)
                    . ' bytes.',
            ]);
        }

        if (
            !is_array($backup) ||
            ($backup['format'] ?? null) !== 'gym-membership-tracker-backup' ||
            ($backup['version'] ?? null) !== 1 ||
            !isset($backup['tables']) ||
            !is_array($backup['tables'])
        ) {
            throw ValidationException::withMessages([
                'backup_file' =>
                    'The JSON is readable, but it is not a supported Gym Tracker backup.',
            ]);
        }

        // Verify that all required tables exist in the backup and database.
        foreach (self::TABLES as $table) {
            if (
                !array_key_exists($table, $backup['tables']) ||
                !is_array($backup['tables'][$table])
            ) {
                throw ValidationException::withMessages([
                    'backup_file' =>
                        "The backup is missing valid data for {$table}.",
                ]);
            }

            if (!Schema::hasTable($table)) {
                abort(500, "Required table [{$table}] does not exist.");
            }
        }

        // Protect existing operational records: never overwrite or merge.
        foreach (self::TABLES as $table) {
            if (DB::table($table)->exists()) {
                throw ValidationException::withMessages([
                    'backup_file' =>
                        'Restore was stopped because the '
                        . $table
                        . ' table is not empty. Your existing records were not changed. '
                        . 'Restore requires all five operational tables to be empty.',
                ]);
            }
        }

        // Validate every row before inserting any records.
        foreach (self::TABLES as $table) {
            $allowedColumns = Schema::getColumnListing($table);

            foreach ($backup['tables'][$table] as $index => $row) {
                if (!is_array($row)) {
                    throw ValidationException::withMessages([
                        'backup_file' =>
                            "Invalid record in {$table} at row "
                            . ($index + 1)
                            . '.',
                    ]);
                }

                $unexpected = array_diff(
                    array_keys($row),
                    $allowedColumns
                );

                if (!empty($unexpected)) {
                    throw ValidationException::withMessages([
                        'backup_file' =>
                            "The backup contains unknown columns in {$table}: "
                            . implode(', ', $unexpected)
                            . '.',
                    ]);
                }

                if (!array_key_exists('id', $row)) {
                    throw ValidationException::withMessages([
                        'backup_file' =>
                            "A record in {$table} is missing its ID.",
                    ]);
                }
            }
        }

        // A failure during insertion rolls back the complete restore.
        DB::transaction(function () use ($backup) {
            foreach (self::TABLES as $table) {
                foreach (
                    array_chunk($backup['tables'][$table], 200) as $chunk
                ) {
                    foreach ($chunk as $row) {
                        DB::table($table)->insert($row);
                    }
                }
            }
        });

        return redirect()
            ->route('data-transfer.index')
            ->with(
                'success',
                'Restore completed successfully. Operational records were imported. User accounts were not changed.'
            );
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless(
            in_array(
                $request->user()?->role,
                ['super_admin', 'admin'],
                true
            ),
            403,
            'Only Admin and Super Admin can import or export system data.'
        );
    }
}
