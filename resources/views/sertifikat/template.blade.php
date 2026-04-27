<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @page {
            margin: 0px;
        }
        html, body {
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
        }
        body {
            background-image: url('{{ $bgImage }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            font-family: sans-serif;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="nama-peserta" style="
        position: absolute; 
        left: {{ $config['x'] ?? '50%' }}; 
        top: {{ $config['y'] ?? '50%' }}; 
        font-size: {{ $config['fontSize'] ?? '30px' }}; 
        color: {{ $config['color'] ?? '#000000' }}; 
        text-align: {{ $config['align'] ?? 'center' }};
        width: 100%;
        white-space: nowrap;
    ">
        {{ $namaPeserta }}
    </div>
</body>
</html>
