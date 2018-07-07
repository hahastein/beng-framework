<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-07
 * Time: 16:31
 */

namespace bengbeng\framework\base;


class Enum
{
    const LOGIN_TYPE_MOBILE_PASS = 10;
    const LOGIN_TYPE_MOBILE_SMS = 20;
    const LOGIN_TYPE_WEIXIN = 30;
    const LOGIN_TYPE_ACCOUNT = 40;

    const BIND_TYPE_USER = 10;

    // 支付类型0线下1支付宝2微信3银联4苹果支付
    const PAY_TYPE_NOPAY = -1;
    const PAY_TYPE_OFFLINE = 0;
    const PAY_TYPE_ALIPAY = 1;
    const PAY_TYPE_WXPAY = 2;
    const PAY_TYPE_UNIONPAY = 3;
    const PAY_TYPE_APPLEPAY = 4;

    //订单状态0未支付1已支付2退款中3退款完成4取消订单5异常订单
    const ORDER_STATUS_NOPAY = 0;
    const ORDER_STATUS_PAY_FINISH = 1;
    const ORDER_STATUS_REFUND = 2;
    const ORDER_STATUS_REFUND_FINISH = 3;
    const ORDER_STATUS_CANCEL = 4;
    const ORDER_STATUS_EXCEPTION = 5;

    //设备类型来源
    const DRIVER_TYPE_IOS = 10;
    const DRIVER_TYPE_ANDROID = 11;
    const DRIVER_TYPE_H5 = 12;
    const DRIVER_TYPE_WXXCX = 13;
    const DRIVER_TYPE_WXMP = 14;

}