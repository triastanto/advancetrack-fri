<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '{{ config("app.name") }} Notification')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .info-section.success {
            border-left-color: #28a745;
        }
        .info-section.warning {
            border-left-color: #ffc107;
        }
        .info-section.danger {
            border-left-color: #dc3545;
        }
        .info-section h3, .info-section h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: flex-start;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
            margin-right: 10px;
        }
        .info-value {
            color: #6c757d;
            flex: 1;
        }
        .highlight-section {
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        .highlight-section.success {
            background-color: #e8f5e8;
            border-color: #28a745;
        }
        .highlight-section.warning {
            background-color: #fff3cd;
            border-color: #ffc107;
        }
        .highlight-section.danger {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        .highlight-section h4 {
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .highlight-section.success h4 {
            color: #155724;
        }
        .highlight-section.warning h4 {
            color: #856404;
        }
        .highlight-section.danger h4 {
            color: #721c24;
        }
        .comment-box {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin-top: 10px;
            font-style: italic;
            color: #495057;
        }
        .action-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .action-list li {
            margin-bottom: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 15px 0;
            text-align: center;
        }
        .button-primary {
            background-color: #007bff;
            color: white;
        }
        .button-primary:hover {
            background-color: #0056b3;
        }
        .button-success {
            background-color: #28a745;
            color: white;
        }
        .button-success:hover {
            background-color: #218838;
        }
        .button-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .button-warning:hover {
            background-color: #e0a800;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        @include('emails.partials.header')
        
        <div class="content">
            @yield('content')
        </div>

        @include('emails.partials.footer')
    </div>
    
    @stack('scripts')
</body>
</html>
