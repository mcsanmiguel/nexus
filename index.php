<?php
require __DIR__.'/config.php';
$action=$_GET['action']??'list'; $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: 0;
$values=['sku'=>'','name'=>'','category'=>'ERP Software','price'=>'0.00','stock'=>'0','status'=>'Active','description'=>''];
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf(); $action=$_POST['action']??'list'; $id=(int)($_POST['id']??0);
 $values=[]; foreach(['sku','name','category','price','stock','status','description'] as $k) $values[$k]=trim((string)($_POST[$k]??''));
 if ($action==='delete') {
   $s=$pdo->prepare('DELETE FROM products WHERE id=?'); $s->execute([$id]); flash('Product deleted.'); header('Location: index.php'); exit;
 }
 if ($values['sku']===''||$values['name']===''||$values['category']==='') $error='SKU, product name and category are required.';
 elseif (!is_numeric($values['price'])||(float)$values['price']<0) $error='Enter a valid non-negative price.';
 elseif (filter_var($values['stock'],FILTER_VALIDATE_INT)===false||(int)$values['stock']<0) $error='Stock must be a non-negative whole number.';
 elseif (!in_array($values['status'],['Active','Inactive'],true)) $error='Invalid status.';
 else {
  try {
   if ($action==='edit') {
    $s=$pdo->prepare('UPDATE products SET sku=?,name=?,category=?,price=?,stock=?,status=?,description=? WHERE id=?');
    $s->execute([$values['sku'],$values['name'],$values['category'],$values['price'],$values['stock'],$values['status'],$values['description']?:null,$id]);
    flash('Product updated successfully.');
   } else {
    $s=$pdo->prepare('INSERT INTO products(sku,name,category,price,stock,status,description) VALUES(?,?,?,?,?,?,?)');
    $s->execute([$values['sku'],$values['name'],$values['category'],$values['price'],$values['stock'],$values['status'],$values['description']?:null]);
    flash('Product created successfully.');
   }
   header('Location: index.php'); exit;
  } catch(PDOException $ex) { $error=$ex->getCode()==='23000'?'That SKU already exists.':'Database operation failed.'; }
 }
}
if (in_array($action,['edit','view'],true) && $_SERVER['REQUEST_METHOD']!=='POST') {
 $s=$pdo->prepare('SELECT * FROM products WHERE id=?'); $s->execute([$id]); $found=$s->fetch();
 if (!$found) { flash('Product not found.'); header('Location: index.php'); exit; }
 $values=$found;
}
$search=trim((string)($_GET['q']??''));
if ($search!=='') { $s=$pdo->prepare('SELECT * FROM products WHERE name LIKE ? OR sku LIKE ? OR category LIKE ? ORDER BY updated_at DESC'); $term='%'.$search.'%'; $s->execute([$term,$term,$term]); $products=$s->fetchAll(); }
else $products=$pdo->query('SELECT * FROM products ORDER BY updated_at DESC')->fetchAll();
$total=(int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$stock=(int)$pdo->query('SELECT COALESCE(SUM(stock),0) FROM products')->fetchColumn();
$value=(float)$pdo->query('SELECT COALESCE(SUM(price*stock),0) FROM products')->fetchColumn();
function field(array $v,string $key,string $default=''): string { return e($v[$key]??$default); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>NexusTech | Product Management</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header><a class="logo" href="index.php"><b>NX</b><span>NEXUS<em>TECH</em><small>PRODUCT ADMIN</small></span></a><nav><a href="index.php">Products</a><a class="outline" href="index.php?action=create">＋ Add Product</a></nav></header><main class="wrap">
<?php if(!empty($_SESSION['flash'])): ?><p class="notice"><?=e($_SESSION['flash']);unset($_SESSION['flash']);?></p><?php endif;?>
<?php if(in_array($action,['create','edit'],true)): ?>
<section class="intro"><p class="eyebrow">CATALOG / <?=strtoupper(e($action))?></p><h1><?=$action==='edit'?'Edit':'Add'?> <span>Product</span></h1><p>Manage your software catalog and product packages.</p></section>
<section class="panel"><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="<?=e($action)?>"><input type="hidden" name="id" value="<?=$id?>">
<div class="fields">
<label>Product name *<input name="name" maxlength="120" required value="<?=field($values,'name')?>"></label>
<label>SKU *<input name="sku" maxlength="50" required value="<?=field($values,'sku')?>"></label>
<label>Category *<select name="category"><?php foreach(['ERP Software','HRIS & Payroll','POS System','Analytics','Integration','Other'] as $c):?><option <?=($values['category']===$c?'selected':'')?> value="<?=e($c)?>"><?=e($c)?></option><?php endforeach;?></select></label>
<label>Price (PHP) *<input type="number" min="0" step=".01" name="price" required value="<?=field($values,'price','0.00')?>"></label>
<label>Stock / Licenses *<input type="number" min="0" step="1" name="stock" required value="<?=field($values,'stock','0')?>"></label>
<label>Status<select name="status"><option <?=($values['status']==='Active'?'selected':'')?>>Active</option><option <?=($values['status']==='Inactive'?'selected':'')?>>Inactive</option></select></label>
<label class="wide">Description<textarea name="description" rows="4" maxlength="2000"><?=field($values,'description')?></textarea></label>
</div><div class="buttons"><a class="btn secondary" href="index.php">Cancel</a><button class="btn primary" type="submit">Save Product</button></div></form></section>
<?php elseif($action==='view' && isset($found)): ?>
<section class="intro"><p class="eyebrow">CATALOG / DETAILS</p><h1>Product <span>Details</span></h1></section><section class="panel detail"><h2><?=e($found['name'])?></h2><p><code><?=e($found['sku'])?></code> · <?=e($found['category'])?></p><div class="detailgrid"><?php foreach(['Price'=>'₱'.number_format((float)$found['price'],2),'Stock / Licenses'=>$found['stock'],'Status'=>$found['status'],'Created'=>$found['created_at'],'Updated'=>$found['updated_at']] as $label=>$val):?><div><small><?=e($label)?></small><strong><?=e($val)?></strong></div><?php endforeach;?></div><p><?=nl2br(e($found['description']?:'No description provided.'))?></p><div class="buttons"><a class="btn secondary" href="index.php">Back</a><a class="btn primary" href="index.php?action=edit&id=<?=$id?>">Edit Product</a></div></section>
<?php else: ?>
<section class="intro row"><div><p class="eyebrow">NEXUSTECH / INVENTORY</p><h1>Product <span>Management</span></h1><p>Create, view, edit and delete catalog products.</p></div><a class="btn primary" href="index.php?action=create">＋ Add Product</a></section>
<section class="stats"><div><small>Catalog Products</small><strong><?=$total?></strong></div><div><small>Units in Stock</small><strong><?=number_format($stock)?></strong></div><div><small>Inventory Value</small><strong>₱<?=number_format($value,2)?></strong></div></section>
<section class="panel"><div class="panelhead"><div><h2>Product Catalog</h2><p>Search and manage product records.</p></div><form class="search"><input name="q" value="<?=e($search)?>" placeholder="Search name, SKU or category"><button class="btn secondary">Search</button></form></div>
<div class="tablewrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($products as $p):?><tr><td><strong><?=e($p['name'])?></strong></td><td><code><?=e($p['sku'])?></code></td><td><?=e($p['category'])?></td><td>₱<?=number_format((float)$p['price'],2)?></td><td><?=number_format((int)$p['stock'])?></td><td><span class="badge <?=$p['status']==='Active'?'active':'inactive'?>"><?=e($p['status'])?></span></td><td><div class="actions"><a href="index.php?action=view&id=<?=$p['id']?>">View</a><a href="index.php?action=edit&id=<?=$p['id']?>">Edit</a><form method="post" onsubmit="return confirm('Delete this product?')"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="delete">Delete</button></form></div></td></tr><?php endforeach;?>
<?php if(!$products):?><tr><td colspan="7" class="empty">No products found. Add a product or change your search.</td></tr><?php endif;?></tbody></table></div></section>
<?php endif;?>
</main><footer>© <?=date('Y')?> NexusTech B2B Solutions · PHP + MySQL CRUD academic project</footer></body></html>
