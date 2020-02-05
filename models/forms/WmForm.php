<?php
namespace app\models\forms;

use yii\web\BadRequestHttpException;

class WmForm extends \yii\base\Model
{
    public $LMI_PAYEE_PURSE;
    public $LMI_PAYMENT_AMOUNT;
    public $LMI_PAYMENT_NO;
    public $LMI_MODE;
    public $LMI_SYS_INVS_NO;
    public $LMI_SYS_TRANS_NO;
    public $LMI_SYS_TRANS_DATE;
    public $LMI_SECRET_KEY;
    public $LMI_PAYER_PURSE;
    public $LMI_PAYER_WM;
    public $LMI_HASH;

    private $_options = [
        'secret'    =>  'fsdfsdSdad12312asZZXvcfdf',
        'purse'     =>  'R626242660214',
        'mode'      =>  0
    ];

    /**
     * Declares the validation rules.
     */
    public function rules(){
        return array(
            [['LMI_PAYMENT_NO', 'LMI_MODE', 'LMI_SYS_INVS_NO', 'LMI_SYS_TRANS_NO', 'LMI_PAYER_WM'], 'integer'],
            [['LMI_PAYMENT_AMOUNT'], 'number'],
            [['LMI_PAYER_WM'], 'match', 'pattern' => '/\d{12}/i', 'message' => 'WMID должен содержать 12 цифр'],
            [['LMI_PAYEE_PURSE', 'LMI_PAYER_PURSE'], 'match', 'pattern' => '/[z,u,r]\d{12}/i', 'message' => 'Кошелек должен содержать 1 букву и 12 цифр'],
            [['LMI_SECRET_KEY'], 'safe'],
            [['LMI_HASH'], 'isTrueSign'],
            [['LMI_PAYEE_PURSE'], 'isTruePurse'],
            [['LMI_MODE'], 'isTrueMode'],
            [['LMI_PAYMENT_AMOUNT'], 'isTrueAmount'],
        );
    }

    /**
     * Check true payee purse
     */
    public function isTruePurse($attribute,$params)
    {
        if($this->_options['purse'] != $this->LMI_PAYEE_PURSE){
            throw new BadRequestHttpException('Ошибка в кошельке');
        }
    }

    /**
     * Check true mode
     */
    public function isTrueMode($attribute,$params)
    {
        if($this->_options['mode'] != $this->LMI_MODE){
            throw new BadRequestHttpException('Ошибка в режиме');
        }
    }

    /**
     * Check true paymant amount
     */
    public function isTrueAmount($attribute,$params)
    {
        /*
        $order = WebmoneyOrder::findOne($this->LMI_PAYMENT_NO);
        if($order->sum != $this->LMI_PAYMENT_AMOUNT){
            throw new BadRequestHttpException('Ошибка в сумме платежа');
        }
        */
    }

    /**
     * Check true sign
     */
    public function isTrueSign($attribute,$params)
    {
        $sign = $this->LMI_PAYEE_PURSE.
            $this->LMI_PAYMENT_AMOUNT.
            $this->LMI_PAYMENT_NO.
            $this->LMI_MODE.
            $this->LMI_SYS_INVS_NO.
            $this->LMI_SYS_TRANS_NO.
            $this->LMI_SYS_TRANS_DATE.
            $this->_options['secret'].
            $this->LMI_PAYER_PURSE.
            $this->LMI_PAYER_WM;

        //$sign = strtoupper(md5($sign));
        $sign = strtoupper(hash('sha256', $sign));
        if($sign != $this->LMI_HASH){
            throw new BadRequestHttpException('Ошибка в подписи');
        }
    }
}