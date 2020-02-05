<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;


$this->title = \Yii::$app->name . ' - Информация по номеру телефона';

$site = \app\models\Site::find()->where(["name" => \Yii::$app->request->hostName])->one();

$exclude = 0;
$canComment = false;
if (!\Yii::$app->getUser()->isGuest) {
    $requests = \app\models\SearchRequest::find()->where(["user_id" => \Yii::$app->getUser()->getId(), "is_payed" => 0])->count();
    if ($requests) {
        $canComment = true; $exclude = 1;
    }
}
?>

        <div class="cont clfix">
            <!--
            <h2 style="display: none">Информация по номеру телефона</h2>

            <?php if (($error = \Yii::$app->session->getFlash("error"))): ?>
                <p class="error"><?= (!is_array($error)) ? $error : ""; ?></p>
            <?php endif; ?>

            <?php if (\Yii::$app->request->hostName == 'rutel.me'): ?>
                <p class="danger">Мы не предоставляем номер телефона по номеру машины, мы только показываем всю
                    доступную информацию из открытых источников по номеру телефона</p>
            <?php endif; ?>
            -->

            <div class="tabs-wrapper">
                <?php /*
                <ul>
                    <li class="active">
                        <span class="__desktop">Поиск по номеру телефона</span>
                        <span class="__mobile">Телефон</span>
                    </li>
                    <li>
                        <span class="__desktop">Поиск по профилю VK, Facebook, Instagram<br>или Email</span>
                        <span class="__mobile">Соцсети или Email</span>
                    </li>
                </ul>
                */?>
                <div class="tabs">
                    <div id="tab1" class="active">
                        <?= Html::beginForm(["search/index"], "post", ["class" => "searchPhoneForm"]); ?>
                        <p class="__mobile"><b>Поиск по номеру телефона</b></p>
                        <p>Введите номер мобильного телефона</p>
                        <?= MaskedInput::widget([
                            'name' => 'phone',
                            'mask' => '+7 ( 999 ) 999 - 99 - 99',
                            'options' => [
                                'autocomplete' => 'off',
                                'type' => 'tel',
                                'class' => 'searchPhoneInput',
                                'placeholder' => '+7 ( ___ ) ___ - __ - __',
                            ]
                        ]); ?>

                        <p><?=Html::checkbox("agree", true, ["onchange" => new \yii\web\JsExpression('if(this.checked) $("#searchByPhoneButton").removeAttr("disabled"); else $("#searchByPhoneButton").attr("disabled", "disabled")')]);?> Я согласен с <span class="rules">правилами сервиса<span>Я понимаю, что результат может мне не понравится и не соответствовать действительности по-моему мнению, но мне всё-равно придётся его оплатить.</span></span></p>

                        <input id="searchByPhoneButton" type="submit" class="searchButton" value="Найти" onclick="ga('send', 'event', 'button', 'click', 'search-button', $('[name=\'phone\']').val());">
                        <?= Html::endForm(); ?>
                    </div>
                    <?php /*
                    <div id="tab2">
                        <!--
                        <p>Определение номера телефона по профилю в социальных сетях - facebook, vkontakte, instagramm. Укажите, пожалуйста, ссылку на профиль человека</p>

                        <p style="font-weight: bold; color: darkred">ВНИМАНИЕ!!! Мы не работаем со знаменитостями и известными личностями!!!</p>
                        -->

                            <?php $form = ActiveForm::begin(['enableAjaxValidation' => true, 'options' => ['class' => 'searchProfileForm']]); ?>
                            <p><b>Определение номера телефона по профилю в facebook, VK, Instagram или по Email адресу*</b></p>
                            <?php if(\Yii::$app->getUser()->isGuest): ?>
                                <p>Зарегистрируйтесь или войдите в аккаунт чтобы начать поиск.</p>
                                <a href="#signup" class="searchButton">Зарегистрироваться</a>

                                <p style="margin-top: 10px;">У вас уже есть аккаунт? <?=Html::a("Войти", "#signin");?></p>
                            <?php else: ?>
                            <?=$form->field($model, "data", ['template' => '{input}{error}'])->textInput(['class' => 'searchProfileInput']);?>
                                <p><?=Html::checkbox("agree", true, ["onchange" => new \yii\web\JsExpression('if(this.checked) $("#searchByData").removeAttr("disabled"); else $("#searchByData").attr("disabled", "disabled")')]);?> Я согласен с <span class="rules">правилами сервиса<span>Я понимаю, что результат может мне не понравится и не соответствовать действительности по-моему мнению, но мне всё-равно придётся его оплатить.</span></span></p>

                                <?=Html::submitButton("Найти", ['class' => 'searchButton', 'id' => "searchByData"]);?>
                            <?php endif; ?>
                            <?php ActiveForm::end(); ?>

                    </div>
 */?>
                </div>
            </div>
        </div>
<?php /*
<?php if ($canComment): ?>
    <div class="cont clfix">
        <!-- Put this script tag to the <head> of your page -->
        <script type="text/javascript" src="//vk.com/js/api/openapi.js?146"></script>

        <script type="text/javascript">
            VK.init({apiId: <?=$site->vk_id;?>, onlyWidgets: true});
        </script>

        <!-- Put this div tag to the place, where the Comments block will be -->
        <div id="vk_comments"></div>
        <script type="text/javascript">
            VK.Widgets.Comments("vk_comments", {limit: 20, attach: "*", autoPublish: 1}, 777);
        </script>
    </div>
<?php endif; ?>
    <?php
    $comments = \app\models\VkComment::find()->where(['pid' => 0])->andWhere(["<>", "site_id", $exclude?$site->id:0])->orderBy(["tm" => SORT_DESC])->all();
    ?>
    <?php if(count($comments)): ?>
    <div class="cont clfix">
    <div class="wcomments_head _wcomments_head clear_fix">
        <a class="wcomments_logo" href="/dev/Comments"></a>
        <span class="wcomments_count _wcomments_count"><?=\Yii::t('app', '{n,plural,=0{комментариев} =1{1 комментарий} one{# комментарий} few{# комментария} many{# комментариев} other{# комментария}}', ['n' => count($comments)]);?></span>
    </div>
    <div class="_wcomments_posts_outer wcomments_posts_outer no_post_click wall_module wide_wall_module">
    <div class="wcomments_posts_inner">
        <div id="wcomments_posts" class="wcomments_posts">
            <?php foreach ($comments as $c): ?>
            <div class="_post post wcomments_post">
                <div class="_post_content">
                    <a target="_blank" class="post_image" href="https://vk.com/id<?= $c->vk_id; ?>">
                        <img src="data:image/jpg;base64,<?= $c->photo; ?>" class="post_img">
                    </a>
                    <div class="post_content">
                        <div class="wcomments_post_content">
                            <div class="post_author"><a target="_blank" class="author" href="https://vk.com/id<?= $c->vk_id; ?>"><?= $c->name; ?></a>
                            </div>
                            <div class="post_info">
                                <div class="wall_text">
                                    <div class="wall_post_cont _wall_post_cont">
                                        <div class="wall_post_text"><?= $c->comment; ?></div>
                                    </div>
                                </div>
                                <div class="wcomments_post_footer clear_fix">
                                    <div class="post_date"><span class="rel_date"><?= \Yii::$app->formatter->asRelativeTime($c->tm); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (count($c->comments)): ?>
                        <div class="replies">
                            <div class="replies_wrap">
                                <div class="replies_list _replies_list">
                                    <?php foreach ($c->comments as $cc): ?>
                                        <div class="reply reply_dived clear reply_replieable _post">
                                            <div class="reply_wrap _reply_content _post_content clear_fix">
                                                <a target="_blank" class="reply_image" href="https://vk.com/id<?= $cc->vk_id; ?>">
                                                    <img  src="data:image/jpg;base64,<?= $cc->photo; ?>" class="reply_img" width="40" height="40">
                                                </a>
                                                <div class="reply_content">
                                                    <div class="reply_author">
                                                        <a target="_blank" class="author" href="https://vk.com/id<?= $cc->vk_id; ?>"><?=$cc->name;?></a>
                                                    </div>
                                                    <div class="reply_text">
                                                        <div class="wall_reply_text"><?=preg_replace("/\[(.+)\|(.+)\], (.+)/", "$3", $cc->comment);?></div>
                                                    </div>
                                                    <div class="reply_footer clear_fix">
                                                        <div class="reply_date"><span class="rel_date rel_date_needs_update"><?=\Yii::$app->formatter->asRelativeTime($cc->tm);?></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
*/
?>
<?php $this->registerJs("jQuery('[name=phone]').bind('paste', function(e){
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
    text = text.replace(/[^0-9]/gim, '');
    if( text.charAt( 0 ) === '7' || text.charAt( 0 ) === '8' )
        text = text.slice( 1 );
    $(this).val(text);
 });
 
 "); ?>

<?php if(false):?><script><?php endif; ?>
<?php ob_start(); ?>

var wrapper = $('.tabs-wrapper');
wrapper.find('li').each(function(index) {
    var tabTitle = $(this);
    tabTitle.click(function() {
        $('.tabs-wrapper li').removeClass('active');
        tabTitle.addClass('active');
        wrapper.find('.tabs > div').removeClass('active');
        wrapper.find('.tabs > div:eq('+index+')').addClass('active');
    })
});

<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
