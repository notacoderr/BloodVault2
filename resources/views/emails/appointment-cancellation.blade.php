<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Cancelled</title>
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
            background-color: #6c757d;
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
            border-left: 4px solid #6c757d;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚫 Appointment Cancelled</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $user_name }}</strong>,</p>
        
        <div class="info">
            <strong>Notice:</strong> Your appointment has been cancelled.
        </div>
        
        <div class="details">
            <h3>Cancelled Appointment Details:</h3>
            <ul>
                <li><strong>Appointment ID:</strong> #{{ $appointment_id }}</li>
                <li><strong>Type:</strong> {{ ucfirst($appointment_type) }}</li>
                <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment_date)->format('M d, Y') }}</li>
                <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($time_slot)->format('g:i A') }}</li>
                <li><strong>Status:</strong> <span style="background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 15px; font-weight: bold;">CANCELLED</span></li>
            </ul>
        </div>
        
        @if($reason)
        <div class="details">
            <h3>Reason for Cancellation:</h3>
            <p><em>{{ $reason }}</em></p>
        </div>
        @endif
        
        <div class="details">
            <h3>Next Steps:</h3>
            <p>If you would like to reschedule your appointment:</p>
            <ol>
                <li>Visit our website or contact our team</li>
                <li>Choose a new date and time that works for you</li>
                <li>We'll confirm your new appointment</li>
            </ol>
        </div>
        
        <div class="details">
            <h3>Alternative Options:</h3>
            <ul>
                <li><strong>Reschedule:</strong> Book a new appointment at your convenience</li>
                <li><strong>Different Time:</strong> Choose from available time slots</li>
                <li><strong>Different Date:</strong> Select a date that works better for you</li>
                <li><strong>Contact Us:</strong> Speak with our team for assistance</li>
            </ul>
        </div>
        
        <div class="details">
            <h3>Need Help?</h3>
            <p>Our team is here to help you find the best solution. If you have any questions or need assistance with rescheduling, please don't hesitate to contact us.</p>
        </div>
        
        <p>We apologize for any inconvenience and look forward to serving you in the future.</p>
        
        <p>Best regards,<br>
        <strong>The Life Vault Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
