<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>O'qituvchi Hisoboti</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th {
            background-color: #4F46E5;
            color: white;
            padding: 10px 5px;
            text-align: left;
            font-size: 11px;
        }
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px 5px;
            font-size: 11px;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #E0E7FF !important;
            font-weight: bold;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .stat-box .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #4F46E5;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>O'QITUVCHI YUKLAMA HISOBOTI</h1>
        <p>{{ date('d.m.Y H:i') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>O'qituvchi:</td>
            <td>{{ $teacher->user->name ?? '' }}</td>
        </tr>
        <tr>
            <td>Lavozim:</td>
            <td>{{ $teacher->position ?? '—' }}</td>
        </tr>
        <tr>
            <td>Kafedra:</td>
            <td>{{ $teacher->department->name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Semestr:</td>
            <td>{{ $semester ?? 'Joriy semestr' }}</td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Jami soatlar</div>
            <div class="value">{{ $stats['total_hours'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Fanlar soni</div>
            <div class="value">{{ $stats['total_subjects'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Guruhlar</div>
            <div class="value">{{ $stats['total_groups'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Ma'ruza</div>
            <div class="value">{{ $stats['lecture_hours'] }}</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">№</th>
                <th style="width: 25%;">Fan nomi</th>
                <th style="width: 10%;">Fan kodi</th>
                <th style="width: 15%;">Guruh</th>
                <th style="width: 9%;">Ma'ruza</th>
                <th style="width: 9%;">Seminar</th>
                <th style="width: 9%;">Amaliy</th>                
                <th style="width: 9%;">Imtihon</th>
                <th style="width: 9%;">Jami</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workloads as $index => $workload)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $workload->subject->name ?? '' }}</td>
                <td>{{ $workload->subject->code ?? '' }}</td>
                <td>{{ $workload->group->name ?? '' }}</td>
                <td>{{ $workload->lecture_hours ?? 0 }}</td>
                <td>{{ $workload->seminar_hours ?? 0 }}</td>
                <td>{{ $workload->practical_hours ?? 0 }}</td>
                <td>{{ $workload->exam_hours ?? 0 }}</td>
                <td><strong>{{ $workload->total_hours ?? 0 }}</strong></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;"><strong>JAMI:</strong></td>
                <td><strong>{{ $stats['lecture_hours'] }}</strong></td>
                <td><strong>{{ $stats['seminar_hours'] }}</strong></td>
                <td><strong>{{ $stats['practical_hours'] }}</strong></td>                
                <td><strong>{{ $stats['exam_hours'] }}</strong></td>
                <td><strong>{{ $stats['total_hours'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Hisobot avtomatik tarzda {{ date('d.m.Y H:i') }} da yaratildi</p>
    </div>
</body>
</html>