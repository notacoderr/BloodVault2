<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Appointment Rejected</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $user_name }}</strong>,</p>
        
        <div class="alert">
            <strong>Important:</strong> Your appointment request has been rejected by our administration team.
        </div>
        
        <div class="details">
            <h3>Appointment Details:</h3>
            <ul>
                <li><strong>Appointment ID:</strong> #{{ $appointment_id }}</li>
                <li><strong>Type:</strong> {{ ucfirst($appointment_type) }}</li>
                <li><strong>Requested Date:</strong> {{ \Carbon\Carbon::parse($appointment_date)->format('M d, Y') }}</li>
                <li><strong>Requested Time:</strong> {{ \Carbon\Carbon::parse($time_slot)->format('g:i A') }}</li>
            </ul>
        </div>
        
        @if($admin_notes)
        <div class="details">
            <h3>Reason for Rejection:</h3>
            <p><em>{{ $admin_notes }}</em></p>
        </div>
        @endif
        
        <div class="details">
            <h3>Next Steps:</h3>
            <p>If you believe this rejection was made in error, or if you have additional information that might change this decision, please:</p>
            <ol>
                <li>Review the reason for rejection above</li>
                <li>Contact our support team for clarification</li>
                <li>Provide any additional documentation if requested</li>
                <li>Consider booking a new appointment with different parameters</li>
            </ol>
        </div>
        
        <div class="details">
            <h3>Alternative Options:</h3>
            <ul>
                <li>Book a different appointment time</li>
                <li>Choose a different appointment type</li>
                <li>Contact us for a consultation to discuss your needs</li>
                <li>Check our FAQ section for common questions</li>
            </ul>
        </div>
        
        <p>We apologize for any inconvenience this may cause. Our team is here to help you find the best solution for your healthcare needs.</p>
        
        <p>Best regards,<br>
        <strong>The Life Vault Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
