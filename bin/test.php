<?php

// setting parameters
$targetLaguage = 'cz';
$source = ':pref:signals';
$target = ':pref:signals_texts';


/** @var  \Nette\DI\Container */
$container = require __DIR__ . '/../app/bootstrap.php';

/** @var \\DibiConnection */
$db = $container->getService('dibi.connection');

//
echo PHP_EOL;
echo "----------[ loading signals ]----------" . PHP_EOL;
echo PHP_EOL;

$signals = $db->fetchAll("SELECT * FROM %n", $source);

echo "Loaded signals: " . count($signals) . PHP_EOL;

echo PHP_EOL;
echo "----------[ migrating texts ]----------" . PHP_EOL;
echo PHP_EOL;

$db->query("DELETE FROM %n", $target);

foreach($signals as $signal) {

    // inserting czech texts
    $data = [
        'signals_id%i' => $signal->id,
        'title%s' => $signal->title,
        'name%s' => $signal->name,
        'timeframe%s' => $signal->timeframe,
        'perex%s' => $signal->perex,
        'description%s' => $signal->description,
        'impact%s' => $signal->impact,
        'likelyhood%s' => $signal->likelyhood,
        'drivers%s' => $signal->drivers,
        'recomendations%s' => $signal->recomendations,
        'lang%s' => $targetLaguage,
        'created%d' => $signal->created,
        'createdby%i' => $signal->createdby,
        'updated%d' => $signal->updated,
        'updatedby%i' => $signal->updatedby,
    ];
    $db->query("INSERT INTO %n", $target, $data);

    // inserting english texts
    $data = [
        'signals_id%i' => $signal->id,
        'lang%s' => 'en',
        'created%d' => $signal->created,
        'createdby%i' => $signal->createdby,
        'updated%d' => $signal->updated,
        'updatedby%i' => $signal->updatedby,
    ];
    $db->query("INSERT INTO %n", $target, $data);

    echo sprintf("Migrated: #%s - %s", $signal->id, substr($signal->title, 0, 50)) . PHP_EOL;
}


echo PHP_EOL;
echo "----------[ migration complete ]----------" . PHP_EOL;
echo PHP_EOL;