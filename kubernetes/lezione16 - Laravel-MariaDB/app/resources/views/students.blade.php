<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lezione 16 - Laravel e MariaDB</title>
    <style>body{font-family:system-ui,sans-serif;max-width:800px;margin:3rem auto;padding:0 1rem;color:#243447}table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccd6dd;padding:.7rem;text-align:left}th{background:#eef4f7}.ok{color:#167447}</style>
</head>
<body>
    <h1>Studenti</h1>
    <p class="ok">Dati letti da MariaDB tramite Laravel.</p>
    <table><thead><tr><th>Nome</th><th>Corso</th></tr></thead><tbody>
    @forelse ($students as $student)
        <tr><td>{{ $student->name }}</td><td>{{ $student->course }}</td></tr>
    @empty
        <tr><td colspan="2">Nessun dato disponibile.</td></tr>
    @endforelse
    </tbody></table>
</body>
</html>
