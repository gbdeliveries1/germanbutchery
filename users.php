<?php
// Users Section
$users = [];
$result = $conn->query("
    SELECT u.*, 
           (SELECT picture FROM user_picture WHERE user_id = u.user_id LIMIT 1) as picture,
           (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as order_count
    FROM user u
    ORDER BY u.register_date DESC
    LIMIT 150
");
if($result) while($row = $result->fetch_assoc()) $users[] = $row;
?>

<div class="ali-card">
    <div class="ali-card-header">
        <h2 class="ali-card-title">👥 Users <span class="count"><?php echo count($users); ?></span></h2>
        <div class="ali-card-actions">
            <div class="ali-search">
                <span class="ali-search-icon">🔍</span>
                <input type="text" placeholder="Search users..." onkeyup="filterTable(this,'userTable')">
            </div>
        </div>
    </div>
    
    <div class="ali-table-wrap">
        <table class="ali-table" id="userTable">
            <thead>
                <tr>
                    <th style="width:50px"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th style="width:80px">Orders</th>
                    <th style="width:120px">Joined</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($users)): ?>
            <tr><td colspan="6" class="ali-empty"><div class="ali-empty-icon">👥</div><p>No users yet</p></td></tr>
            <?php else: ?>
            <?php foreach($users as $u): 
                $pic = $u['picture'] ?? '4.png';
            ?>
            <tr>
                <td>
                    <img src="/uploads/<?php echo htmlspecialchars($pic); ?>" class="ali-img" style="border-radius:50%;" onerror="this.src='/uploads/4.png';">
                </td>
                <td><strong><?php echo htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')); ?></strong></td>
                <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($u['phone_no'] ?? '-'); ?></td>
                <td>
                    <?php if($u['order_count'] > 0): ?>
                    <span class="ali-badge ali-badge-success"><?php echo $u['order_count']; ?> orders</span>
                    <?php else: ?>
                    <span class="ali-badge ali-badge-gray">0</span>
                    <?php endif; ?>
                </td>
                <td style="color:#999;font-size:12px;"><?php echo date('M d, Y', strtotime($u['register_date'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>