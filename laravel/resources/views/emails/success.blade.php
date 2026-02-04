<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
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
            color: #22c55e;
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

        .qr-container {
            margin-bottom: 60px;
            text-align: left;
        }

        .qr-code {
            display: inline-block;
            padding: 12px;
            border: 1px solid #eeeeee;
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

        .footer {
            margin-top: 80px;
            padding-top: 40px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.8;
        }

        .link {
            color: #14a7a8;
            text-decoration: none;
            font-weight: 700;
        }

        .button {
            display: inline-block;
            background-color: #111827;
            color: #ffffff !important;
            text-align: center;
            padding: 20px 40px;
            border-radius: 0;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
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
    @php
        $subtotal = $transaction->ticket->price * $transaction->quantity;

        // Handling/Reseller Fee Logic
        $isReseller = $transaction->reseller_id ? true : false;
        $handlingFee = 0;

        if ($isReseller) {
            // Reseller Commission / Fee
            if ($transaction->event->reseller_fee_type === 'percent') {
                $handlingFee = ($transaction->ticket->price * ($transaction->event->reseller_fee_value / 100));
            } else {
                $handlingFee = (float) $transaction->event->reseller_fee_value;
            }
        } else {
            $handlingFee = (int) \App\Models\Setting::getValue('handling_fee', 0);
        }
    @endphp
    <div class="wrapper">
        <div style="margin-bottom: 40px;">
            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Logo" style="height: 32px; display: block;">
        </div>
        <div class="status-badge">Confirmed</div>

        <h1 class="title">You're in.</h1>

        <p class="intro-text">
            Hi {{ $transaction->name }},<br><br>
            Your payment is confirmed. Your tickets for <strong>{{ $transaction->event->name }}</strong> are now
            secured. We look forward to seeing you there.
        </p>

        <div class="section-title">Access Pass</div>

        <div class="qr-container">
            <div class="qr-code">
                <img src="{{ $message->embedData($qrString, 'qr-code.png', 'image/png') }}" width="160" height="160" style="display: block;">
            </div>
            <p
                style="font-size: 12px; color: rgba(0,0,0,0.4); margin-top: 16px; font-weight: 700; text-transform: uppercase;">
                Ticket #{{ $transaction->code }}</p>
        </div>

        <div class="section-title">Order Details</div>

        <div class="data-grid">
            <div class="data-row">
                <div class="label">Event</div>
                <div class="value">{{ $transaction->event->name }}</div>
            </div>
            <div class="data-row">
                <div class="label">Ticket</div>
                <div class="value">{{ $transaction->ticket->name }} &times; {{ $transaction->quantity }}</div>
            </div>
            @if($transaction->reseller_id)
                <div class="data-row">
                    <div class="label">Reseller</div>
                    <div class="value">{{ $transaction->reseller->name }}</div>
                </div>
            @endif

            @if($handlingFee * $transaction->quantity > 0)
                <div class="data-row">
                    <div class="label">Subtotal</div>
                    <div class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                </div>
                <div class="data-row">
                    <div class="label">{{ $transaction->reseller_id ? 'Reseller Fee' : 'Handling Fee' }}</div>
                    <div class="value">Rp {{ number_format($handlingFee * $transaction->quantity, 0, ',', '.') }}</div>
                </div>
            @endif

            <div class="data-row" style="border: none;">
                <div class="label">Amount Paid</div>
                <div class="total-amount">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
            </div>
        </div>

        <a href="{{ route('payment.success', $transaction->code) }}" class="button">View Online Ticket</a>

        <div class="footer">
            You can manage your tickets at <a href="{{ route('payment.success', $transaction->code) }}"
                class="link">anntix.com</a><br>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
