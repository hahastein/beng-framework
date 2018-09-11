<?php
use yii\helpers\Url;
?>

<?php foreach ($menus as $menu){ ?>

    <li <?= $controllerID == $menu['controller']?'class="active"':'' ?>>
        <a><i class="fa fa-th-large"></i> <span class="nav-label"><?=$menu['menu_name']?></span> <span class="fa arrow"></span></a>
        <?php if(!empty($menu['parent'])) { ?>
        <ul class="nav nav-second-level">
            <?php foreach ($menu['parent'] as $parent){ ?>
            <li <?= $controllerID==$parent['controller'] && $actionID==$parent['action']?'class="active"':'' ?>>
                <a href="<?= Url::to([$menu['controller'].'/'.$menu['action']]) ?>"><?=$parent['menu_name']?></a>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </li>

<?php } ?>
