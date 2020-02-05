<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;

/* @var $site string */
/* @var $phone string */
$css = <<<CSS
        img { max-width: 600px; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;}
        a img { border: none; }
        table { border-collapse: collapse !important; }
        #outlook a { padding:0; }
        .ReadMsgBody { width: 100%; }
        .ExternalClass {width:100%;}
        .backgroundTable {margin:0 auto; padding:0; width:100% !important;}
        table td {border-collapse: collapse;}
        .ExternalClass * {line-height: 115%;}

        /* General styling */
        td {
            font-family: Arial, sans-serif;
        }
        body {
            -webkit-font-smoothing:antialiased;
            -webkit-text-size-adjust:none;
            width: 100%;
            height: 100%;
            color: #6f6f6f;
            font-weight: 400;
            font-size: 18px;
        }
        h1 {
            margin: 10px 0;
        }
        a {
            color: #333333;
            text-decoration: none;
        }
        .body-padding {
            padding: 0 75px;
        }
        .force-full-width {
            width: 100% !important;
        }

        /* Mobile styles */
        @media only screen and (max-width: 1039px) {
            table[class*="w1300"] {
                width: 600px !important;
                font-size: 24px !important;
            }
            td[class*="w1300"] {
                width: 560px !important;
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
            td[class*="mobile-center"] {
                text-align: center !important;
            }
            td[class*="column-padding"] {
                padding: 20px 0px !important;
            }
            td[class*="bottom-padding"] {
                padding-bottom: 40px !important;
            }
            td[class*="top-padding"] {
                padding-top: 40px !important;
            }
        }

        /* Mobile styles */
        @media only screen and (max-width: 599px) {
            table[class*="w320"] {
                width: 320px !important;
                font-size: 18px !important;
            }
            td[class*="w320"] {
                width: 280px !important;
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
            td[class*="mobile-spacing"] {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }
            *[class*="mobile-hide"] {
                display: none !important;
                width: 0 !important;
            }
            *[class*="mobile-br"] {
                font-size: 12px !important;
            }
            td[class*="mobile-center"] {
                text-align: center !important;
            }
            table[class*="columns"] {
                width: 100% !important;
            }
            td[class*="column-padding"] {
                padding: 10px 0px !important;
            }
            td[class*="bottom-padding"] {
                padding-bottom: 30px !important;
            }
            td[class*="top-padding"] {
                padding-top: 30px !important;
            }
            td[class*="logo-block"] img {
				width: 180px !important;
				height: 180px !important;
            }
        }
CSS;
$this->registerCss($css);
?>

<table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
    <tr>
        <td align="center" valign="top" background="#f7f7f7" style="background-color: #f7f7f7;" width="100%">
            <!-- HEADER -->
            <table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
                <tr>
                    <td align="center" valign="top" width="100%">
                        <center>
                            <table cellspacing="0" cellpadding="0" width="100%" class="w320 w1300" style="width: 100%; font-family: Arial, sans-serif; font-size: 2em; max-width: 1040px; padding-left: 10px; padding-right: 10px;">
                                <tr>
                                    <td align="center" valign="top" class="column-padding top-padding logo-block" style="text-align: center; padding-top: 100px; padding-bottom: 90px;">
                                        <a href="https://ktoti.me/" target="_blank"><img src="<?= $message->embed($logo); ?>" alt="logo" width="260px" height="260px"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" class="column-padding" style="text-align: center; padding-top: 0px; padding-bottom: 45px; color: #333333; line-height: 1;">
                                        Вы на нашем сайте <a href="https://ktoti.me/" target="_blank" style="color: #f7a916; font-weight: bold;">nomer.io</a> как-то давно искали информацию по номеру:
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" class="column-padding" style="text-align: center; padding-top: 0px; padding-bottom: 0px; font-weight: bold; text-decoration: underline; color: #333333; line-height: 1;">
                                        <a href="tel:<?=$phone;?>"><?=$phone;?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" class="column-padding bottom-padding" style="text-align: center; padding-top: 45px; padding-bottom: 65px; color: #333333; line-height: 1; border-bottom: 3px solid #b3b3b3;">
                                        но так как не заплатили нам, то мы не показали результат.
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- END HEADER -->

            <!-- FOOTER -->
            <table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
                <tr>
                    <td align="center" valign="top" width="100%">
                        <center>
                            <table cellspacing="0" cellpadding="0" width="100%" class="w320 w1300" style="width: 100%; max-width: 1040px; padding-left: 10px; padding-right: 10px; font-family: Arial, sans-serif; font-size: 2em;">
                                <tr>
                                    <td align="center" valign="top" class="column-padding top-padding" style="text-align: center; padding-top: 45px; padding-bottom: 100px; color: #333333; line-height: 1;">
                                        Сейчас мы всё же решили показать вам, на что мы способны совершенно бесплатно и вы можете ознакомиться со всей добытой нами информацией по этому номеру ниже в скриншоте:
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" class="column-padding" style="padding-top: 0px; padding-bottom: 100px; box-sizing: border-box;">
                                        <img src="<?= $message->embed($screenshot); ?>" alt="img" width="100%" style="border-radius: 10px; border: 2px solid #f7a916; max-width: 100%; width: 100%; box-sizing: border-box;">
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <!-- END FOOTER -->
        </td>
    </tr>