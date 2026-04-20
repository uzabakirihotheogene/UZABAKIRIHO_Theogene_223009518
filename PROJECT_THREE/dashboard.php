<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: /hotel_website/login.html");
    exit();
}

// Handle delete order
if (isset($_GET['delete_order'])) {
    $id = intval($_GET['delete_order']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php?tab=orders");
    exit();
}

// Handle delete message
if (isset($_GET['delete_message'])) {
    $id = intval($_GET['delete_message']);
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php?tab=messages");
    exit();
}

// Get counts for stats
$total_orders   = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_messages = $conn->query("SELECT COUNT(*) as c FROM contacts")->fetch_assoc()['c'];
$total_users    = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - THEOGENE RELAX HOTEL</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
        body { display: flex; min-height: 100vh; background: #f1f5f9; }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            background: #111f17;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            top: 0; left: 0;
        }
        .sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .brand h2 { font-size: 14px; color: #f8fafc; }
        .sidebar .brand p  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }
        .sidebar a:hover        { background: rgba(255,255,255,0.06); color: #f1f5f9; }
        .sidebar a.active       { background: rgba(201,168,76,0.2); color: #e8c97a; border-left: 3px solid #c9a84c; }
        .sidebar .logout        { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); }

        /* MAIN */
        .main {
            margin-left: 220px;
            flex: 1;
            padding: 30px;
        }

        /* TOP BAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .topbar h1 { font-size: 22px; color: #0f172a; }
        .topbar span { font-size: 13px; color: #64748b; }

        /* STATS CARDS */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .stat-card .label { font-size: 13px; color: #64748b; margin-bottom: 6px; }
        .stat-card .value { font-size: 32px; font-weight: 700; color: #1a3a2a; }
        .stat-card .sub   { font-size: 12px; color: #22c55e; margin-top: 4px; }

        /* TABLE */
        .table-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .table-box h3 {
            padding: 16px 20px;
            font-size: 15px;
            color: #1a3a2a;
            border-bottom: 1px solid #e2e8f0;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #1a3a2a;
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
        }
        td {
            padding: 12px 16px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-delete:hover { background: #fecaca; }

        .empty { text-align: center; padding: 30px; color: #94a3b8; font-size: 14px; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <h2>THEOGENE RELAX</h2>
        <p>Admin Panel</p>
    </div>

    <a href="dashboard.php"               class="<?= $tab=='dashboard' ? 'active' : '' ?>"> Dashboard</a>
    <a href="dashboard.php?tab=orders"    class="<?= $tab=='orders'    ? 'active' : '' ?>"> Orders</a>
    <a href="dashboard.php?tab=messages"  class="<?= $tab=='messages'  ? 'active' : '' ?>">Messages</a>
    <a href="dashboard.php?tab=users"     class="<?= $tab=='users'     ? 'active' : '' ?>"> Users</a>

    <a href="logout.php" class="logout">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="topbar">
        <h1>
            <?php
                if ($tab == 'orders')   echo 'Orders';
                elseif ($tab == 'messages') echo 'Messages';
                elseif ($tab == 'users')    echo 'Users';
                else echo 'Dashboard Overview';
            ?>
        </h1>
        <span>Logged in as <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
    </div>

    <?php if ($tab == 'dashboard'): ?>
    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <div class="label">Total Orders</div>
            <div class="value"><?= $total_orders ?></div>
            <div class="sub">All time</div>
        </div>
        <div class="stat-card">
            <div class="label">Messages</div>
            <div class="value"><?= $total_messages ?></div>
            <div class="sub">From contact form</div>
        </div>
        <div class="stat-card">
            <div class="label">Users</div>
            <div class="value"><?= $total_users ?></div>
            <div class="sub">Registered</div>
        </div>
    </div>

    <!-- RECENT ORDERS -->
    <div class="table-box">
        <h3>Recent Orders</h3>
        <table>
            <tr><th>Name</th><th>Email</th><th>Menu</th><th>Date</th><th>Action</th></tr>
            <?php
            $result = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['menu']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><a href="dashboard.php?delete_order=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this order?')">Delete</a></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="5" class="empty">No orders yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <?php elseif ($tab == 'orders'): ?>
    <!-- ALL ORDERS -->
    <div class="table-box">
        <h3>All Orders</h3>
        <table>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Menu</th><th>Address</th><th>Date</th><th>Action</th></tr>
            <?php
            $result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['menu']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><a href="dashboard.php?tab=orders&delete_order=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7" class="empty">No orders yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <?php elseif ($tab == 'messages'): ?>
    <!-- MESSAGES -->
    <div class="table-box">
        <h3>Contact Messages</h3>
        <table>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Action</th></tr>
            <?php
            $result = $conn->query("SELECT * FROM contacts ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><a href="dashboard.php?tab=messages&delete_message=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="5" class="empty">No messages yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <?php elseif ($tab == 'users'): ?>
    <!-- USERS -->
    <div class="table-box">
        <h3>Registered Users</h3>
        <table>
            <tr><th>#</th><th>Username</th></tr>
            <?php
            $result = $conn->query("SELECT id, username FROM users ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="2" class="empty">No users found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

</div>
</body>
</html>