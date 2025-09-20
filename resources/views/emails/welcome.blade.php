<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Life Vault!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #dc3545;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .title {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .highlight {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .features {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🩸 Life Vault</div>
            <div class="title">Welcome to Life Vault!</div>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <div class="highlight">
                <strong>🎉 Congratulations!</strong> Your email has been successfully verified. You're now a full member of the Life Vault community!
            </div>
            
            <p>Welcome to <strong>Life Vault</strong>, where we connect blood donors with those in need. Your account is now fully activated and you can access all features of our platform.</p>
            
            <div class="features">
                <h3>🚀 What You Can Do Now:</h3>
                <ul>
                    <li><strong>Request Blood:</strong> Submit blood requests for yourself or loved ones</li>
                    <li><strong>Donate Blood:</strong> Register as a blood donor and save lives</li>
                    <li><strong>Book Appointments:</strong> Schedule blood donation or screening appointments</li>
                    <li><strong>Manage Profile:</strong> Update your personal information and preferences</li>
                    <li><strong>Track Requests:</strong> Monitor the status of your blood requests</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="button">Access Your Dashboard</a>
            </div>
            
            <p><strong>Quick Start Tips:</strong></p>
            <ul>
                <li>Complete your profile with accurate information</li>
                <li>Set your blood type and contact preferences</li>
                <li>Explore the different services available</li>
                <li>Read our safety guidelines before donating</li>
            </ul>
            
            <p>If you have any questions or need assistance, don't hesitate to contact our support team.</p>
        </div>

        <div class="footer">
            <p>Thank you for joining Life Vault and helping us save lives!</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Life Vault. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
