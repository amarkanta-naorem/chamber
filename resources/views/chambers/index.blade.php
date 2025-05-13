<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamber Data Export</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        html, body, .table-wrapper {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        .table-wrapper::-webkit-scrollbar {
            display: none;
        }

        .table th {
            background-color: #f1f3f5;
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-success {
            background-color: #198754;
        }

        .message-text {
            font-style: italic;
            color: #b02a37;
        }
        
        .text-label {
            font-size: 10px;
            font-style: italic;
            font-weight: 300;
            /* color: rgba(255, 255, 255, 0.85); */
            letter-spacing: 0.5px;
        }

        .export-btn {
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .export-title {
            font-size: 14px;
        }

        .message-tag {
            font-size: 0.75rem;
            font-style: italic;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
            max-width: 100%;
        }

        .fixed-header {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 20;
            padding: 1rem 0;
            /* border-bottom: 1px solid #dee2e6; */
        }

        .table-wrapper {
            max-height: 76vh; /* Adjust as needed */
            overflow-y: auto;
            overflow-x: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f1f3f5;
        }


    </style>
</head>

<body>
    <div class="container-fluid my-3">
        <div class="card p-4">
            <div class="fixed-header d-flex justify-content-between align-items-center">
                <h2 class="fs-5 text-dark">Export Chamber Data as Excel</h2>
                <div>
                    <a href="{{ route('chambers.export') }}" class="btn btn-success export-btn d-inline-flex align-items-center px-3 py-2 shadow-sm">
                        <i class="bi bi-download me-2 fs-4"></i>
                        <span class="d-flex flex-column text-start">
                            <span class="export-title fw-semibold">Export to Excel</span>
                            <span class="text-label">Chamber data - {{ \Carbon\Carbon::parse($formattedChambers[0]['first_row_date'])->format('F, Y') }}</span>
                        </span>
                    </a>
                    <a href="{{ route('chambers.exportMissing') }}" class="btn btn-danger text-white export-btn d-inline-flex align-items-center px-3 py-2 shadow-sm">
                        <i class="bi bi-file-earmark-text me-2 fs-4"></i>
                        <span class="d-flex flex-column text-start">
                            <span class="export-title fw-semibold">Export to TXT</span>
                            <span class="text-label">missing data</span>
                        </span>
                    </a>
                </div>
            </div>

            <div class="table-wrapper mt-0">
                <table class="table table-bordered align-middle text-nowrap">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th>Sl. No.</th>
                            <th>Sys. Svc. ID</th>
                            <th>Reporting Date</th>
                            <th>Reporting Time</th>
                            <th>GPS Time</th>
                            <th>Temperature</th>
                            <th>Reporting Date</th>
                            <th>Reporting Time</th>
                            <th>GPS Time</th>
                            <th>Temperature</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($formattedChambers as $chamber)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $chamber['sys_service_id'] }}</td>
                            <td>{{ $chamber['first_row_date'] }}</td>
                            <td>{{ $chamber['first_row_time'] }}</td>
                            <td>{{ $chamber['first_row_gps_time'] }}</td>
                            <td>{{ $chamber['first_row_tel_temperature'] }}</td>
                            <td>{{ $chamber['second_row_date'] }}</td>
                            <td>{{ $chamber['second_row_time'] }}</td>
                            <td>{{ $chamber['second_row_gps_time'] }}</td>
                            <td>{{ $chamber['second_row_tel_temperature'] }}</td>
                            <td>
                                @php
                                    $rawMessage = $chamber['message'];
                                    $messages = [];

                                    if (Str::contains($rawMessage, 'Morning data is missing')) {
                                        $messages[] = 'Morning data is missing';
                                    }

                                    if (Str::contains($rawMessage, 'Afternoon data is missing')) {
                                        $messages[] = 'Afternoon data is missing';
                                    }
                                @endphp

                                <div class="d-flex flex-column gap-1">
                                    @foreach($messages as $msg)
                                        @if($msg === 'Morning data is missing')
                                            <span class="message-tag bg-warning text-dark">{{ $msg }}</span>
                                        @elseif($msg === 'Afternoon data is missing')
                                            <span class="message-tag bg-info text-dark">{{ $msg }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-5">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>

</html>
