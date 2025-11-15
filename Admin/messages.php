<?php
include('../Database/database.php');
echo "<h4 class='mb-3'>Contact Messages</h4>";
$rs = $conn->query("SELECT id,user_id,name,email,subject,message,created_at FROM contact_messages ORDER BY created_at DESC");
?>
<div class="table-responsive">
<table class="table table-hover align-middle">
  <thead><tr><th>ID</th><th>From</th><th>Email</th><th>Subject</th><th>Message</th><th>Time</th></tr></thead>
  <tbody>
  <?php while($m=$rs->fetch_assoc()): ?>
    <tr>
      <td><?php echo $m['id'];?></td>
      <td><?php echo htmlspecialchars($m['name']);?> <?php if($m['user_id']) echo "<small class='text-muted'>(user #".$m['user_id'].")</small>";?></td>
      <td><?php echo htmlspecialchars($m['email']);?></td>
      <td><?php echo htmlspecialchars($m['subject']);?></td>
      <td style="white-space:pre-wrap"><?php echo htmlspecialchars($m['message']);?></td>
      <td class="text-nowrap"><?php echo $m['created_at'];?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
