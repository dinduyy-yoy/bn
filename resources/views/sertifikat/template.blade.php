<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
            padding: 0px;
            background-image: url('{{ $bgImage }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            width: 100%;
            height: 100%;
            font-family: sans-serif;
            position: relative;
        }
        .nama-peserta {
            position: absolute;
            left: {{ $config['x'] ?? '50%' }};
            top: {{ $config['y'] ?? '50%' }};
            transform: translate(-50%, -50%);
            font-size: {{ $config['fontSize'] ?? '30px' }};
            color: {{ $config['color'] ?? '#000000' }};
            text-align: {{ $config['align'] ?? 'center' }};
            font-weight: bold;
            width: 100%;
            margin: 0;
            padding: 0;
            /* DomPDF has some limitations with transform: translate. 
               If it doesn't work well, we might need to adjust using margins. 
               But absolute positioning usually works ok if width is 100% and text-align is center for x=50%.
               Since we want custom x, y, we'll try this approach or inline styles.
            */
        }
    </style>
</head>
<body>
    <div class="nama-peserta" style="
        position: absolute; 
        left: {{ $config['x'] ?? '0%' }}; 
        top: {{ $config['y'] ?? '50%' }}; 
        font-size: {{ $config['fontSize'] ?? '30px' }}; 
        color: {{ $config['color'] ?? '#000000' }}; 
        text-align: {{ $config['align'] ?? 'center' }};
        width: 100%;
    ">
        {{ $namaPeserta }}
    </div>
</body>
</html>
