<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$debt_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get debt details
$stmt = $conn->prepare("
    SELECT d.*, 
           COALESCE(SUM(dp.amount), 0) as total_paid,
           DATEDIFF(CURDATE(), due_date) as days_overdue,
           CASE 
               WHEN CURDATE() > due_date AND d.interest_rate > 0 THEN d.amount * (d.interest_rate / 100)
               ELSE 0
           END as interest_amount
    FROM debts d
    LEFT JOIN debt_payments dp ON d.id = dp.debt_id
    WHERE d.id = ? AND d.user_id = ?
    GROUP BY d.id
");
$stmt->bind_param("ii", $debt_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$debt = $result->fetch_assoc();

if (!$debt) {
    header("Location: debts.php");
    exit;
}

$remaining_amount = $debt['amount'] - $debt['total_paid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment - Finance Tracker</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper d-flex">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col">
                            <h2 class="content-title">Add Payment</h2>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="summary-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($debt['description']); ?></h5>
                                        <div class="text-muted">
                                            <?php echo $debt['type'] === 'owe' ? 'You owe ' : 'Owed by '; ?>
                                            <strong><?php echo htmlspecialchars($debt['person_name']); ?></strong>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-500 <?php echo $debt['type'] === 'owe' ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo $debt['type'] === 'owed' ? '+' : '-'; ?>₹<?php echo number_format($debt['amount'], 2); ?>
                                        </div>
                                        <div class="text-muted small">Total Amount</div>
                                    </div>
                                </div>

                                <div class="progress mb-4" style="height: 8px;">
                                    <?php $percentage = ($debt['total_paid'] / $debt['amount']) * 100; ?>
                                    <div class="progress-bar <?php echo $debt['type'] === 'owe' ? 'bg-danger' : 'bg-success'; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%" 
                                         aria-valuenow="<?php echo $percentage; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon bg-primary-light">
                                                <i class='bx bx-money text-primary'></i>
                                            </div>
                                            <div class="stat-title">Total Amount</div>
                                            <div class="stat-value">₹<?php echo number_format($debt['amount'], 2); ?></div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($debt['days_overdue'] > 0 && $debt['interest_amount'] > 0): ?>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon bg-warning-light">
                                                <i class='bx bx-trending-up text-warning'></i>
                                            </div>
                                            <div class="stat-title">Interest (<?php echo $debt['interest_rate']; ?>%)</div>
                                            <div class="stat-value text-warning">₹<?php echo number_format($debt['interest_amount'], 2); ?></div>
                                            <div class="stat-meta"><?php echo $debt['days_overdue']; ?> days overdue</div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon bg-success-light">
                                                <i class='bx bx-check-circle text-success'></i>
                                            </div>
                                            <div class="stat-title">Amount Paid</div>
                                            <div class="stat-value">₹<?php echo number_format($debt['total_paid'], 2); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card">
                                            <div class="stat-icon bg-danger-light">
                                                <i class='bx bx-wallet text-danger'></i>
                                            </div>
                                            <div class="stat-title">Total Due</div>
                                            <div class="stat-value text-danger">₹<?php echo number_format($remaining_amount + $debt['interest_amount'], 2); ?></div>
                                            <?php if ($debt['interest_amount'] > 0): ?>
                                            <div class="stat-meta">Includes interest</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <form action="add_debt_payment.php" method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="debt_id" value="<?php echo $debt_id; ?>">
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-500">Payment Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="amount" 
                                                   step="0.01" 
                                                   max="<?php echo $remaining_amount; ?>" 
                                                   required>
                                            <div class="invalid-feedback">
                                                Please enter a valid amount (max: ₹<?php echo number_format($remaining_amount, 2); ?>)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-500">Payment Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               name="payment_date" 
                                               value="<?php echo date('Y-m-d'); ?>" 
                                               max="<?php echo date('Y-m-d'); ?>" 
                                               required>
                                        <div class="invalid-feedback">
                                            Please select a valid date
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-500">Notes (Optional)</label>
                                        <textarea class="form-control" 
                                                  name="notes" 
                                                  rows="3" 
                                                  placeholder="Add any additional notes about this payment"></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="debts.php" class="btn btn-outline-secondary">
                                            <i class='bx bx-arrow-back me-2'></i>Back to Debts
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class='bx bx-check me-2'></i>Add Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="summary-card">
                                <h5 class="mb-4">Payment History</h5>
                                <?php
                                $stmt = $conn->prepare("
                                    SELECT * FROM debt_payments 
                                    WHERE debt_id = ? 
                                    ORDER BY payment_date DESC
                                ");
                                $stmt->bind_param("i", $debt_id);
                                $stmt->execute();
                                $payments = $stmt->get_result();

                                if ($payments->num_rows > 0):
                                    while ($payment = $payments->fetch_assoc()):
                                ?>
                                <div class="payment-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="fw-500">₹<?php echo number_format($payment['amount'], 2); ?></div>
                                        <div class="text-muted small">
                                            <?php echo date('M d, Y', strtotime($payment['payment_date'])); ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($payment['notes'])): ?>
                                    <div class="text-muted small"><?php echo htmlspecialchars($payment['notes']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <div class="text-center text-muted py-4">
                                    <i class='bx bx-receipt mb-2' style="font-size: 24px;"></i>
                                    <p class="mb-0">No payments recorded yet</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
    </script>
</body>
</html>
