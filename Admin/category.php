<?php
$cname= "";
$btnname="Insert";
$ucid="";
if(isset($_GET["ucid"]))
{
    $btnname="Update";
    $cname=$_GET["ucname"];
    $ucid=$_GET["ucid"];
}
if(isset($_POST["btnCat"]))
{
    if($_POST["btnCat"]=="Insert")
    {
        $cname= $_POST['catname'];
        $sql = "INSERT INTO product_category(category_name) VALUES ('$cname')";
        $conn->query($sql);
    }
    else if($_POST["btnCat"]=="Update")
    {
        $cname= $_POST['catname'];
        $ucid= $_POST['ucid'];
        $sql = "UPDATE product_category SET category_name='$cname' WHERE category_id=$ucid";
        $conn->query($sql);

        $btnname="Insert";
        $cname= "";
        $ucid="";
    }
}
?>

<!-- ======== Modern UI (no PHP logic changed) ======== -->
<!-- If your dashboard already includes these, you can remove them -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid px-4 py-4 page-wrap">

  <!-- Title pill -->
  <div class="text-center mb-4">
    <span class="badge text-bg-primary fs-5 px-4 py-2 rounded-pill shadow-sm">
      <i class="bi bi-tags me-2"></i>Manage Categories
    </span>
  </div>

  <!-- Form card -->
  <div class="card soft-card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex align-items-center gap-2 mb-3">
        <div class="btn btn-light btn-circle"><i class="bi bi-plus-lg"></i></div>
        <h5 class="mb-0 fw-semibold"><?php echo $btnname; ?> Category</h5>
      </div>

      <form action="dashboard.php" method="POST" class="row g-3">
        <div class="col-md-8">
          <label for="catname" class="form-label fw-semibold">Category Name</label>
          <input type="text" id="catname" name="catname"
                 class="form-control form-control-lg"
                 placeholder="Enter category name"
                 required value="<?php echo $cname; ?>">
        </div>

        <input type="hidden" name="ucid" value="<?php echo $ucid; ?>">

        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" name="btnCat" value="<?php echo $btnname;?>"
                  class="btn btn-lg w-100 <?php echo ($btnname=='Insert') ? 'btn-primary' : 'btn-warning'; ?>">
            <?php if ($btnname=='Insert'): ?>
              <i class="bi bi-check2-circle me-1"></i> Insert
            <?php else: ?>
              <i class="bi bi-pencil-square me-1"></i> Update
            <?php endif; ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Table card -->
  <div class="card soft-card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 pb-0">
      <h6 class="mb-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Category List</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light sticky-top">
            <tr>
              <th style="width:120px">ID</th>
              <th class="text-center">Category Name</th>
              <th style="width:220px" class="text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM product_category";
            $ans = $conn->query($sql);

            while($row = mysqli_fetch_assoc($ans)) {
              $id = $row['category_id'];
              $name = $row['category_name'];
            ?>
            <tr>
              <td class="fw-semibold"><?php echo $id; ?></td>
              <td class="text-center">
                <span class="badge text-bg-secondary-subtle border rounded-pill px-3 py-2">
                  <i class="bi bi-tag me-1"></i><?php echo $name; ?>
                </span>
              </td>
              <td class="text-end pe-4">
                <a href="dashboard.php?ucid=<?php echo $id;?>&ucname=<?php echo $name?>"
                   class="btn btn-outline-primary btn-sm me-2">
                  <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="dashboard.php?dcid=<?php echo $id;?>"
                   class="btn btn-outline-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this category?');">
                  <i class="bi bi-trash3 me-1"></i> Delete
                </a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Polished styles to match the screenshot vibe -->
<style>
  :root{
    --bg-soft:#f2f3f5;
    --card-radius:18px;
  }
  .page-wrap{ background: var(--bg-soft); border-radius: 16px; }
  body{ background: var(--bg-soft); }
  .soft-card{ border-radius: var(--card-radius); }
  .btn-circle{
    width: 40px; height: 40px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
  }
  .table thead th{ font-weight: 700; letter-spacing:.2px; }
  .table tbody tr:hover{ background: #f8f9fb; }
  .badge.text-bg-secondary-subtle{
    background: #f3f4f6; color:#374151; border-color:#e5e7eb;
  }
  .badge.text-bg-primary{ box-shadow: 0 6px 16px rgba(0,72,255,.15); }
  .card{ box-shadow: 0 6px 20px rgba(0,0,0,.06) !important; }
  .btn{ border-radius: 10px; }
</style>
