<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blood Donation Status Update</title>
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
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #28a745; color: #fff; }
        .status-completed { background-color: #17a2b8; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>❤️ Blood Donation Status Update</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $user_name }}</strong>,</p>
        
        <p>Your blood donation status has been updated by our medical team.</p>
        
        <div class="details">
            <h3>Donation Details:</h3>
            <ul>
                <li><strong>Donation ID:</strong> #{{ $donation_id }}</li>
                <li><strong>Blood Type:</strong> {{ $blood_type }}</li>
                <li><strong>Donation Date:</strong> {{ \Carbon\Carbon::parse($donation_date)->format('M d, Y') }}</li>
                <li><strong>Previous Status:</strong> <span class="status-badge status-{{ strtolower($old_status) }}">{{ ucfirst($old_status) }}</span></li>
                <li><strong>New Status:</strong> <span class="status-badge status-{{ strtolower($new_status) }}">{{ ucfirst($new_status) }}</span></li>
            </ul>
        </div>
        
        @if($notes)
        <div class="details">
            <h3>Medical Team Notes:</h3>
            <p><em>{{ $notes }}</em></p>
        </div>
        @endif
        
        <div class="details">
            <h3>What This Means:</h3>
            @if($new_status === 'approved')
                <p>✅ Your blood donation has been approved! You can now proceed with your donation on the scheduled date.</p>
                <p>Please ensure you:</p>
                <ul>
                    <li>Get adequate rest the night before</li>
                    <li>Eat a healthy meal 2-3 hours before donation</li>
                    <li>Stay well hydrated</li>
                    <li>Bring a valid ID</li>
                </ul>
            @elseif($new_status === 'rejected')
                <p>❌ Your blood donation has been rejected. Please review the medical team notes above for more details.</p>
                <p>This decision is made based on medical screening results and safety protocols. If you have any questions, please contact our medical team.</p>
            @elseif($new_status === 'completed')
                <p>🎉 Your blood donation has been completed successfully! Thank you for your life-saving contribution.</p>
                <p>Your donation will help save lives in our community.</p>
            @else
                <p>Your blood donation is currently being processed. We will keep you updated on any further changes.</p>
            @endif
        </div>
        
        <p>Thank you for your commitment to saving lives through blood donation.</p>
        
        <p>Best regards,<br>
        <strong>The Life Vault Medical Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our medical team.</p>
    </div>
</body>
</html>
