<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Booking Receipt - {{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #111418;
            background: #fff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #E3655B;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #E3655B;
            letter-spacing: 2px;
        }
        .logo-subtitle {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .receipt-title {
            font-size: 18px;
            color: #111418;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-number {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #E3655B;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .label {
            display: table-cell;
            width: 40%;
            color: #666;
            font-size: 11px;
        }
        .value {
            display: table-cell;
            width: 60%;
            font-weight: 500;
            text-align: right;
        }
        .vehicle-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .vehicle-name {
            font-size: 16px;
            font-weight: bold;
            color: #111418;
        }
        .vehicle-details {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-table .label-cell {
            color: #666;
        }
        .summary-table .value-cell {
            text-align: right;
            font-weight: 500;
        }
        .total-row td {
            border-bottom: none;
            border-top: 2px solid #111418;
            font-size: 14px;
            font-weight: bold;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background: #9CBF9B;
            color: #fff;
        }
        .status-pending {
            background: #CFD186;
            color: #111418;
        }
        .status-cancelled {
            background: #E3655B;
            color: #fff;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .footer-links {
            margin-top: 10px;
        }
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .confirmation-code {
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #E3655B;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">CARTAR</div>
            <div class="logo-subtitle">Premium Car Rentals</div>
            <div class="receipt-title">Booking Receipt</div>
            <div class="receipt-number">Receipt #{{ str_pad($booking->id, 8, '0', STR_PAD_LEFT) }}</div>
        </div>

        <!-- Booking Status -->
        <div class="section" style="text-align: center; margin-bottom: 30px;">
            @if($booking->status->value === 'confirmed')
                <span class="status-badge status-confirmed">CONFIRMED</span>
            @elseif($booking->status->value === 'pending')
                <span class="status-badge status-pending">PENDING PAYMENT</span>
            @elseif($booking->status->value === 'cancelled')
                <span class="status-badge status-cancelled">CANCELLED</span>
            @else
                <span class="status-badge" style="background: #666; color: #fff;">{{ strtoupper($booking->status->value) }}</span>
            @endif
        </div>

        <!-- Customer Info -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="row">
                <span class="label">Name</span>
                <span class="value">{{ $booking->user->name }}</span>
            </div>
            <div class="row">
                <span class="label">Email</span>
                <span class="value">{{ $booking->user->email }}</span>
            </div>
            @if($booking->user->phone)
            <div class="row">
                <span class="label">Phone</span>
                <span class="value">{{ $booking->user->phone }}</span>
            </div>
            @endif
        </div>

        <!-- Vehicle Info -->
        <div class="section">
            <div class="section-title">Vehicle Details</div>
            <div class="vehicle-info">
                <div class="vehicle-name">{{ $booking->vehicle->year }} {{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</div>
                <div class="vehicle-details">
                    {{ $booking->vehicle->transmission->value }} • {{ $booking->vehicle->fuel_type->value }} • {{ $booking->vehicle->seats }} Seats
                </div>
            </div>
        </div>

        <!-- Rental Period -->
        <div class="section">
            <div class="section-title">Rental Period</div>
            <div class="row">
                <span class="label">Pick-up Date</span>
                <span class="value">{{ $booking->start_time->format('D, M j, Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Pick-up Time</span>
                <span class="value">{{ $booking->start_time->format('g:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Return Date</span>
                <span class="value">{{ $booking->end_time->format('D, M j, Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Return Time</span>
                <span class="value">{{ $booking->end_time->format('g:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Duration</span>
                <span class="value">{{ max(1, $booking->start_time->diffInDays($booking->end_time)) }} Days</span>
            </div>
            @if($booking->pickup_location)
            <div class="row">
                <span class="label">Pick-up Location</span>
                <span class="value">{{ $booking->pickup_location }}</span>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="section">
            <div class="section-title">Payment Summary</div>
            <table class="summary-table">
                <tr>
                    <td class="label-cell">Daily Rate</td>
                    <td class="value-cell">₦{{ number_format($booking->vehicle->daily_rate, 2) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Duration ({{ max(1, $booking->start_time->diffInDays($booking->end_time)) }} days)</td>
                    <td class="value-cell">₦{{ number_format($booking->vehicle->daily_rate * max(1, $booking->start_time->diffInDays($booking->end_time)), 2) }}</td>
                </tr>
                @if($booking->discount_amount > 0)
                <tr>
                    <td class="label-cell">Discount</td>
                    <td class="value-cell" style="color: #9CBF9B;">-₦{{ number_format($booking->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label-cell">Total Amount</td>
                    <td class="value-cell">₦{{ number_format($booking->total_price, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Confirmation Code -->
        <div class="qr-section">
            <div style="font-size: 10px; color: #666; margin-bottom: 10px;">CONFIRMATION CODE</div>
            <div class="confirmation-code">{{ strtoupper(substr(md5($booking->id . $booking->created_at), 0, 8)) }}</div>
            <div style="font-size: 9px; color: #999; margin-top: 10px;">Present this code at pickup</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing CARTAR!</p>
            <p style="margin-top: 5px;">This receipt was generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <div class="footer-links">
                <p>support@cartar.com | +234 800 123 4567</p>
            </div>
        </div>
    </div>
</body>
</html>
