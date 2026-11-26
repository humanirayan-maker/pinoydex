<?php require 'config.php'; require 'partials.php';
$regions  = $pdo->query("SELECT * FROM region ORDER BY id")->fetchAll();
$statuses = $pdo->query("SELECT * FROM conservation_status ORDER BY id")->fetchAll();
$families = $pdo->query("SELECT * FROM family ORDER BY family_name")->fetchAll();
$stat = $pdo->query("SELECT COUNT(*) t, SUM(is_endemic) en, SUM(is_toxic) tx FROM species")->fetch();
$nfam = $pdo->query("SELECT COUNT(DISTINCT family_id) FROM species")->fetchColumn();
$slides = $pdo->query("SELECT id, common_name, scientific_name, image_url FROM species ORDER BY RAND()")->fetchAll();
foreach ($slides as &$s) { $s['loc'] = (bool)local_img($s); $s['img'] = img_for($s); }
unset($s);
$slides = array_filter($slides, fn($s) => $s['img']);
usort($slides, fn($a, $b) => $b['loc'] <=> $a['loc']);   // trees with your own photos first
$slides = array_slice($slides, 0, 6);                    // slider shows 6 trees
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>PinoyDex · Philippine Native &amp; Endemic Trees</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css"></head><body>
<?php nav(); ?>
<section class="hero" id="hero">
  <div class="hero-text">
    <h1>Learn,<br>Explore, Value</h1>
    <p class="lead">the magnificent diversity of Philippine trees</p>
    <p class="counts"><span><b><?= $stat['t'] ?></b> trees</span><span><b><?= (int)$stat['en'] ?></b> endemic</span><span><b><?= $nfam ?></b> families</span><span><b><?= (int)$stat['tx'] ?></b> toxic</span></p>
    <div class="ctrl"><button id="prev" aria-label="Previous">‹</button><span id="count">01/<?= str_pad(count($slides),2,'0',STR_PAD_LEFT) ?></span><button id="next" aria-label="Next">›</button>
      <a class="btn solid" href="#explore">Explore trees</a></div>
  </div>
  <div class="hero-art"><div class="sun"></div>
    <?php foreach ($slides as $i => $s): ?>
      <figure class="slide<?= $i ? '' : ' on' ?>"><img src="<?= e($s['img']) ?>" alt="<?= e($s['common_name']) ?>" onerror="this.closest('figure').remove();window.refreshSlides&&refreshSlides()">
        <figcaption><b><?= e($s['common_name']) ?></b><i><?= e($s['scientific_name']) ?></i></figcaption></figure>
    <?php endforeach; ?>
  </div>
</section>

<main id="explore">
  <form id="filters" class="filters" onsubmit="return false">
    <input type="search" name="q" placeholder="Search local name, common or scientific name…">
    <select name="region"><option value="">All island groups</option><?php foreach ($regions as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['region_name']) ?></option><?php endforeach; ?></select>
    <select name="status"><option value="">All statuses</option><?php foreach ($statuses as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['status_name']) ?></option><?php endforeach; ?></select>
    <select name="family"><option value="">All families</option><?php foreach ($families as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['family_name']) ?></option><?php endforeach; ?></select>
    <select name="endemic"><option value="">Endemic: any</option><option value="1">Endemic only</option><option value="0">Not endemic</option></select>
    <select name="toxic"><option value="">Toxic: any</option><option value="1">Toxic only</option><option value="0">Not toxic</option></select>
    <button type="reset" class="btn" id="reset">Reset</button>
  </form>
  <p id="result-count" class="sub" aria-live="polite"></p>
  <div id="grid" class="grid"></div>
</main>

<dialog id="modal"><button class="x" onclick="modal.close()" aria-label="Close">×</button><div id="modal-body"></div></dialog>
<script src="assets/app.js"></script></body></html>
