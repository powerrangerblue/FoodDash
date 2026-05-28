<?php
    $transactions = $transactions ?? [];
    $summaryCards = $summaryCards ?? [];
    $selectedSummary = $selectedSummary ?? ['revenue' => 0, 'orders' => 0];
    $restaurantName = (string) ($restaurantName ?? 'Restaurant');
    $generatedAt = (string) ($generatedAt ?? date('M d, Y h:i A'));
    $selectedRangeLabel = (string) ($selectedRangeLabel ?? 'This Month');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 24px;
            font-size: 12px;
        }

        .header {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #1C4B4A;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .muted {
            color: #6b7280;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .meta-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            min-width: 190px;
        }

        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 12px;
        }

        .card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            vertical-align: top;
            width: 25%;
        }

        .card h3 {
            margin: 6px 0 4px;
            font-size: 18px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .report th,
        .report td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        .report thead th {
            background: #1C4B4A;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Sales Report</div>
        <div class="muted"><?= htmlspecialchars($restaurantName, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div class="meta">
        <div class="meta-box">
            <strong>Date Generated</strong><br>
            <span><?= htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="meta-box">
            <strong>Selected Date Range</strong><br>
            <span><?= htmlspecialchars($selectedRangeLabel, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="meta-box">
            <strong>Total Sales</strong><br>
            <span>₱<?= number_format((float) ($selectedSummary['revenue'] ?? 0), 2) ?></span>
        </div>
        <div class="meta-box">
            <strong>Total Orders</strong><br>
            <span><?= (int) ($selectedSummary['orders'] ?? 0) ?></span>
        </div>
    </div>

    <table class="cards">
        <tr>
            <td class="card">
                <div class="muted">Daily Sales</div>
                <h3>₱<?= number_format((float) (($summaryCards['daily']['revenue'] ?? 0)), 2) ?></h3>
                <div><?= (int) ($summaryCards['daily']['orders'] ?? 0) ?> orders</div>
            </td>
            <td class="card">
                <div class="muted">Weekly Sales</div>
                <h3>₱<?= number_format((float) (($summaryCards['weekly']['revenue'] ?? 0)), 2) ?></h3>
                <div><?= (int) ($summaryCards['weekly']['orders'] ?? 0) ?> orders</div>
            </td>
            <td class="card">
                <div class="muted">Monthly Sales</div>
                <h3>₱<?= number_format((float) (($summaryCards['monthly']['revenue'] ?? 0)), 2) ?></h3>
                <div><?= (int) ($summaryCards['monthly']['orders'] ?? 0) ?> orders</div>
            </td>
            <td class="card">
                <div class="muted">Lifetime Sales</div>
                <h3>₱<?= number_format((float) (($summaryCards['lifetime']['revenue'] ?? 0)), 2) ?></h3>
                <div><?= (int) ($summaryCards['lifetime']['orders'] ?? 0) ?> orders</div>
            </td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($transaction['order_number'] ?? $transaction['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($transaction['customer_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= ! empty($transaction['created_at']) ? htmlspecialchars(date('M d, Y h:i A', strtotime((string) $transaction['created_at'])), ENT_QUOTES, 'UTF-8') : '-' ?></td>
                        <td><?= htmlspecialchars((string) ($transaction['payment_method'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>₱<?= number_format((float) ($transaction['total_amount'] ?? 0), 2) ?></td>
                        <td>Delivered</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:18px;">No delivered sales found for the selected range.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>