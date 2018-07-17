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
    //登录类型
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

    //用户绑定类型
    const USER_BIND_MOBILE = 1;
    const USER_BIND_WEIXIN = 2;

    //
    const STRUCTURE_AREA_ORDER = 1;
    const STRUCTURE_AREA_RECURSION = 2;

    //附件类型
    const ATTACHMENT_TYPE_STORE = 10;//商户
    const ATTACHMENT_TYPE_EVALUATE = 11;//评价
    const ATTACHMENT_TYPE_GOODS = 12;//商品

    //评价
    const EVALUATE_STATUS_SHOW = 10;
    const EVALUATE_STATUS_DELETE = 20;

    const EVALUATE_TYPE_GOODS = 20;
    const EVALUATE_TYPE_USER = 10;
    const EVALUATE_TYPE_ARTICLE = 30;

    //支付
    const PAYMENT_TYPE_WEIXIN = 2;
    const PAYMENT_TYPE_ALIPAY = 1;

    //关注
    const FOLLOW_TYPE_EVALUATE = 7;
    const FOLLOW_TYPE_USER = 1;

    const IM_REQUEST_POST_TYPE_CURL = 1;
    const IM_REQUEST_POST_TYPE_FSOCK = 2;



}