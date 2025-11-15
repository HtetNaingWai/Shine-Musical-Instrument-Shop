<?php
$mname= "";
$btnm="Insert";
if(isset($_GET["umid"]))
{
    $btnm="Update";
    $mname=$_GET["umname"];
    $mid=$_GET["umid"];
}
if(isset($_POST["btnm"]))
{
    if($_POST["btnm"]=="Insert")
    {
        $mname= $_POST['mname'];
        $sql = "INSERT INTO manufacturer(mname) VALUES ('$mname')";
        $conn->query($sql);
    }
    else if($_POST["btnm"]=="Update")
    {
        $mname= $_POST['mname'];
        $mid= $_POST['umid'];
        $sql = "UPDATE manufacturer SET mname='$mname' WHERE mid=$mid ";
        $conn->query($sql);

        $btnm="Insert";
        $mname= "";
        $mid="";
    }
}
?>

<div class="container py-5">

  <!-- Form Card -->
  <div class="card shadow-lg border-0 mb-5">
    <div class="card-body">
      <h5 class="card-title text-secondary mb-3"><?php echo $btnm; ?> Manufacturer</h5>

      <form action="dashboard.php" method="post" class="row g-3 align-items-center">
        <div class="col-md-8">
          <label for="mname" class="form-label fw-semibold">Manufacturer Name</label>
          <input type="text" id="mname" name="mname"
                 class="form-control form-control-lg shadow-sm"
                 placeholder="Enter Manufacturer Name"
                 required value="<?php echo $mname; ?>">
        </div>

        <input type="hidden" name="umid" value="<?php echo $mid; ?>">

        <div class="col-md-8 d-flex align-items-end">
          <button type="submit" name="btnm" value="<?php echo $btnm;?>"
                  class="btn btn-lg w-80 <?php echo ($btnm=='Insert') ? 'btn-primary' : 'btn-warning'; ?>">
            <?php echo ($btnm=='Insert') ? '➕ Insert' : '✏️ Update'; ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Manufacturer List</h5>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover table-striped align-middle mb-0">
        <thead class="table-light">
          <tr class="text-center">
            <th style="width:160px">Manufacturer ID</th>
            <th>Manufacturer Name</th>
            <th style="width:240px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM manufacturer";
          $ans= $conn->query($sql);   
          while($row = mysqli_fetch_assoc($ans)) {
              $mid = $row['mid'];
              $mname = $row['mname'];
          ?>
          <tr class="text-center">
            <td class="fw-semibold"><?php echo $mid;?></td>
            <td><?php echo htmlspecialchars($mname);?></td>
            <td>
              <a href="dashboard.php?umid=<?php echo $mid;?>&umname=<?php echo $mname;?>"
                 class="btn btn-outline-warning btn-sm me-2 px-3">
                ✏️ Edit
              </a>
              <a href="dashboard.php?dmid=<?php echo $mid;?>"
                 class="btn btn-outline-danger btn-sm px-3"
                 onclick="return confirm('Are you sure you want to delete this manufacturer?');">
                🗑️ Delete
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Subtle polish -->
<style>
  body { background: #f7f8fa; }
  .card { border-radius: 15px; }
  .btn { border-radius: 10px; transition: .3s; }
</style>
