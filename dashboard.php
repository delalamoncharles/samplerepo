<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once 'includes/header.php';
require_once 'config/db.php';

// ── STATS ──
$total     = $conn->query("SELECT COUNT(*) AS c FROM medicines")->fetch_assoc()['c'];
$inStock   = $conn->query("SELECT COUNT(*) AS c FROM medicines WHERE status='In Stock'")->fetch_assoc()['c'];
$lowOut    = $conn->query("SELECT COUNT(*) AS c FROM medicines WHERE status IN ('Low Stock','Out of Stock') OR quantity <= 10")->fetch_assoc()['c'];
$expired   = $conn->query("SELECT COUNT(*) AS c FROM medicines WHERE status='Expired' OR expiration_date < CURDATE()")->fetch_assoc()['c'];

// ── RECENT 5 ──
$recent = $conn->query("SELECT * FROM medicines ORDER BY medicine_id DESC LIMIT 5");

// ── EXPIRING SOON (next 30 days) ──
$expiring = $conn->query("
    SELECT * FROM medicines
    WHERE expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY expiration_date ASC
    LIMIT 5
");

function statusClass($s) {
    return match($s) {
        'In Stock'     => 'in-stock',
        'Low Stock'    => 'low-stock',
        'Out of Stock' => 'out-stock',
        'Expired'      => 'expired',
        default        => ''
    };
}
?>

<div class="page-header">
  <div>
    <h1>Dashboard</h1>
    <p>Overview of the clinic inventory status — <?= date('F d, Y') ?></p>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-label">Total Medicines</div>
    <div class="stat-value"><?= $total ?></div>
    <div class="stat-sub">items in inventory</div>
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
	<g fill="none" stroke="#fff" stroke-width="1.5">
		<path d="M9.068 2h5.864c.92 0 1.382 0 1.668.293c.533.546.533 2.868 0 3.414C16.313 6 15.853 6 14.932 6H9.068c-.92 0-1.381 0-1.668-.293c-.533-.546-.533-2.868 0-3.414C7.686 2 8.147 2 9.068 2ZM8 6a7 7 0 0 1 .306.656a3 3 0 0 1-.23 2.542a7 7 0 0 1-.418.59l-.403.539c-.45.6-.675.9-.838 1.229a4 4 0 0 0-.35 1.05C6 12.965 6 13.34 6 14.092V16c0 2.828 0 4.243.879 5.121C7.757 22 9.172 22 12 22s4.243 0 5.121-.879C18 20.243 18 18.828 18 16v-1.908c0-.75 0-1.126-.067-1.487a4 4 0 0 0-.35-1.05c-.163-.328-.388-.628-.838-1.228l-.403-.538a7 7 0 0 1-.419-.59a3 3 0 0 1-.23-2.543c.06-.161.142-.326.307-.656" />
		<path stroke-linecap="round" d="M12 13v5m-2.5-2.5h5" />
	</g>
</svg></div>
  </div>
  <div class="stat-card blue">
    <div class="stat-label">In Stock</div>
    <div class="stat-value"><?= $inStock ?></div>
    <div class="stat-sub">available items</div>
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
	<path fill="#fff" fill-rule="evenodd" d="M12 21a9 9 0 1 0 0-18a9 9 0 0 0 0 18m-.232-5.36l5-6l-1.536-1.28l-4.3 5.159l-2.225-2.226l-1.414 1.414l3 3l.774.774z" clip-rule="evenodd" />
</svg></div>
  </div>
  <div class="stat-card yellow">
    <div class="stat-label">Low / Out of Stock</div>
    <div class="stat-value"><?= $lowOut ?></div>
    <div class="stat-sub">need restocking</div>
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 12 12">
	<path fill="#fff" d="M5.44 6.44a.562.562 0 1 0 1.124 0V3.56a.562.562 0 1 0-1.124 0zM6 8.25a.75.75 0 1 0 0 1.5a.75.75 0 0 0 0-1.5" stroke-width="0.1" stroke="#fff" />
	<path fill="#fff" fill-rule="evenodd" d="m.244 8.73l3.76-7.49c.827-1.65 3.16-1.65 3.98 0l3.76 7.49c.753 1.5-.327 3.27-1.99 3.27h-7.53c-1.67 0-2.74-1.77-1.99-3.27zm.894.449l3.76-7.49a1.227 1.227 0 0 1 2.2 0l3.76 7.49c.425.846-.192 1.82-1.1 1.82h-7.53c-.907 0-1.52-.976-1.1-1.82z" clip-rule="evenodd" stroke-width="0.1" stroke="#fff" />
</svg></div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Expired</div>
    <div class="stat-value"><?= $expired ?></div>
    <div class="stat-sub">to be disposed</div>
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
	<path fill="#fd0000" fill-rule="evenodd" d="M9.774 5L3.758 3.94l.174-.986a.5.5 0 0 1 .58-.405L18.411 5h.088h-.087l1.855.327a.5.5 0 0 1 .406.58l-.174.984l-2.09-.368l-.8 13.594A2 2 0 0 1 15.615 22H8.386a2 2 0 0 1-1.997-1.883L5.59 6.5h12.69zH5.5zM9 9l.5 9H11l-.4-9zm4.5 0l-.5 9h1.5l.5-9zm-2.646-7.871l3.94.694a.5.5 0 0 1 .405.58l-.174.984l-4.924-.868l.174-.985a.5.5 0 0 1 .58-.405z" />
</svg></div>
  </div>
</div>

<!-- Recent Entries -->
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div>
      <h3>Recent Entries</h3>
      <p>Last 5 medicines added to inventory</p>
    </div>
    <a href="medicines.php" class="btn btn-secondary btn-sm">View All →</a>
  </div>
  <table>
    <thead><tr>
      <th>ID</th><th>Medicine Name</th><th>Category</th>
      <th>Qty</th><th>Expiration</th><th>Status</th>
    </tr></thead>
    <tbody>
    <?php if ($recent->num_rows > 0): ?>
      <?php while ($m = $recent->fetch_assoc()): ?>
      <tr>
        <td><span class="med-id">#<?= str_pad($m['medicine_id'],3,'0',STR_PAD_LEFT) ?></span></td>
        <td><div class="med-name"><?= htmlspecialchars($m['medicine_name']) ?></div></td>
        <td><span class="badge cat"><?= htmlspecialchars($m['category'] ?: '—') ?></span></td>
        <td><span class="qty-num"><?= $m['quantity'] ?></span> <?= htmlspecialchars($m['unit'] ?: '') ?></td>
        <td class="mono"><?= $m['expiration_date'] ? date('m/d/Y', strtotime($m['expiration_date'])) : '—' ?></td>
        <td><span class="badge <?= statusClass($m['status']) ?>"><?= htmlspecialchars($m['status']) ?></span></td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr id="empty-row"><td colspan="6">
        <div class="empty-state"><span class="empty-icon">📭</span><p>No medicines found</p></div>
      </td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Expiring Soon -->
<div class="table-card">
  <div class="table-card-header">
    <div>
      <h3>⏳ Expiring Within 30 Days</h3>
      <p>Medicines that need attention soon</p>
    </div>
    <a href="expiring.php" class="btn btn-secondary btn-sm">View All →</a>
  </div>
  <table>
    <thead><tr>
      <th>Medicine</th><th>Category</th><th>Qty</th><th>Expiration</th><th>Days Left</th>
    </tr></thead>
    <tbody>
    <?php if ($expiring->num_rows > 0): ?>
      <?php while ($m = $expiring->fetch_assoc()):
        $days = (int) ceil((strtotime($m['expiration_date']) - time()) / 86400);
        $cls  = $days < 7 ? 'days-critical' : ($days < 15 ? 'days-warning' : 'days-ok');
      ?>
      <tr>
        <td><div class="med-name"><?= htmlspecialchars($m['medicine_name']) ?></div></td>
        <td><span class="badge cat"><?= htmlspecialchars($m['category'] ?: '—') ?></span></td>
        <td><?= $m['quantity'] ?> <?= htmlspecialchars($m['unit'] ?: '') ?></td>
        <td class="mono"><?= date('m/d/Y', strtotime($m['expiration_date'])) ?></td>
        <td><span class="<?= $cls ?>"><?= $days <= 0 ? 'Expired' : "In {$days} days" ?></span></td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5">
        <div class="empty-state"><span class="empty-icon"><svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24">
	<path fill="#e32a00" d="M22.175 10.525q.275.3.275.713t-.275.712l-3.55 3.525q-.275.3-.7.3t-.7-.3l-1.425-1.4q-.3-.3-.3-.712t.3-.713q.3-.275.713-.275t.712.275l.7.7l2.825-2.825q.3-.275.712-.275t.713.275m-11.85 9.9l-2.5-2.275q-1.8-1.625-3.087-2.9t-2.125-2.4t-1.225-2.175T1 8.475q0-2.35 1.575-3.912T6.5 3q1.3 0 2.475.538T11 5.075q.85-1 2.025-1.537T15.5 3q2.125 0 3.563 1.288T20.85 7.3q-.5-.2-1.05-.25T18.675 7q-2.125 0-3.9 1.713T13 13q0 1.2.525 2.438T15 17.45q-.475.425-1.237 1.088T12.45 19.7l-.8.725q-.275.25-.663.25t-.662-.25" />
</svg></span><p>No medicines expiring soon</p></div>
      </td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/footer.php'; ?>
