<!DOCTYPE html>
<html>

<head>
    <title>Flipbook System Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .warning {
            color: #ffc107;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>🔍 Flipbook System Diagnostic</h1>

    <!-- System Check -->
    <div class="section">
        <h2>System Requirements</h2>
        <table>
            <tr>
                <th>Requirement</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td class="{{ version_compare(PHP_VERSION, '8.1.0', '>=') ? 'success' : 'error' }}">
                    {{ version_compare(PHP_VERSION, '8.1.0', '>=') ? '✓' : '✗' }}
                </td>
                <td>{{ PHP_VERSION }}</td>
            </tr>
            <tr>
                <td>Imagick Extension</td>
                <td class="{{ extension_loaded('imagick') ? 'success' : 'error' }}">
                    {{ extension_loaded('imagick') ? '✓' : '✗' }}
                </td>
                <td>
                    @if (extension_loaded('imagick'))
                        <span class="success">Installed - Version {{ phpversion('imagick') }}</span>
                    @else
                        <span class="error">NOT INSTALLED</span>
                        <div
                            style="margin-top: 10px; padding: 10px; background: #fff3cd; border-left: 3px solid #ffc107;">
                            <strong>⚠️ Note:</strong> Without Imagick, the system will use <strong>PDF.js</strong> to
                            render PDFs directly in the browser.
                            <br><br>
                            <strong>To install Imagick:</strong>
                            <ul style="margin: 10px 0 0 20px;">
                                <li>Windows: Download DLL from <a href="https://pecl.php.net/package/imagick"
                                        target="_blank" style="color: #007bff;">pecl.php.net</a></li>
                                <li>Add <code>extension=imagick</code> to php.ini</li>
                                <li>Restart web server (Apache/Nginx)</li>
                            </ul>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Storage Directory Writable</td>
                <td class="{{ is_writable(storage_path('app/public')) ? 'success' : 'error' }}">
                    {{ is_writable(storage_path('app/public')) ? '✓' : '✗' }}
                </td>
                <td>{{ storage_path('app/public') }}</td>
            </tr>
            <tr>
                <td>Storage Link</td>
                <td class="{{ file_exists(public_path('storage')) ? 'success' : 'error' }}">
                    {{ file_exists(public_path('storage')) ? '✓' : '✗' }}
                </td>
                <td>
                    @if (!file_exists(public_path('storage')))
                        <span class="error">Run: php artisan storage:link</span>
                    @else
                        Linked
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Database Tables -->
    <div class="section">
        <h2>Database Tables</h2>
        @php
            $tables = ['flipbook_templates', 'flipbooks', 'flipbook_pages', 'flipbook_hotspots', 'flipbook_analytics'];
            $tableStatus = [];
            foreach ($tables as $table) {
                try {
                    $exists = \DB::select("SHOW TABLES LIKE '$table'");
                    $tableStatus[$table] = !empty($exists);
                } catch (\Exception $e) {
                    $tableStatus[$table] = false;
                }
            }
        @endphp
        <table>
            <tr>
                <th>Table</th>
                <th>Status</th>
                <th>Records</th>
            </tr>
            @foreach ($tableStatus as $table => $exists)
                <tr>
                    <td>{{ $table }}</td>
                    <td class="{{ $exists ? 'success' : 'error' }}">
                        {{ $exists ? '✓' : '✗' }}
                    </td>
                    <td>
                        @if ($exists)
                            @php
                                try {
                                    $count = \DB::table($table)->count();
                                    echo $count;
                                } catch (\Exception $e) {
                                    echo 'Error';
                                }
                            @endphp
                        @else
                            Run migrations
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        @if (in_array(false, $tableStatus))
            <p class="error">⚠️ Missing tables! Run: <code>php artisan migrate</code></p>
        @endif
    </div>

    <!-- Flipbooks -->
    <div class="section">
        <h2>Flipbooks in Database</h2>
        @php
            try {
                $flipbooks = \App\Models\Flipbook::with(['pages', 'template'])->get();
            } catch (\Exception $e) {
                $flipbooks = collect();
                $error = $e->getMessage();
            }
        @endphp

        @if (isset($error))
            <p class="error">Error: {{ $error }}</p>
        @elseif($flipbooks->isEmpty())
            <p class="warning">⚠️ No flipbooks found. <a href="{{ route('flipbooks.create') }}">Create one</a></p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Pages</th>
                        <th>Status</th>
                        <th>PDF Exists</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($flipbooks as $flipbook)
                        <tr>
                            <td>{{ $flipbook->id }}</td>
                            <td>{{ $flipbook->title }}</td>
                            <td><code>{{ $flipbook->slug }}</code></td>
                            <td>{{ $flipbook->pages->count() }} / {{ $flipbook->total_pages }}</td>
                            <td>
                                @if ($flipbook->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                                @if (!$flipbook->is_public)
                                    <span class="badge badge-danger">Private</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pdfExists = \Storage::disk('public')->exists($flipbook->pdf_path);
                                @endphp
                                <span class="{{ $pdfExists ? 'success' : 'error' }}">
                                    {{ $pdfExists ? '✓' : '✗' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('flipbooks.edit', $flipbook) }}" class="btn btn-primary">Edit</a>
                                @if ($flipbook->is_published)
                                    <a href="{{ route('flipbook.viewer', $flipbook->slug) }}" class="btn btn-success"
                                        target="_blank">View</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- PDF File Verification -->
    @if ($flipbooks->isNotEmpty())
        <div class="section">
            <h2>PDF File Verification</h2>
            @php
                $firstFlipbook = $flipbooks->first();
            @endphp
            @if ($firstFlipbook)
                <table>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                    <tr>
                        <td>PDF Path in DB</td>
                        <td class="success">✓</td>
                        <td><code>{{ $firstFlipbook->pdf_path }}</code></td>
                    </tr>
                    <tr>
                        <td>Storage File Exists</td>
                        @php
                            $storagePath = storage_path('app/public/' . $firstFlipbook->pdf_path);
                            $storageExists = file_exists($storagePath);
                        @endphp
                        <td class="{{ $storageExists ? 'success' : 'error' }}">
                            {{ $storageExists ? '✓' : '✗' }}
                        </td>
                        <td>{{ $storagePath }}</td>
                    </tr>
                    <tr>
                        <td>Public Symlink Access</td>
                        @php
                            $publicPath = public_path('storage/' . $firstFlipbook->pdf_path);
                            $publicExists = file_exists($publicPath);
                        @endphp
                        <td class="{{ $publicExists ? 'success' : 'error' }}">
                            {{ $publicExists ? '✓' : '✗' }}
                        </td>
                        <td>
                            {{ $publicPath }}
                            @if (!$publicExists)
                                <br><span class="error">Run: php artisan storage:link</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Asset URL</td>
                        <td class="success">✓</td>
                        <td><a href="{{ asset('storage/' . $firstFlipbook->pdf_path) }}" target="_blank">
                                {{ asset('storage/' . $firstFlipbook->pdf_path) }}</a></td>
                    </tr>
                    <tr>
                        <td>File Size</td>
                        <td class="{{ $storageExists ? 'success' : 'error' }}">
                            {{ $storageExists ? '✓' : '✗' }}
                        </td>
                        <td>
                            @if ($storageExists)
                                {{ number_format(filesize($storagePath) / 1024 / 1024, 2) }} MB
                            @else
                                File not found
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>MIME Type</td>
                        <td class="{{ $storageExists ? 'success' : 'error' }}">
                            {{ $storageExists ? '✓' : '✗' }}
                        </td>
                        <td>
                            @if ($storageExists)
                                {{ mime_content_type($storagePath) }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    @endif

    <!-- Routes -->
    <div class="section">
        <h2>Available Routes</h2>
        <table>
            <tr>
                <th>Route</th>
                <th>URL</th>
            </tr>
            <tr>
                <td>Dashboard</td>
                <td><a href="{{ route('dashboard') }}">{{ route('dashboard') }}</a></td>
            </tr>
            <tr>
                <td>Flipbooks List</td>
                <td><a href="{{ route('flipbooks.index') }}">{{ route('flipbooks.index') }}</a></td>
            </tr>
            <tr>
                <td>Create Flipbook</td>
                <td><a href="{{ route('flipbooks.create') }}">{{ route('flipbooks.create') }}</a></td>
            </tr>
            @if ($flipbooks->isNotEmpty())
                @php $first = $flipbooks->first(); @endphp
                <tr>
                    <td>View Flipbook (example)</td>
                    <td><a href="{{ route('flipbook.viewer', $first->slug) }}"
                            target="_blank">{{ route('flipbook.viewer', $first->slug) }}</a></td>
                </tr>
            @endif
        </table>
    </div>

    <!-- File Storage Check -->
    <div class="section">
        <h2>File Storage Check</h2>
        <table>
            <tr>
                <th>Directory</th>
                <th>Exists</th>
                <th>Writable</th>
                <th>Path</th>
            </tr>
            @php
                $dirs = [
                    'storage/app/public' => storage_path('app/public'),
                    'storage/app/public/flipbooks' => storage_path('app/public/flipbooks'),
                    'storage/app/public/flipbooks/pdfs' => storage_path('app/public/flipbooks/pdfs'),
                    'public/storage' => public_path('storage'),
                ];
            @endphp
            @foreach ($dirs as $name => $path)
                <tr>
                    <td>{{ $name }}</td>
                    <td class="{{ file_exists($path) ? 'success' : 'error' }}">
                        {{ file_exists($path) ? '✓' : '✗' }}
                    </td>
                    <td class="{{ is_writable($path) ? 'success' : 'warning' }}">
                        {{ is_writable($path) ? '✓' : '✗' }}
                    </td>
                    <td><small>{{ $path }}</small></td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- Quick Actions -->
    <div class="section">
        <h2>Quick Actions</h2>
        <p>
            <a href="{{ route('flipbooks.index') }}" class="btn btn-primary">Go to Flipbooks</a>
            <a href="{{ route('flipbooks.create') }}" class="btn btn-success">Upload New PDF</a>
            @if ($flipbooks->isNotEmpty() && $flipbooks->where('is_published', true)->isNotEmpty())
                @php $published = $flipbooks->where('is_published', true)->first(); @endphp
                <a href="{{ route('flipbook.viewer', $published->slug) }}" class="btn btn-success" target="_blank">View
                    Published Flipbook</a>
            @endif
        </p>
    </div>

    <!-- Installation Steps -->
    @if (!extension_loaded('imagick') || !file_exists(public_path('storage')) || in_array(false, $tableStatus))
        <div class="section">
            <h2>⚠️ Setup Required</h2>
            <h3>Run these commands:</h3>
            <pre>
# Run migrations (if tables missing)
php artisan migrate

# Seed templates (if no templates)
php artisan db:seed --class=FlipbookTemplateSeeder

# Create storage link (if missing)
php artisan storage:link

# For Imagick (Windows/Laragon):
# 1. Download from: https://pecl.php.net/package/imagick
# 2. Extract php_imagick.dll to C:\laragon\bin\php\php8.x\ext\
# 3. Add to php.ini: extension=imagick
# 4. Restart Apache
        </pre>
        </div>
    @endif
</body>

</html>
