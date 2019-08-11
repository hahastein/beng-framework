<?php


namespace bengbeng\framework\user;


class WalletProperty
{
    public $balance;
    public $virtualCoin;
    public $points;

    public function __construct($wallet)
    {
        $this->balance = $wallet->balance;
        $this->virtualCoin = $wallet->virtualcoin;
        $this->points = $wallet->points;
    }
}