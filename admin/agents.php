<?php

require_once __DIR__ . '/../app/Config/Config.php';
require_once __DIR__ . '/../app/Models/Agent.php';

$agents = Agent::all();

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Agent Manager</title>

<style>
body{
    margin:40px;
    background:#f5f5f5;
    font-family:Arial,Helvetica,sans-serif;
    color:#222;
}

.container{
    max-width:1100px;
    margin:auto;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h1{
    margin:0;
}

.button{
    background:#111;
    color:#fff;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

th{
    background:#222;
    color:#fff;
    text-align:left;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.empty{
    padding:40px;
    text-align:center;
    color:#777;
}
</style>

</head>

<body>

<div class="container">

<div class="header">
    <div>
        <h1>👥 Agent Manager</h1>
        <div><?php echo e(Config::appName()); ?></div>
    </div>

    <a class="button" href="agent-edit.php">+ Add Agent</a>
</div>

<table>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Website</th>
    <th>Status</th>
</tr>

<?php if(empty($agents)): ?>

<tr>
<td colspan="4" class="empty">No agents have been created yet.</td>
</tr>

<?php else: ?>

<?php foreach($agents as $agent): ?>

<tr>
<td><?= e($agent['display_name']) ?></td>
<td><?= e($agent['email']) ?></td>
<td><?= e($agent['website']) ?></td>
<td><?= ($agent['active']) ? 'Active' : 'Inactive' ?></td>
</tr>

<?php endforeach; ?>

<?php endif; ?>

</table>

</div>

</body>
</html>
