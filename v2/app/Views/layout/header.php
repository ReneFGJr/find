<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'FIND'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/css/style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" href="<?= base_url('/favicon.ico'); ?>" sizes="any" />
    <link rel="icon" href="<?= base_url('/assets/favicon.ico'); ?>" sizes="48x48" />
    <link rel="icon" href="<?= base_url('/assets/favicon.ico'); ?>" type="image/svg+xml" />
    <link rel="icon" href="<?= base_url('/assets/favicon.ico'); ?>" type="image/svg+xml" media="(prefers-color-scheme: dark)" />
    <style>
        /* Dark mode para offcanvas Bootstrap */
        body.dark-mode .offcanvas {
            background-color: #23272b !important;
            color: #f1f1f1 !important;
            border-left: 1px solid #333a40;
        }

        body.dark-mode .offcanvas-header {
            background-color: #23272b !important;
            color: #f1f1f1 !important;
            border-bottom: 1px solid #333a40;
        }

        body.dark-mode .offcanvas-title {
            color: #f1f1f1 !important;
        }

        body.dark-mode .offcanvas-body {
            background-color: #23272b !important;
            color: #f1f1f1 !important;
        }

        body.dark-mode .btn-close {
            filter: invert(1) grayscale(1);
        }

        body.dark-mode .form-label {
            color: #f1f1f1 !important;
        }

        /* Dark mode para tabelas Bootstrap */
        body.dark-mode table.table {
            background-color: #23272b;
            color: #f1f1f1;
        }

        body.dark-mode table.table th,
        body.dark-mode table.table td {
            background-color: #23272b;
            color: #f1f1f1;
            border-color: #333a40;
        }

        body.dark-mode .table-light th,
        body.dark-mode .table-light td,
        body.dark-mode .table-light {
            background-color: #181a1b !important;
            color: #f1f1f1 !important;
            border-color: #333a40 !important;
        }

        body.dark-mode .table-secondary {
            background-color: #343a40 !important;
            color: #f1f1f1 !important;
        }

        body.dark-mode .table-bordered {
            border-color: #333a40 !important;
        }

        body.dark-mode pre {
            background: #181a1b;
            color: #f1f1f1;
            border: 1px solid #333a40;
        }

        :root {
            --find-primary: #0d6efd;
            --find-dark: #0f172a;
            --find-soft: #eef4ff;
            --find-bg-dark: #181a1b;
            --find-bg-dark-2: #23272b;
            --find-text-dark: #f1f1f1;
            --find-border-dark: #333a40;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
            color: #1f2937;
            transition: background 0.3s, color 0.3s;
        }

        body.dark-mode {
            background: var(--find-bg-dark) !important;
            color: var(--find-text-dark) !important;
        }

        body.dark-mode .navbar,
        body.dark-mode .dropdown-menu {
            background-color: var(--find-bg-dark-2) !important;
            color: var(--find-text-dark) !important;
        }

        body.dark-mode .navbar .nav-link,
        body.dark-mode .dropdown-item {
            color: var(--find-text-dark) !important;
        }

        body.dark-mode .navbar-brand img,
        body.dark-mode .logo-card img {
            filter: brightness(0.85) contrast(1.2);
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: var(--find-bg-dark-2) !important;
            color: var(--find-text-dark) !important;
            border-color: var(--find-border-dark) !important;
        }

        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            background-color: #23272b !important;
            color: var(--find-text-dark) !important;
        }

        body.dark-mode .btn-outline-light {
            color: #fff;
            border-color: #888;
        }

        body.dark-mode .btn-outline-light:hover {
            background: #333a40;
            color: #fff;
        }

        body.dark-mode .bg-white,
        body.dark-mode .border-top {
            background: var(--find-bg-dark-2) !important;
            border-color: var(--find-border-dark) !important;
        }

        body.dark-mode .text-muted {
            color: #b0b0b0 !important;
        }

        /* Dark mode para cards Bootstrap */
        body.dark-mode .card,
        body.dark-mode .card-header,
        body.dark-mode .card-body {
            background-color: #23272b !important;
            color: #f1f1f1 !important;
            border-color: #333a40 !important;
        }

        body.dark-mode .card-header {
            border-bottom: 1px solid #333a40 !important;
        }

        body.dark-mode .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        }

        body.dark-mode .btn-outline-primary {
            color: #90caf9 !important;
            border-color: #90caf9 !important;
        }

        body.dark-mode .btn-outline-primary:hover {
            background: #1565c0 !important;
            color: #fff !important;
            border-color: #1565c0 !important;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(13, 110, 253, .08), rgba(25, 135, 84, .08));
            border: 1px solid rgba(13, 110, 253, .08);
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
            min-height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .logo-card img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
        }

        .feature-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .nav-user-pill {
            background: rgba(255, 255, 255, .08);
            border-radius: 999px;
            padding-inline: .85rem !important;
        }

        .auth-card {
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .form-control,
        .form-select {
            border: 1px solid #000;
            background-color: #F8F8F8;
            transition: background-color .2s, border-color .2s;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #FFFFFF;
            border-color: #000;
            box-shadow: 0 0 0 .15rem rgba(0, 0, 0, .15);
        }
    </style>
</head>

<body>
    <script>
        // Dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('darkModeToggle');
            const icon = document.getElementById('darkModeIcon');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            let dark = localStorage.getItem('find_dark_mode');
            if (dark === null) {
                dark = prefersDark ? '1' : '0';
            }

            function setDarkMode(on) {
                document.body.classList.toggle('dark-mode', on);
                if (icon) icon.className = on ? 'bi bi-sun' : 'bi bi-moon';
                localStorage.setItem('find_dark_mode', on ? '1' : '0');
            }
            setDarkMode(dark === '1');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    const isDark = document.body.classList.toggle('dark-mode');
                    if (icon) icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon';
                    localStorage.setItem('find_dark_mode', isDark ? '1' : '0');
                });
            }
        });
    </script>