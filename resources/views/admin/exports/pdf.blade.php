<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medical Records PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>🩺 Medical Records</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Medical History</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $index => $patient)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->age ?? 'N/A' }}</td>
                    <td>{{ ucfirst($patient->gender ?? 'N/A') }}</td>
                    <td>{{ $patient->contact_number ?? 'N/A' }}</td>
                    <td>{{ $patient->address ?? 'N/A' }}</td>
                    <td>{{ $patient->medical_history ?? 'None' }}</td>
                    <td>{{ $patient->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
