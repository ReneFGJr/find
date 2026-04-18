<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'FIND'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --find-primary: #0d6efd;
            --find-dark: #0f172a;
            --find-soft: #eef4ff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
            color: #1f2937;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(13,110,253,.08), rgba(25,135,84,.08));
            border: 1px solid rgba(13,110,253,.08);
            border-radius: 1.5rem;
        }

        .hero-logo {
            max-width: 220px;
            width: 100%;
            object-fit: contain;
        }

        .logo-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            min-height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .logo-card img {
            max-width: 100%;
            max-height: 54px;
            object-fit: contain;
        }

        .feature-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .nav-user-pill {
            background: rgba(255,255,255,.08);
            border-radius: 999px;
            padding-inline: .85rem !important;
        }

        .auth-card {
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .form-control, .form-select {
            border: 1px solid #000;
            background-color: #F8F8F8;
            transition: background-color .2s, border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            background-color: #FFFFFF;
            border-color: #000;
            box-shadow: 0 0 0 .15rem rgba(0, 0, 0, .15);
        }
    </style>
</head>
<body>