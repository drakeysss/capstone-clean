<!DOCTYPE html>
<html>
<head>
    <title>Pre-order Deadline Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pre-order Deadline Reminder</h1>
        </div>
        <div class="content">
            <p>Dear Student,</p>
            
            <p>This is a friendly reminder that the deadline for pre-ordering your <strong>{{ ucfirst($mealType) }}</strong> meal is approaching.</p>
            
            <p>You have <strong>{{ $minutesLeft }} minutes</strong> left to submit your pre-order.</p>
            
            <p>Please click the button below to submit your pre-order now:</p>
            
            <div style="text-align: center;">
                <a href="{{ route('student.pre-order') }}" class="button">Submit Pre-order</a>
            </div>
            
            <p>If you don't submit a pre-order, you may not be guaranteed a meal for this service.</p>
            
            <p>Thank you for helping us reduce food waste!</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Kitchen Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 