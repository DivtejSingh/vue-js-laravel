<!DOCTYPE html>
<html>
<head>
    <title>New Business Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Business Registered</h1>
        <p><strong>Business Name:</strong> {{ $business->name }}</p>
        <p><strong>Email:</strong> {{ $business->email }}</p>
        <p><strong>Phone:</strong> {{ $business->mobile_phone }}</p>
        {{-- <p><strong>Address:</strong> {{ $profile->address }}</p> --}}
        {{-- <p>Please review and approve the business from your admin dashboard.</p> --}}
        {{-- <a href="{{ $adminDashboardUrl }}" class="btn">Go to Dashboard</a> --}}
        {{-- <div class="footer">
            &copy; 2025 Laravel. All rights reserved.
        </div> --}}
    </div>
</body>
</html>
