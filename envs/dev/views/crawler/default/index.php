<h1>Suchen</h1>
<p>Sie haben nach <b><?= $query; ?></b> gesucht.</p>

<h2><?= count($results); ?> Resultate</h2>
<ul>
<? foreach($results as $item): ?>
    <li><a href="<?= $item->url; ?>"><?= $item->title; ?></a>
    
        <p style="background-color:red;"><?= $item->preview($query); ?></p>
    </li>
<? endforeach; ?>
</ul>