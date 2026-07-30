<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchQueriesRequest;
use App\Models\SearchBatch;
use App\Services\ValueSerpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SearchController extends Controller
{
    public function index(): View
    {
        return view('search.index', [
            'batch' => null,
            'results' => collect(),
            'errors_list' => [],
        ]);
    }

    public function search(SearchQueriesRequest $request, ValueSerpService $valueSerp): RedirectResponse
    {
        $queries = $request->validated('queries');
        $allResults = [];
        $errors = [];

        foreach ($queries as $query) {
            try {
                $results = $valueSerp->search($query);

                if ($results === []) {
                    $errors[] = "No results found for \"{$query}\".";
                    continue;
                }

                $allResults = array_merge($allResults, $results);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            } catch (Throwable $e) {
                report($e);
                $errors[] = "Unexpected error while searching for \"{$query}\".";
            }
        }

        if ($allResults === []) {
            return back()
                ->withInput()
                ->withErrors(['queries' => $errors !== [] ? implode(' ', $errors) : 'No search results were found for the provided queries.']);
        }

        $batch = DB::transaction(function () use ($queries, $allResults) {
            $batch = SearchBatch::create([
                'queries' => $queries,
                'result_count' => count($allResults),
            ]);

            $batch->results()->createMany($allResults);

            return $batch;
        });

        return redirect()
            ->route('search.show', $batch)
            ->with('warnings', $errors);
    }

    public function show(SearchBatch $batch): View
    {
        $batch->load(['results' => fn ($query) => $query->orderBy('query')->orderBy('position')]);

        return view('search.index', [
            'batch' => $batch,
            'results' => $batch->results,
            'errors_list' => session('warnings', []),
        ]);
    }

    public function export(SearchBatch $batch): StreamedResponse|RedirectResponse
    {
        $results = $batch->results()->orderBy('query')->orderBy('position')->get();

        if ($results->isEmpty()) {
            return redirect()
                ->route('search.show', $batch)
                ->withErrors(['export' => 'There are no results available to export.']);
        }

        $filename = 'search-results-'.$batch->id.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($results) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Query', 'Position', 'Title', 'Link', 'Snippet', 'Displayed Link']);

            foreach ($results as $result) {
                fputcsv($handle, [
                    $result->query,
                    $result->position,
                    $result->title,
                    $result->link,
                    $result->snippet,
                    $result->displayed_link,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
