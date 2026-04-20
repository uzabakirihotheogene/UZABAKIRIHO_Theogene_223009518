<?php
session_start();
include 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: /hotel_website/login.html");
    exit();
}

// Redirect admins to dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: /hotel_website/dashboard.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['user'];
$tab      = isset($_GET['tab']) ? $_GET['tab'] : 'orders';

// Handle cancel (delete) own order
if (isset($_GET['cancel_order'])) {
    $oid  = intval($_GET['cancel_order']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $oid, $user_id);
    $stmt->execute();
    header("Location: my_account.php?tab=orders");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - THEOGENE RELAX HOTEL</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { display:flex; min-height:100vh; background:#f1f5f9; }

        .sidebar {
            width: 210px;
            background: #111f17;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }
        .sidebar .brand { padding:20px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar .brand h2 { font-size:14px; color:#f8fafc; }
        .sidebar .brand p  { font-size:11px; color:#94a3b8; margin-top:2px; }
        .sidebar a {
            display:flex; align-items:center; gap:10px;
            padding:12px 20px; color:#94a3b8;
            text-decoration:none; font-size:14px; transition:0.2s;
        }
        .sidebar a:hover  { background:rgba(255,255,255,0.06); color:#f1f5f9; }
        .sidebar a.active { background:rgba(201,168,76,0.2); color:#e8c97a; border-left:3px solid #c9a84c; }
        .sidebar .logout  { margin-top:auto; border-top:1px solid rgba(255,255,255,0.1); }

        .main { margin-left:210px; flex:1; padding:30px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
        .topbar h1 { font-size:22px; color:#0f172a; }
        .topbar span { font-size:13px; color:#64748b; }

        .table-box {
            background:white; border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,0.06);
            overflow:hidden;
        }
        .table-box h3 {
            padding:16px 20px; font-size:15px;
            color:#1a3a2a; border-bottom:1px solid #e2e8f0;
        }
        table { width:100%; border-collapse:collapse; }
        th { background:#1a3a2a; color:white; padding:12px 16px; text-align:left; font-size:13px; }
        td { padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #f1f5f9; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f8fafc; }
        .btn-cancel {
            background:#fee2e2; color:#dc2626; border:none;
            padding:5px 12px; border-radius:6px; font-size:12px;
            cursor:pointer; text-decoration:none;
        }
        .btn-cancel:hover { background:#fecaca; }
        .empty { text-align:center; padding:30px; color:#94a3b8; font-size:14px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>THEOGENE RELAX</h2>
        <p>My Account</p>
    </div>
    <a href="my_account.php?tab=orders"  class="<?= $tab=='orders'  ? 'active':'' ?>">My Orders</a>
    <a href="order.html"                                                               >Place New Order</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>My Orders</h1>
        <span>Logged in as <strong><?= htmlspecialchars($username) ?></strong></span>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert-success" style="margin-bottom:20px;">
        ✅ Order placed successfully!
    </div>
    <?php endif; ?>

    <div class="table-box">
        <h3>Your Orders</h3>
        <table>
            <tr>
                <th>Menu Item</th>
                <th>Date</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            <?php
            $stmt = $conn->prepare(
                "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC"
            );
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['menu']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                    <a href="my_account.php?cancel_order=<?= $row['id'] ?>"
                       class="btn-cancel"
                       onclick="return confirm('Cancel this order?')">Cancel</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="5" class="empty">You have no orders yet. <a href="order.html">Place one now →</a></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>