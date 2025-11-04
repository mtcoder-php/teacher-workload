<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fakultet Hisoboti</title>
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
            border-bottom: 2px solid #8B5CF6;
        }
        .header h1 {
            color: #8B5CF6;
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
            background-color: #8B5CF6;
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
            background-color: #EDE9FE !important;
            font-weight: bold;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 33.33%;
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
            color: #8B5CF6;
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
        <h1>FAKULTET YUKLAMA HISOBOTI</h1>
        <p>{{ date('d.m.Y H:i') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Fakultet:</td>
            <td>{{ $faculty->name }}</td>
        </tr>
        <tr>
            <td>Kod:</td>
            <td>{{ $faculty->code ?? '—' }}</td>
        </tr>
        <tr>
            <td>Dekan:</td>
            <td>{{ $faculty->dean ? $faculty->dean->name : '—' }}</td>
        </tr>
        <tr>
            <td>Semestr:</td>
            <td>{{ $semester ?? 'Joriy semestr' }}</td>
        </tr>
    </table>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Kafedralar</div>
            <div class="value">{{ $totalStats['departments_count'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">O'qituvchilar</div>
            <div class="value">{{ $totalStats['teachers_count'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Jami soatlar</div>
            <div class="value">{{ $totalStats['total_hours'] }}</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">№</th>
                <th style="width: 35%;">Kafedra</th>
                <th style="width: 25%;">Mudiri</th>
                <th style="width: 12%;">O'qituvchilar</th>
                <th style="width: 11%;">Jami soat</th>
                <th style="width: 12%;">O'rtacha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departmentStats as $index => $stat)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $stat['department']['name'] }}</td>
                <td>{{ $stat['department']['head'] ?? '—' }}</td>
                <td>{{ $stat['teachers_count'] }}</td>
                <td><strong>{{ $stat['total_hours'] }}</strong></td>
                <td>{{ $stat['average_hours'] }}</td>
            </tr>
            @endforeach>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>JAMI:</strong></td>
                <td><strong>{{ $totalStats['teachers_count'] }}</strong></td>
                <td><strong>{{ $totalStats['total_hours'] }}</strong></td>
                <td>—</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Hisobot avtomatik tarzda {{ date('d.m.Y H:i') }} da yaratildi</p>
    </div>
</body>
</html>