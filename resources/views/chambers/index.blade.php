<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container table-responsive mt-5 h-[100vh] overflow-hidden">
    @if(session('warning'))
            <div class="alert alert-warning" role="alert">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Display Success Message if exists -->
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="fs-5">Export Chamber Data as Excel Spreadsheet</h1>
            <a href="{{ route('chambers.export') }}" class="btn btn-success my-3">Export to Excel</a>
        </div>
        <table class="table border">
            <thead style="font-weight: normal; font-size: 14px;">
                <tr>
                    <th class="text-secondary fw-normal">Sl. No.</th>
                    <th class="text-secondary fw-normal">System Service ID</th>
                    <th class="text-secondary fw-normal">Reporting Date</th>
                    <th class="text-secondary fw-normal">Reporting Time</th>
                    <th class="text-secondary fw-normal">GPS Time</th>
                    <th class="text-secondary fw-normal">Temperature</th>
                    <th class="text-secondary fw-normal">Reporting Date</th>
                    <th class="text-secondary fw-normal">Reporting Time</th>
                    <th class="text-secondary fw-normal">GPS Time</th>
                    <th class="text-secondary fw-normal">Temperature</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-secondary fw-medium">No data found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>