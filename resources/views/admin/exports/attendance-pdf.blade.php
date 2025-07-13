<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Absensi</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
  </style>
</head>
<body>
  <h2>Laporan Absensi - {{ now()->format('d/m/Y') }}</h2>
  <table>
    <thead>
      <tr>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Status</th>
        <th>Latitude</th>
        <th>Longitude</th>
      </tr>
    </thead>
    <tbody>
      @foreach($attendances as $a)
      <tr>
        <td>{{ $a->user->name }}</td>
        <td>{{ $a->date }}</td>
        <td>{{ $a->check_in ?? '-' }}</td>
        <td>{{ $a->check_out ?? '-' }}</td>
        <td>{{ ucfirst($a->status) }}</td>
        <td>{{ $a->latitude }}</td>
        <td>{{ $a->longitude }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
