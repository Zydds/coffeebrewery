<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogImporter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ImportController extends Controller
{
    public function __construct(
        private CatalogImporter $importer
    ) {}

    public function create(Request $request)
    {
        $pending = $request->session()->get('import.pending');

        if ($pending && is_file($pending['path'])) {
            @unlink($pending['path']);
        }

        $request->session()->forget('import.pending');

        return view('admin.import.create');
    }

    public function template(string $type)
    {
        abort_unless(array_key_exists($type, CatalogImporter::TYPES), 404);

        [$headers, $example] = $this->importer->template($type);

        return $this->csvDownload("{$type}-template.csv", [$headers, $example]);
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(CatalogImporter::TYPES))],
            'file' => ['required', 'file', 'extensions:csv,txt', 'max:1024'],
        ], [
            'type.required' => 'Choose what you are importing.',
            'file.required' => 'Choose a CSV file.',
            'file.extensions' => 'Upload a .csv file.',
            'file.max' => 'The file must be 1 MB or smaller.',
        ]);

        $path = tempnam(sys_get_temp_dir(), 'catalog-import-');
        copy($data['file']->getRealPath(), $path);

        try {
            $preview = $this->importer->preview($data['type'], $path);
        } catch (ValidationException $exception) {
            @unlink($path);

            throw $exception;
        }

        $request->session()->put('import.pending', [
            'type' => $data['type'],
            'path' => $path,
            'name' => $data['file']->getClientOriginalName(),
        ]);

        return view('admin.import.preview', [
            'preview' => $preview,
            'type' => $data['type'],
            'name' => $data['file']->getClientOriginalName(),
        ]);
    }

    public function commit(Request $request)
    {
        $pending = $request->session()->get('import.pending');

        if (! $pending || ! is_file($pending['path'])) {
            return redirect()->route('admin.import.create')
                ->with('error', 'Upload a file and preview it before importing.');
        }

        $result = $this->importer->commit($pending['type'], $pending['path']);

        @unlink($pending['path']);
        $request->session()->forget('import.pending');
        $request->session()->put('import.failed', $result['failed']);
        $request->session()->put('import.failed_type', $pending['type']);

        return view('admin.import.result', [
            'result' => $result,
            'type' => $pending['type'],
            'name' => $pending['name'],
        ]);
    }

    public function failed(Request $request)
    {
        $failed = $request->session()->get('import.failed', []);
        $type = $request->session()->get('import.failed_type', 'import');

        if ($failed === []) {
            return redirect()->route('admin.import.create')
                ->with('error', 'There are no failed rows to download.');
        }

        $rows = [['line', 'slug', 'reason']];

        foreach ($failed as $row) {
            $rows[] = [$row['line'], $row['key'], $row['reason']];
        }

        return $this->csvDownload("{$type}-failed-rows.csv", $rows);
    }

    /**
     * @param  list<list<string|int>>  $rows
     */
    private function csvDownload(string $name, array $rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($handle, array_map($this->csvCell(...), $row), ',', '"', '');
            }

            fclose($handle);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Neutralise cells a spreadsheet would run as a formula.
     */
    private function csvCell(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
    }
}
