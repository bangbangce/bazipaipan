# PHP八字排盘插件

一个纯PHP实现的八字排盘插件，无需依赖任何框架，支持公农历转换、真太阳时计算。

## 功能特性

- ✅ 公历转农历 / 农历转公历
- ✅ 真太阳时计算（支持300+中国城市）
- ✅ 四柱（年柱、月柱、日柱、时柱）干支计算
- ✅ 五行属性查询
- ✅ 纳音五行
- ✅ 十神关系计算
- ✅ 长生十二宫
- ✅ 生肖查询
- ✅ 无框架依赖，纯PHP实现

## 安装

```bash
composer require bazi/bazi-plugin
```

## 快速开始

### 创建八字排盘

```php
<?php
require 'vendor/autoload.php';

use Bazipaipan\Bazi;

$chart = Bazi::create([
    'name' => '张三',
    'year' => 1990,
    'month' => 5,
    'day' => 15,
    'hour' => 10,
    'minute' => 30,
    'isLunar' => false,      // 是否农历
    'isSolarTime' => true,   // 是否使用真太阳时
    'birthPlace' => '北京',   // 出生地点
    'gender' => 'male'
]);

// 获取完整结果
$result = $chart->toArray();
print_r($result);

// 只获取八字
$bazi = $chart->getBazi();
// 输出: ['year' => '庚午', 'month' => '辛巳', 'day' => '己巳', 'hour' => '己巳']

// 获取四柱详细信息
$fourColumns = $chart->getFourColumns();

// 获取五行统计
$wuXingCount = $chart->getWuXingCount();
// 输出: ['金' => 2, '木' => 0, '水' => 0, '火' => 4, '土' => 2]
```

### 农历转公历

```php
$solar = Bazi::lunarToSolar(1990, 5, 15);
// 输出: ['year' => 1990, 'month' => 6, 'day' => 7]
```

### 公历转农历

```php
$lunar = Bazi::solarToLunar(1990, 6, 7);
// 输出: ['year' => 1990, 'month' => 5, 'day' => 15, 'isLeap' => false]
```

### 计算真太阳时

```php
$solarTime = Bazi::calcSolarTime(1990, 5, 15, 10, 30, 0, 116.4074);
```

### 获取支持的城市列表

```php
$cities = Bazi::getSupportedCities();
$location = Bazi::getCityLocation('北京');
// 输出: ['lng' => 116.4074, 'lat' => 39.9042]
```

## 返回数据结构

```php
[
    'basic' => [...],           // 基本信息（姓名、性别、出生地）
    'solarDateTime' => [...],   // 公历时间
    'lunarDateTime' => [...],   // 农历时间
    'fourColumns' => [...],     // 四柱详细信息
    'bazi' => [...],            // 八字（四柱干支）
    'dayMaster' => [...],       // 日主信息
    'animalSign' => '马',       // 生肖
    'wuXingCount' => [...],     // 五行统计
    'ganRelations' => [...],    // 十神关系
    'changShengList' => [...],  // 长生十二宫
]
```

## 在ThinkPHP中使用

将bazi目录复制到 `extend/` 目录下即可直接使用：

```php
use Bazipaipan\Bazi;

$chart = Bazi::create([...]);
```

## 在Laravel中使用

```bash
composer require bazi/bazi-plugin
```

```php
use Bazipaipan\Bazi;

$chart = Bazi::create([...]);
```

## 要求

- PHP >= 5.6.0

## License

MIT License