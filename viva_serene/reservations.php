<?php
include 'db.php';

// HANDLE DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $res = mysqli_query($conn, "SELECT room_id FROM reservations WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $room_id = $row['room_id'];
        mysqli_query($conn, "UPDATE rooms SET status='Available' WHERE id=$room_id");
    }
    mysqli_query($conn, "DELETE FROM reservations WHERE id=$id");
    header("Location: reservations.php");
    exit();
}

// HANDLE CREATE/UPDATE
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $room_id = (int)$_POST['room_id'];
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);

    if (empty($id)) {
        mysqli_query($conn, "INSERT INTO reservations (customer_name, customer_email, room_id, check_in, check_out) VALUES ('$name', '$email', $room_id, '$check_in', '$check_out')");
        mysqli_query($conn, "UPDATE rooms SET status='Booked' WHERE id=$room_id");
    } else {
        $id = (int)$id;
        mysqli_query($conn, "UPDATE reservations SET customer_name='$name', customer_email='$email', room_id=$room_id, check_in='$check_in', check_out='$check_out' WHERE id=$id");
        mysqli_query($conn, "UPDATE rooms SET status='Booked' WHERE id=$room_id");
    }
    header("Location: reservations.php");
    exit();
}

$edit_data = ['id'=>'','customer_name'=>'','customer_email'=>'','room_id'=>'','check_in'=>'','check_out'=>''];
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM reservations WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_data = $row;
    }
}

$rooms_res = mysqli_query($conn, "SELECT id, room_number FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservations - Viva Serene</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;800&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-navy: #0a192f;
            --gold-accent: #c5a059;
            --soft-gold: #e2c995;
            --off-white: #fcfaf7;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --white: #ffffff;
            --error: #d63031;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: var(--off-white);
            color: var(--text-dark);
        }

        .admin-header {
            background: var(--deep-navy);
            color: var(--white);
            padding: 30px 20px;
            text-align: center;
            border-bottom: 5px solid var(--gold-accent);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .admin-header img {
            height: 70px;
            filter: brightness(0) invert(1);
            margin-bottom: 20px;
        }

        .admin-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin: 0;
            letter-spacing: 1px;
            color: var(--soft-gold);
        }

        .main-container {
            max-width: 1200px;
            margin: -40px auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 40px;
            align-items: start;
        }

        .form-box {
            background: var(--white);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border: 1px solid rgba(197, 160, 89, 0.2);
            position: sticky;
            top: 30px;
        }

        .form-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-top: 0;
            color: var(--deep-navy);
            border-bottom: 2px solid var(--gold-accent);
            display: inline-block;
            margin-bottom: 25px;
        }

        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label {
            display: block;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .btn-save {
            background: var(--deep-navy);
            color: var(--white);
            padding: 15px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            text-transform: uppercase;
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: var(--deep-navy);
            color: var(--soft-gold);
            padding: 20px;
            text-align: left;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        td { padding: 20px; border-bottom: 1px solid #eee; text-align: left; }

        .actions a {
            text-decoration: none;
            color: var(--deep-navy);
            font-weight: 500;
            margin-right: 15px;
        }
        .actions a.delete { color: var(--error); }

        .back-link {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 40px auto 60px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--deep-navy); }

        .footer-nav-btn {
            background: var(--deep-navy);
            color: var(--soft-gold);
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer-nav-btn:hover {
            background: var(--gold-accent);
            color: var(--deep-navy);
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <img src="assets/img/Viva_Serene_Resort_Management_System_Logo.svg" alt="Management System Logo">
        <h2>Reservation Management</h2>
    </div>

    <div class="main-container">
        <div class="form-box">
            <h3><?php echo !empty($edit_data['id']) ? "Edit Reservation" : "New Reservation"; ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

                <div class="input-group">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" value="<?php echo $edit_data['customer_name']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="customer_email" value="<?php echo $edit_data['customer_email']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Room</label>
                    <select name="room_id" required>
                        <option value="">Select Room</option>
                        <?php while($room = mysqli_fetch_assoc($rooms_res)): ?>
                            <option value="<?php echo $room['id']; ?>" <?php if($edit_data['room_id'] == $room['id']) echo 'selected'; ?>>
                                Room #<?php echo $room['room_number']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Check-In Date</label>
                    <input type="date" name="check_in" value="<?php echo $edit_data['check_in']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Check-Out Date</label>
                    <input type="date" name="check_out" value="<?php echo $edit_data['check_out']; ?>" required>
                </div>

                <button type="submit" name="save" class="btn-save">Save Reservation</button>
                <?php if(!empty($edit_data['id'])): ?>
                    <div style="text-align:center; margin-top:15px;">
                        <a href="reservations.php" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT r.*, rooms.room_number FROM reservations r JOIN rooms ON r.room_id = rooms.id");
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                            <td>
                                <strong>{$row['customer_name']}</strong><br>
                                <small style='color: var(--text-muted)'>{$row['customer_email']}</small>
                            </td>
                            <td>Room #{$row['room_number']}</td>
                            <td>{$row['check_in']} to {$row['check_out']}</td>
                            <td class='actions'>
                                <a href='reservations.php?edit={$row['id']}'>Edit</a>
                                <a href='reservations.php?delete={$row['id']}' class='delete' onclick='return confirm(\"Delete this reservation?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="back-link">
        <a href="admin.php" class="footer-nav-btn">&larr; Back to Room Management</a>
        <a href="admin.php" class="footer-nav-btn">Room Management &rarr;</a>
    </div>
</body>
</html>
