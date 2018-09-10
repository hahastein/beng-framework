<?php
use yii\helpers\Url;
?>
<li <?= $controllerID=="site"?'class="active"':'' ?>>
    <a href="<?= Url::to(['site/index']) ?>"><i class="fa fa-th-large"></i> <span class="nav-label">快捷菜单</span> <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level">
        <li <?= $controllerID=="site" && $actionID=="index"?'class="active"':'' ?>><a href="<?= Url::to(['site/index']) ?>">数据统计</a></li>
    </ul>
</li>