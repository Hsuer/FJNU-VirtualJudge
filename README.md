# FJNU-VirtualJudge

![PHP from Travis config](https://img.shields.io/travis/php-v/symfony/symfony.svg)[![Release](https://img.shields.io/github/release/qubyte/rubidium.svg)](Release)[![License](https://img.shields.io/aur/license/yaourt.svg)](LICENSE)

基于 Laravel 与 Swoole 的 ACM-ICPC 远程评测平台，支持HDOJ、POJ、FOJ、FJNUOJ~

已完成 Virtual Judge 的功能，主要功能 & 特点:

- Web端与爬虫服务均为 PHP 编写，运行部署简单快速
- 虚拟评测服务基于Swoole + Redis
- 提供虚拟评测API，轻松分离Web端与爬虫服务端

## 依赖
* Windows (受限于 Swoole，支持不完整), Linux, macOS
* PHP > 7.1.0
* MySQL > 5.1.60
* Redis > 2.6.10
* Swoole > 1.7.0
* Apache2

## 安装部署
以 Ubuntu 14.04 （LTS）为例。

### 安装 Swoole

通过 pecl 安装 (推荐)

    pecl install swoole

通过源码安装

    sudo apt-get install php5-dev
    git clone https://github.com/swoole/swoole-src.git
    cd swoole-src
    phpize
    ./configure
    make && make install

### 安装 MySQL

    apt-get install mysql-server mysql-client

### 安装 Apache2

    apt-get install apache2

### 安装 PHP

	apt-get install php7.0 libapache2-mod-php7.0
	/etc/init.d/apache2 restart

### 安装 Redis

	sudo apt-get install redis-server

### 部署 Web 端

	cp ./vjudge ~/var/www/vjudge

### 启动爬虫服务端

	cd ./vitual-judger
	php index.php

### 导入数据表

	mysql> source   ./database/fjnuvj.sql

## 配置

### Web 端
	cp ~/var/www/vjudge/.env.example ~/var/www/vjudge/.env

并填写以下数据

	APP_KEY=#随机字符串
	APP_URL=#当前URL
	DB_DATABASE=#表名
	DB_USERNAME=#MySQL 账户
	DB_PASSWORD=#MySQL 密码

### 爬虫服务端

#### 数据库配置

进入 [config.php](FJNU-VirtualJudge/virtual-judger/config.php) 编辑以下字段

	$BASE_PATH = ""; 	//指向Web端资源目录，如 /var/www/html/vjudge/public
	
	$DB_CONFIG = array(
	    'host'      => "127.0.0.1",
	    'port'      => 3306,
	    'username'  => "",
	    'password'  => "",
	    'database'  => ""
	);

#### 提交账号配置

进入 [account.php](FJNU-VirtualJudge/virtual-judger/account.php) 编辑账号，如下格式：

	$account = array(
	    'HDU' => 
	    [
	        ['account' => 'fjnuvj1', 'password' => ''],
	        ['account' => 'fjnuvj2', 'password' => ''],
	    ],
	);

## 预览
![index](http://7lrwu1.com1.z0.glb.clouddn.com/index.png)

![problemset](http://7lrwu1.com1.z0.glb.clouddn.com/problem.png)

![problem](http://7lrwu1.com1.z0.glb.clouddn.com/download.png)

![contestset](http://7lrwu1.com1.z0.glb.clouddn.com/contest.png)

![contestshow](http://7lrwu1.com1.z0.glb.clouddn.com/contest_show.png)