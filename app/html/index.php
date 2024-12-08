<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/../server.php';

if (!check_auth()) {
    redirect('/auth.php');
}

$db = new Capsule;
$db->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOSTNAME'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$db->setAsGlobal();
$db->bootEloquent();

$schema = $db->schema();


if (
    !$schema->hasTable('plans') ||
    !$schema->hasTable('servers')
){
    $schema->create('plans', function(Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('type');
        $table->integer('vCores')->nullable();
        $table->integer('ram')->nullable();
        $table->integer('storage')->nullable();
        $table->double('price')->nullable();
    });

    $schema->create('servers', function(Blueprint $table) {
        $table->id();
        $table->string('providers');
        $table->string('name');
        $table->text('cpu')->nullable();
        $table->integer('ram')->nullable();
        $table->integer('storage')->nullable();
        $table->double('price')->nullable();
    });
}

function round_up($number, $precision = 0)
{
    $fig = (int) str_pad('1', $precision, '0');
    return (ceil($number * $fig) / $fig);
}

$plans = $db->table('plans')->get();
$servers = $db->table('servers')->get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoster</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container-fluid">
        <nav>
            <ul>
                <li><strong>Hoster Tables</strong></li>
            </ul>
            <ul>
                <li>Hi, <?= $_SESSION['user']->name ?? 'John Doe' ?></li>
                <li><a href="/connect-adm.php">Admin</a></li>
                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </nav>
        <article>
            <h2>Plans</h2>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>vCores</th>
                        <th>Ram</th>
                        <th>Storage</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plans as $item) : ?>
                        <tr>
                            <th><?= $item->name ?></th>
                            <td><code><?= $item->type ?></code></td>
                            <td><?= $item->vCores ?></td>
                            <td><?= $item->ram ?> GB</td>
                            <td><?= $item->storage ?> GB</td>
                            <td  data-tooltip="<?= $item->price * 12 ?>€ /year"><?= $item->price ?>€ <sub>/ month</sub></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <article>
            <h2>Servers</h2>

            <table>
                <thead>
                    <tr>
                        <th>Provider</th>
                        <th>Name</th>
                        <th>CPU</th>
                        <th>Ram</th>
                        <th>Disks</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servers as $item) : ?>
                        <tr>
                            <th><?= $item->provider ?></th>
                            <td><?= $item->name ?></td>
                            <td><?= $item->cpu ?></td>
                            <td><?= $item->ram ?> GB</td>
                            <td><?= $item->storage ?> GB</td>
                            <td data-tooltip="<?= $item->price * 12 ?>€ /year"><?= $item->price ?>€ <sub>/month</sub></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <article>
            <table>
                <thead>
                    <tr>
                        <th>Plan</th>
                        <?php foreach ($servers as $item) {
                            echo "<th>".$item->name."</th>";
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plans as $plan) : ?>
                        <tr>
                            <th><?= $plan->name ?></th>
                            <?php foreach ($servers as $server) : 
                                $user_per_ram = $server->ram / $plan->ram;
                                $user_per_storage = $server->storage / $plan->storage;
                                $min_user = $server->price / $plan->price;
                                $min_gewinn = (round_up($min_user) * $plan->price) - $server->price;
                                ?>
                                <td>
                                    <table>
                                        <tr>
                                            <td>User pro Ram</td>
                                            <td data-tooltip="<?= $user_per_ram ?>"><?= round_up($user_per_ram) ?></td>
                                        </tr>
                                        <tr>
                                            <td>User pro Speicher</td>
                                            <td data-tooltip="<?= $user_per_storage ?>"><?= round_up($user_per_storage) ?></td>
                                        </tr>
                                        <tr>
                                            <td>min. User</td>
                                            <td data-tooltip="<?= $min_user ?>"><?= round_up($min_user) ?></td>
                                        </tr>
                                        <tr>
                                            <td>min. Gewinn</td>
                                            <td data-tooltip="<?= $min_gewinn * 12 ?>€ /year"><?= $min_gewinn ?>€ <sub>/month</sub></td>
                                        </tr>
                                    </table>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>
    </main>
</body>
</html>
