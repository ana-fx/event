<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Required</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .status-badge {
            display: inline-block;
            color: #14a7a8;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 24px;
        }

        .title {
            font-size: 48px;
            font-weight: 900;
            color: #111827;
            margin: 0 0 40px 0;
            line-height: 1;
            letter-spacing: -0.05em;
        }

        .intro-text {
            font-size: 18px;
            color: #111827;
            line-height: 1.6;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(0, 0, 0, 0.3);
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eeeeee;
        }

        .data-grid {
            margin-bottom: 60px;
        }

        .data-row {
            padding: 16px 0;
            border-bottom: 1px solid #f9f9f9;
        }

        .label {
            font-size: 12px;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .value {
            font-size: 16px;
            color: #111827;
            font-weight: 600;
        }

        .total-amount {
            font-size: 32px;
            font-weight: 900;
            color: #14a7a8;
            margin-top: 8px;
            letter-spacing: -0.02em;
        }

        .button {
            display: inline-block;
            background-color: #111827;
            color: #ffffff !important;
            text-align: center;
            padding: 20px 40px;
            border-radius: 0;
            /* Sharp edges for minimalist feel */
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .footer {
            margin-top: 80px;
            padding-top: 40px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.8;
        }

        @media only screen and (max-width: 600px) {
            .title {
                font-size: 36px;
            }

            .intro-text {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div style="margin-bottom: 40px;">
            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Logo" style="height: 32px; display: block;">
        </div>
        <div class="status-badge">Payment Required</div>

        <h1 class="title">Complete<br>Your Order.</h1>

        <p class="intro-text">
            Hi {{ $transaction->name }},<br><br>
            Your selection for <strong>{{ $transaction->event->name }}</strong> has been reserved. Please complete your
            payment to finalize your registration.
        </p>

        <div class="section-title">Order Details</div>

        <div class="data-grid">
            <div class="data-row">
                <div class="label">Reference</div>
                <div class="value">{{ $transaction->code }}</div>
            </div>
            <div class="data-row">
                <div class="label">Ticket</div>
                <div class="value">{{ $transaction->ticket->name }} &times; {{ $transaction->quantity }}</div>
            </div>
            <div class="data-row" style="border: none;">
                <div class="label">Total Amount</div>
                <div class="total-amount">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
            </div>
        </div>

        <a href="{{ route('payment.show', $transaction->code) }}" class="button">Pay Now</a>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            This is an automated message regarding your registration.
        </div>
    </div>
</body>

</html>
