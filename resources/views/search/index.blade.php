<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #f3f5f8;
            --surface: #ffffff;
            --text: #1c2430;
            --muted: #5b6777;
            --border: #d7dde6;
            --accent: #0f6e56;
            --accent-dark: #0b5542;
            --danger: #9b1c1c;
            --warning: #8a5a00;
            --warning-bg: #fff7e6;
            --danger-bg: #fdecec;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(15, 110, 86, 0.12), transparent 35%),
                linear-gradient(180deg, #eef2f6 0%, var(--bg) 100%);
            min-height: 100vh;
        }

        .wrap {
            width: min(1100px, calc(100% - 2rem));
            margin: 2rem auto 3rem;
        }

        header h1 {
            margin: 0 0 0.35rem;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
        }

        header p {
            margin: 0;
            color: var(--muted);
            max-width: 46rem;
            line-height: 1.5;
        }

        .panel {
            margin-top: 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(28, 36, 48, 0.05);
        }

        .queries {
            display: grid;
            gap: 0.75rem;
        }

        label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
            color: var(--muted);
        }

        input[type="text"] {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            font-size: 1rem;
            background: #fbfdff;
        }

        input[type="text"]:focus {
            outline: 2px solid rgba(15, 110, 86, 0.25);
            border-color: var(--accent);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        button, .btn {
            appearance: none;
            border: 0;
            border-radius: 10px;
            padding: 0.8rem 1.1rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        button {
            background: var(--accent);
            color: #fff;
        }

        button:hover { background: var(--accent-dark); }

        .btn-secondary {
            background: #e8edf3;
            color: var(--text);
        }

        .alert {
            margin-top: 1rem;
            border-radius: 10px;
            padding: 0.9rem 1rem;
            line-height: 1.45;
        }

        .alert-error {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #f3c1c1;
        }

        .alert-warning {
            background: var(--warning-bg);
            color: var(--warning);
            border: 1px solid #f0d59a;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.25rem;
            color: var(--muted);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        th, td {
            text-align: left;
            vertical-align: top;
            padding: 0.8rem 0.7rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.92rem;
        }

        th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            background: #f7f9fc;
        }

        td a {
            color: var(--accent);
            word-break: break-all;
        }

        .query-chip {
            display: inline-block;
            background: #e8f5f0;
            color: var(--accent-dark);
            border-radius: 999px;
            padding: 0.2rem 0.6rem;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .empty {
            color: var(--muted);
            padding: 1rem 0 0.25rem;
        }

        @media (max-width: 640px) {
            .wrap { width: min(1100px, calc(100% - 1rem)); margin-top: 1rem; }
            .panel { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>{{ config('app.name') }}</h1>
            <p>Enter up to 5 search queries. Each query is sent to the ValueSERP API, results are stored, and you can export everything as CSV.</p>
        </header>

        <section class="panel">
            <form method="POST" action="{{ route('search.perform') }}">
                @csrf
                <div class="queries">
                    @for ($i = 0; $i < 5; $i++)
                        <div>
                            <label for="query-{{ $i }}">Search query {{ $i + 1 }}{{ $i === 0 ? ' *' : ' (optional)' }}</label>
                            <input
                                id="query-{{ $i }}"
                                type="text"
                                name="queries[]"
                                value="{{ old('queries.'.$i, $batch?->queries[$i] ?? '') }}"
                                placeholder="e.g. laravel csv export"
                                maxlength="200"
                                @if($i === 0) required @endif
                            >
                        </div>
                    @endfor
                </div>

                <div class="actions">
                    <button type="submit">Run Search</button>
                    @if ($batch)
                        <a class="btn btn-secondary" href="{{ route('search.export', $batch) }}">Download CSV</a>
                        <a class="btn btn-secondary" href="{{ route('search.index') }}">New Search</a>
                    @endif
                </div>
            </form>

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0; padding-left:1.1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($errors_list))
                <div class="alert alert-warning">
                    <ul style="margin:0; padding-left:1.1rem;">
                        @foreach ($errors_list as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>

        <section class="panel">
            @if ($batch)
                <div class="meta">
                    <div><strong>Batch #{{ $batch->id }}</strong></div>
                    <div>{{ $batch->result_count }} result{{ $batch->result_count === 1 ? '' : 's' }}</div>
                    <div>Queries: {{ implode(', ', $batch->queries) }}</div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Query</th>
                                <th>Pos</th>
                                <th>Title</th>
                                <th>Link</th>
                                <th>Snippet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $result)
                                <tr>
                                    <td><span class="query-chip">{{ $result->query }}</span></td>
                                    <td>{{ $result->position }}</td>
                                    <td>{{ $result->title }}</td>
                                    <td>
                                        @if ($result->link)
                                            <a href="{{ $result->link }}" target="_blank" rel="noopener noreferrer">{{ $result->displayed_link ?: $result->link }}</a>
                                        @endif
                                    </td>
                                    <td>{{ $result->snippet }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty">No results stored for this batch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="empty">Search results will appear here after you submit one or more queries.</p>
            @endif
        </section>
    </div>
</body>
</html>
