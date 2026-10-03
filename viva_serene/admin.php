<?php
include 'db.php';

// HANDLE DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM rooms WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// HANDLE CREATE/UPDATE
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $num = mysqli_real_escape_string($conn, $_POST['room_number']);
    $type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (empty($id)) {
        mysqli_query($conn, "INSERT INTO rooms (room_number, room_type, price, status, description) VALUES ('$num', '$type', '$price', '$status', '$desc')");
    } else {
        $id = (int)$id;
        mysqli_query($conn, "UPDATE rooms SET room_number='$num', room_type='$type', price='$price', status='$status', description='$desc' WHERE id=$id");
    }
    header("Location: admin.php");
    exit();
}

// Fetch data for editing
$edit_data = ['id'=>'','room_number'=>'','room_type'=>'','price'=>'','status'=>'','description'=>''];
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM rooms WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_data = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Viva Serene Management</title>
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
            --success: #2ecc71;
        }

        body {
            font-family: 'Poppins', sans-serif;
            padding: 0;
            margin: 0;
            background: var(--off-white);
            color: var(--text-dark);
            background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0);
            background-size: 40px 40px;
        }

        .admin-header {
            background: var(--deep-navy);
            color: var(--white);
            padding: 40px 20px;
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
            font-size: 2.5rem;
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

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #fdfdfd;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--gold-accent);
            box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.1);
            background: var(--white);
        }

        textarea { height: 100px; resize: none; }

        .btn-save {
            background: var(--deep-navy);
            color: var(--white);
            padding: 15px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn-save:hover {
            background: #163a66;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 25, 47, 0.3);
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            background: var(--deep-navy);
            color: var(--soft-gold);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1.5px;
            padding: 20px;
            text-align: left;
            font-weight: 500;
        }

        td { padding: 20px; border-bottom: 1px solid #eee; text-align: left; color: var(--text-dark); }

        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fafafa; }

        .status-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-Available { background: #e8f5e9; color: #2e7d32; }
        .status-Booked { background: #ffebee; color: #c62828; }

        .actions a {
            text-decoration: none;
            color: var(--deep-navy);
            font-weight: 500;
            margin-right: 15px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .actions a:hover { color: var(--gold-accent); }
        .actions a.delete { color: var(--error); }
        .actions a.delete:hover { color: #b02a2a; }

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
<body style="text-align: center;">


    <div class="admin-header">
        <img src="assets/img/Viva_Serene_Resort_Management_System_Logo.svg" alt="Management System Logo">
        <h2>Resort Room/Villa Management</h2>
    </div>

    <div class="main-container">
        <div class="form-box">
            <h3><?php echo !empty($edit_data['id']) ? "Edit Room Details" : "Add New Room"; ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

                <div class="input-group">
                    <label>Room Number</label>
                    <input type="text" name="room_number" placeholder="e.g. 101" value="<?php echo $edit_data['room_number']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Room Type</label>
                    <input type="text" name="room_type" placeholder="e.g. Luxury Suite" value="<?php echo $edit_data['room_type']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Price per Night (₱)</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" value="<?php echo $edit_data['price']; ?>" required>
                </div>

                <div class="input-group">
                    <label>Availability Status</label>
                    <select name="status">
                        <option value="Available" <?php if($edit_data['status'] == 'Available') echo 'selected'; ?>>Available</option>
                        <option value="Booked" <?php if($edit_data['status'] == 'Booked') echo 'selected'; ?>>Booked</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe the room amenities..."><?php echo $edit_data['description']; ?></textarea>
                </div>

                <button type="submit" name="save" class="btn-save">Save Room Information</button>
                <?php if(!empty($edit_data['id'])): ?>
                    <div style="text-align:center; margin-top:15px;">
                        <a href="admin.php" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">Cancel Editing</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_number ASC");
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td><strong style='color: var(--deep-navy)'>{$row['room_number']}</strong></td>
                            <td>{$row['room_type']}</td>
                            <td>₱" . number_format($row['price'], 2) . "</td>
                            <td><span class='status-pill status-{$row['status']}'>{$row['status']}</span></td>
                            <td class='actions'>
                                <a href='admin.php?edit={$row['id']}'>Edit</a>
                                <a href='admin.php?delete={$row['id']}' class='delete' onclick='return confirm(\"Are you sure you want to remove this room?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="back-link">
        <a href="index.php" class="footer-nav-btn">&larr; Return to Public Website</a>
        <a href="reservations.php" class="footer-nav-btn">Manage Reservations &rarr;</a>
    </div>

</body>
</html>
