<?php

namespace Bazipaipan;

/**
 * 八字排盘插件入口类
 * 
 * 该类是八字排盘插件的主入口点，提供静态方法便于其他项目调用。
 * 所有方法都是静态方法，无需实例化即可使用。
 * 
 * 使用示例：
 * ```php
 * // 方式1：使用create方法创建完整的八字排盘
 * $chart = Bazi::create([
 *     'name' => '张三',
 *     'year' => 1990,
 *     'month' => 5,
 *     'day' => 15,
 *     'hour' => 10,
 *     'minute' => 30,
 *     'isLunar' => false,
 *     'isSolarTime' => true,
 *     'birthPlace' => '北京',
 *     'gender' => 'male'
 * ]);
 * $result = $chart->toArray();
 * 
 * // 方式2：获取支持的城市列表
 * $cities = Bazi::getSupportedCities();
 * 
 * // 方式3：农历转公历
 * $solar = Bazi::lunarToSolar(1990, 5, 15, false);
 * ```
 */

// 引入所需的核心类文件
require_once __DIR__ . '/src/Lunar.php';
require_once __DIR__ . '/src/SolarTime.php';
require_once __DIR__ . '/src/GanZhi.php';
require_once __DIR__ . '/src/BaziChart.php';

/**
 * 八字排盘主类
 * 
 * 提供便捷的静态方法访问插件的各项功能
 */
class Bazi
{
    /**
     * 创建八字排盘实例
     * 
     * 这是最常用的方法，根据用户输入的出生信息创建八字排盘
     * 
     * @param array $params 参数数组，支持以下键：
     *   - name: 姓名（可选）
     *   - gender: 性别，male/female（可选，默认male）
     *   - year: 出生年份（必填）
     *   - month: 出生月份（必填）
     *   - day: 出生日期（必填）
     *   - hour: 出生小时（可选，默认0）
     *   - minute: 出生分钟（可选，默认0）
     *   - second: 出生秒（可选，默认0）
     *   - isLunar: 是否农历（可选，默认false）
     *   - isSolarTime: 是否使用真太阳时（可选，默认false）
     *   - birthPlace: 出生地点，如"北京"（可选）
     *   - isLeapMonth: 是否闰月（农历时使用，可选）
     * 
     * @return BaziChart 八字排盘实例
     * 
     * @example
     * ```php
     * $chart = Bazi::create([
     *     'name' => '张三',
     *     'year' => 1990,
     *     'month' => 5,
     *     'day' => 15,
     *     'hour' => 10,
     *     'minute' => 30,
     *     'isLunar' => false,
     *     'isSolarTime' => true,
     *     'birthPlace' => '北京',
     *     'gender' => 'male'
     * ]);
     * 
     * // 获取完整结果
     * $result = $chart->toArray();
     * 
     * // 获取八字
     * $bazi = $chart->getBazi();
     * 
     * // 获取四柱详细信息
     * $fourColumns = $chart->getFourColumns();
     * ```
     */
    public static function create(array $params)
    {
        return new BaziChart($params);
    }

    /**
     * 获取所有支持的城市列表
     * 
     * 返回插件内置的中国城市数据，包含城市名称和经纬度信息
     * 这些城市数据用于真太阳时计算
     * 
     * @return array 城市列表数组，格式为：
     *   [
     *     ['city' => '北京', 'lng' => 116.4074, 'lat' => 39.9042],
     *     ['city' => '上海', 'lng' => 121.4737, 'lat' => 31.2304],
     *     ...
     *   ]
     * 
     * @example
     * ```php
     * $cities = Bazi::getSupportedCities();
     * foreach ($cities as $city) {
     *     echo $city['city'] . ': 经度 ' . $city['lng'] . ', 纬度 ' . $city['lat'] . "\n";
     * }
     * ```
     */
    public static function getSupportedCities()
    {
        return SolarTime::getAllCities();
    }

    /**
     * 获取指定城市的经纬度
     * 
     * 根据城市名称查询其经纬度信息
     * 
     * @param string $cityName 城市名称，如"北京"、"上海"、"广州"等
     * @return array|null 如果找到则返回包含lng和lat的数组，否则返回null
     * 
     * @example
     * ```php
     * $location = Bazi::getCityLocation('北京');
     * // 返回: ['lng' => 116.4074, 'lat' => 39.9042]
     * ```
     */
    public static function getCityLocation($cityName)
    {
        return SolarTime::getCityLocation($cityName);
    }

    /**
     * 农历转公历
     * 
     * 将农历日期转换为公历日期
     * 
     * @param int $lunarYear 农历年份
     * @param int $lunarMonth 农历月份（1-12）
     * @param int $lunarDay 农历日期（1-30）
     * @param bool $isLeapMonth 是否闰月，默认false
     * @return array 公历日期数组，格式为：
     *   ['year' => 1990, 'month' => 6, 'day' => 7]
     * 
     * @example
     * ```php
     * // 将农历1990年五月初一转换为公历
     * $solar = Bazi::lunarToSolar(1990, 5, 1);
     * // 返回: ['year' => 1990, 'month' => 5, 'day' => 24]
     * 
     * // 如果是闰五月
     * $solar = Bazi::lunarToSolar(1990, 5, 1, true);
     * ```
     */
    public static function lunarToSolar($lunarYear, $lunarMonth, $lunarDay, $isLeapMonth = false)
    {
        return Lunar::toSolar($lunarYear, $lunarMonth, $lunarDay, $isLeapMonth);
    }

    /**
     * 公历转农历
     * 
     * 将公历日期转换为农历日期
     * 
     * @param int $solarYear 公历年份
     * @param int $solarMonth 公历月份（1-12）
     * @param int $solarDay 公历日期（1-31）
     * @return array 农历日期数组，格式为：
     *   ['year' => 1990, 'month' => 5, 'day' => 1, 'isLeap' => false]
     * 
     * @example
     * ```php
     * // 将公历1990年5月24日转换为农历
     * $lunar = Bazi::solarToLunar(1990, 5, 24);
     * // 返回: ['year' => 1990, 'month' => 5, 'day' => 1, 'isLeap' => false]
     * ```
     */
    public static function solarToLunar($solarYear, $solarMonth, $solarDay)
    {
        return Lunar::toLunar($solarYear, $solarMonth, $solarDay);
    }

    /**
     * 计算真太阳时
     * 
     * 根据当地时间、日期和经度计算真太阳时
     * 真太阳时是根据太阳在天空中的实际位置确定的时间，
     * 与当地经度有关，对于八字排盘的精确计算很重要
     * 
     * @param int $year 公历年份
     * @param int $month 公历月份（1-12）
     * @param int $day 公历日期（1-31）
     * @param int $hour 小时（0-23）
     * @param int $minute 分钟（0-59）
     * @param int $second 秒（0-59）
     * @param float $longitude 经度（东经为正，西经为负）
     * @return array 真太阳时数组，格式为：
     *   ['year' => 1990, 'month' => 5, 'day' => 24, 'hour' => 11, 'minute' => 30, 'second' => 0]
     * 
     * @example
     * ```php
     * // 计算北京地区1990年5月24日10:30的真太阳时
     * $solarTime = Bazi::calcSolarTime(1990, 5, 24, 10, 30, 0, 116.4074);
     * // 北京经度约116.4度，真太阳时会比北京时间晚约14分钟
     * ```
     */
    public static function calcSolarTime($year, $month, $day, $hour, $minute, $second, $longitude)
    {
        return SolarTime::calcSolarTime($year, $month, $day, $hour, $minute, $second, $longitude);
    }
}
