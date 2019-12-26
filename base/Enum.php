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

    //注册类型
    const REG_TYPE_MOBILE = 10;
    const REG_TYPE_WEIXIN = 30;

    const BIND_TYPE_USER = 10;

    // 支付类型0未支付40线下支付11支付宝10微信12银联13苹果支付20积分30余额
    const PAY_TYPE_NOPAY = 0;
    const PAY_TYPE_OFFLINE = 0;
    const PAY_TYPE_ALIPAY = 11;
    const PAY_TYPE_WXPAY = 10;
    const PAY_TYPE_UNIONPAY = 12;
    const PAY_TYPE_APPLEPAY = 13;
    const PAY_TYPE_POINT = 20;
    const PAY_TYPE_BALANCE = 30;

    //订单状态0取消订单5异常订单10未支付(待付款)20已支付(待发货)30已发货(待收货)40已收货(订单完成)50待拼团
    const ORDER_STATUS_CANCEL = 0;
    const ORDER_STATUS_EXCEPTION = 5;
    const ORDER_STATUS_NOPAY = 10;
    const ORDER_STATUS_WAIT_DELIVER = 20;
    const ORDER_STATUS_WAIT_RECEIVE = 30;
    const ORDER_STATUS_FINISH = 40;
    const ORDER_STATUS_WAIT_SPELL = 50;

    //退款状态0无退款1部分退款2全部退款
    const ORDER_REFUND_NO = 0;
    const ORDER_REFUND_PART = 1;
    const ORDER_REFUND_ALL = 2;


    //设备类型来源
    const DRIVER_TYPE_IOS = 10;
    const DRIVER_TYPE_ANDROID = 11;
    const DRIVER_TYPE_H5 = 12;
    const DRIVER_TYPE_WXXCX = 13;
    const DRIVER_TYPE_WXMP = 14;

    //用户绑定类型
    const USER_BIND_MOBILE = 1;
    const USER_BIND_WEIXIN = 2;
    const USER_BIND_TWOWAY= 3;
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
    const PAYMENT_TYPE_WEIXIN = 10;
    const PAYMENT_TYPE_ALIPAY = 11;

    //关注
    const FOLLOW_TYPE_EVALUATE = 7;
    const FOLLOW_TYPE_USER = 1;

    const IM_REQUEST_POST_TYPE_CURL = 1;
    const IM_REQUEST_POST_TYPE_FSOCK = 2;


    //错误类型定义
    const ERROR_NO_PARAMS = 108001;     //没有参数
    const ERROR_NO_FIND_USER = 200001;  //没有找到用户信息
    const ERROR_CUSTOMER = 0;           //自定义错误类型

    //成功类型定义
    const SUCCESS_ADD_USER = 200001;
    const SUCCESS_EDIT_USER = 200002;
    const SUCCESS_CUSTOMER = 0;           //自定义错误类型

    //平台统计类型定义
    const PLATFORM_STATISTICS_ACCOUNT_AMOUNT = 'account_amount';
    const PLATFORM_STATISTICS_USER_COUNT = 'user_count';
    const PLATFORM_STATISTICS_ORDER_COUNT = 'order_count';
    const PLATFORM_STATISTICS_GOODS_COUNT = 'goods_count';
    const PLATFORM_STATISTICS_PRE_DAY_ADD_USER_COUNT = 'pre_day_add_user_count';
    const PLATFORM_STATISTICS_PRE_MONTH_ADD_USER_COUNT = 'pre_month_add_user_count';

    //
    const ACCESS_RULE_AUTHENTICATED = '@';
    const ACCESS_RULE_GUEST = '?';
    const ACCESS_RULE_NULL = '';

    //输出类型
    const OUTPUT_JSON = 1;
    const OUTPUT_ARRAY = 2;
    const OUTPUT_XML = 3;
    const OUTPUT_HTML = 4;

    //缓存名称
    const CACHE_MENU_DATA = 'SYSTEM_MENU_DATA';
    const CACHE_USER_DATA = 'U_';

    //namespace
    const NAMESPACE_FRAMEWORK = '\\bengbeng\\framework\\';
    const NAMESPACE_ADMIN = '\\bengbeng\\admin\\';
    const NAMESPACE_MERCHANT = '\\bengbeng\\extend\\merchant\\';

    //扩展功能定义
    const EXTEND_TOOLS_MERCHANT = 'merchant'; //商户系统
    const EXTEND_TOOLS_SHOP = 'shop'; //电商系统
    const EXTEND_TOOLS_CMS = 'cms'; //内容文章系统
    const EXTEND_TOOLS_WM = 'waimai'; //外卖系统
    const EXTEND_TOOLS_ERP = 'erp'; //进销存系统
    const EXTEND_TOOLS_FINANCE = 'finance'; //财务系统

    const MODULE_TYPE_ARTICLE = 10;
    const MODULE_TYPE_FAQS = 20;
    const MODULE_TYPE_FAQS_REPLAY = 21;
    const MODULE_TYPE_GOODS = 30;

    //系统通用状态
    const SYSTEM_STATUS_SUCCESS = 10;
    const SYSTEM_STATUS_DELETE = 0;
    const SYSTEM_STATUS_VIOLATION = 1;

}