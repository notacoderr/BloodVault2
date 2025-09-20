<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Confirmed</title>
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
            background-color: #28a745;
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
            border-left: 4px solid #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Appointment Confirmed</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $user_name }}</strong>,</p>
        
        <div class="success">
            <strong>Great news!</strong> Your appointment has been confirmed and is now scheduled.
        </div>
        
        <div class="details">
            <h3>Appointment Details:</h3>
            <ul>
                <li><strong>Appointment ID:</strong> #{{ $appointment_id }}</li>
                <li><strong>Type:</strong> {{ ucfirst($appointment_type) }}</li>
                <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment_date)->format('M d, Y') }}</li>
                <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($time_slot)->format('g:i A') }}</li>
                <li><strong>Status:</strong> <span style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-weight: bold;">CONFIRMED</span></li>
            </ul>
        </div>
        
        <div class="details">
            <h3>Important Reminders:</h3>
            <ul>
                <li><strong>Arrive 10 minutes early</strong> to complete any necessary paperwork</li>
                <li><strong>Bring a valid ID</strong> (driver's license, passport, etc.)</li>
                <li><strong>Bring any relevant medical documents</strong> or referral letters</li>
                <li><strong>List of current medications</strong> if applicable</li>
                <li><strong>Insurance information</strong> if you have coverage</li>
            </ul>
        </div>
        
        <div class="details">
            <h3>What to Expect:</h3>
            <p>When you arrive:</p>
            <ol>
                <li>Check in at the reception desk</li>
                <li>Complete any required forms</li>
                <li>Wait for your name to be called</li>
                <li>Meet with our healthcare professional</li>
            </ol>
        </div>
        
        <div class="details">
            <h3>Need to Reschedule?</h3>
            <p>If you need to change your appointment time, please contact us at least 24 hours in advance. This helps us accommodate other patients who may be waiting for appointments.</p>
        </div>
        
        <p>We look forward to seeing you!</p>
        
        <p>Best regards,<br>
        <strong>The Life Vault Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated confirmation. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
