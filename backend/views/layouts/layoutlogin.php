<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\ThemesAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
ThemesAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="background:#F7F7F7;">
<?php $this->beginBody() ?>



        <?= $content ?>




<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
