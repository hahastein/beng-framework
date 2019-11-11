<?php


namespace bengbeng\framework\shop;


use bengbeng\framework\base\Bootstrap;

/**
 * 商城逻辑处理类
 * @property GoodsLogic $goods 商品逻辑处理
 * @package bengbeng\framework\shop
 */
class Shop extends Bootstrap
{
    public function init()
    {
        parent::init();
        $this->moduleName = 'shop';
    }
}