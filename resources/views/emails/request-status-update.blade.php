<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blood Request Status Update</title>
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
        .status-rejected { background-color: #dc3545; color: #fff; }
        .status-completed { background-color: #17a2b8; color: #fff; }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🩸 Blood Request Status Update</h1>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $user_name }}</strong>,</p>
        
        <p>Your blood request status has been updated by our administration team.</p>
        
        <div class="details">
            <h3>Request Details:</h3>
            <ul>
                <li><strong>Request ID:</strong> #{{ $request_id }}</li>
                <li><strong>Blood Type:</strong> {{ $blood_type }}</li>
                <li><strong>Units Needed:</strong> {{ $units_needed }}</li>
                <li><strong>Required Date:</strong> {{ \Carbon\Carbon::parse($request_date)->format('M d, Y') }}</li>
                <li><strong>Previous Status:</strong> <span class="status-badge status-{{ strtolower($old_status) }}">{{ ucfirst($old_status) }}</span></li>
                <li><strong>New Status:</strong> <span class="status-badge status-{{ strtolower($new_status) }}">{{ ucfirst($new_status) }}</span></li>
            </ul>
        </div>
        
        @if($admin_notes)
        <div class="details">
            <h3>Administrator Notes:</h3>
            <p><em>{{ $admin_notes }}</em></p>
        </div>
        @endif
        
        <div class="details">
            <h3>What This Means:</h3>
            @if($new_status === 'approved')
                <p>✅ Your blood request has been approved! Our team will now work on allocating the required blood units for you.</p>
                <p>You will receive another notification once the blood is ready for collection.</p>
            @elseif($new_status === 'rejected')
                <p>❌ Your blood request has been rejected. Please review the administrator notes above for more details.</p>
                <p>If you have any questions, please contact our support team.</p>
            @elseif($new_status === 'completed')
                <p>🎉 Your blood request has been completed successfully! Thank you for using our services.</p>
            @else
                <p>Your blood request is currently being processed. We will keep you updated on any further changes.</p>
            @endif
        </div>
        
        <p>Thank you for choosing Life Vault for your blood management needs.</p>
        
        <p>Best regards,<br>
        <strong>The Life Vault Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
