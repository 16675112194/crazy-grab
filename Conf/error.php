<?php
/*
 * +----------------------------------------------------------------------
 * | xf9.com 幸福9号
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014 http://www.xf9.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: haowenhui <haowenhui@vacn.com.cn>
 * +----------------------------------------------------------------------
 * | 错误配置文件
 */

return array(
    /* E_USER_ERROR E_ALL E_USER_NOTICE */
    'ERROR_LIVE' => array(E_USER_ERROR, E_PARSE), # 一般级别错误记录

    /* E_ERROR E_CORE_ERROR E_PARSE */
    'FATAL_ERROR_LIVE' => array(E_ERROR, E_CORE_ERROR, E_PARSE, E_USER_ERROR, E_COMPILE_ERROR), # 致命级别错误记录
    'ERROR' => # 所有错误消息集合
    array(
        1 => '调用的类不存在.',
        2 => '调用的类方法不存在.',
        3 => '未知异常.',
        4 => '调用方式不正确.',
        5 => '参数异常',
        6 => '代码发生严重错误!',
        7 => '执行过程异常抛出!',
        8 => '系统执行异常.',
        9 => '返回消息未定义',
        10 => '请求未授权',
		11 => '代码执行过程发生致命错误',

		/* 100-200 支付相关返回消息 */
		103 => '参数异常查询失败',
		104 => '交易已关闭,无需再次关闭',
		105 => '关闭失败,该笔交易不存在或其他原因',

        1001 => '搜索关键字不能为空',
        1002 => '用户名或密码参数不能为空',
        1003 => '密码不正确',
        1004 => '对不起,该用户组帐号暂时不允许登录!',
        1005 => '是否为普通商品属性参数错误',
		1006 => '用户名长度应在1-30位之间',
        1007 => '邮箱格式不正确',
        1008 => '邮箱长度应在1-32位之间',
        1009 => '用户名已存在',
        1010 => '邮箱已被使用',
        1011 => '手机号码已被使用',
        1012 => '该用户没有此地址',
        1013 => '地址不存在',
    	1014 => '尚未设置交易密码',
    	1015 => '用户名不存在',
        1016 => '手机号码与原号码相同',
    	
    		
    	1101 => '没有此商品',
		
		1201 => '推荐人不存在',
    	
    	1301 => '操作金额大于可用金额',
    	1302 => '账户资金异常',
    	1303 => '修改失败',
    		
    		
    	2001=>'必要的参数没有全部正确的提供',
    	2002=>'要更新的字段数据不能全部为空',
    	2003=>'密码只允许6-30位的数字和字母组合',
    	2004=>'原密码输入错误, 请检查',
    	2005=>'手机号码格式输入不正确, 请检查',
    		
    	2014=>'请求的数据不存在或者已经被删除',
    	2015=>'商品数据不存在或者已经被移除',
    		
    	//单件商品购买限制--liujing
    	3001=>'商品已售罄',
    	3002=>'商品库存不足, 您可以修改购买数量后再试',
    	3003=>'商品已经下架, 暂时无法购买',
    	3004=>'该商品单次最多只能购买{#buyCount}件',
    	3005=>'该商品每人最多只能购买{#buyCount}件',
    	3006=>'秒杀尚未开始, 请稍后再试',
    	3007=>'秒杀已结束, 无法继续购买',
    	
    	//购物车购买限制--liujing
    	3101=>'对不起, 您的购物车还没有任何商品',
    	3102=>'{#goodsName}无法购买! 原因:{#message}',
    	
    	//商品下单结算相关--liujing
    	3201=>'对不起, 请不要频繁做这样的恶意操作',
    	3202=>'对不起, 收货地址保存失败! 原因:{#message}',
    	3203=>'临时订单[{#toid}]不存在或者不属于当前用户',
    	3204=>'支付方式尚未支持, 请重新选择',
    	3205=>'您选择的订单提交方式不正确',
    	3206=>'订单提交失败, 您的购物车商品无法成功获取! 原因:{#message}',
    	3207=>'您的购物车为空, 无法订单提交',
    	3208=>'商品{#goodsName}太热门了, 暂时无法购买! ',		//数据库内数据产生混乱, goods/seckill_goods/special_goods和goods_att表内数据无法对应
    	
    	//待付款订单操作相关
    	3301=>'指定的订单不存在或已经被删除',
    	3302=>'指定的订单已经支付, 无法取消',
    	3303=>'指定的订单已经取消, 无需重复操作',
    	3304=>'同步已支付订单失败, 原因:{#message}',
    	
    	4001=>'短信验证码验证失败',
    	4002=>'短信发送间隔为120秒',
        5001=>'邮件验证已失效',
    	5002=>'邮件发送间隔为120秒',
        6001=>'不能进行退货操作',
    	6002=>'退货商品不存在',
        6003=>'退货商品超过总数量',
        6004=>'该商品已经申请过退货',
        6005=>'该订单不存在',
    	//极品错误码, 用于提示所有无法正确描述的情况--liujing
    	9999=>'对不起, 系统繁忙, 请再试一次或者稍后重试'
    ),
);

