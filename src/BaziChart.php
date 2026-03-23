<?php

namespace Bazipaipan;

/**
 * 八字排盘核心类
 * 
 * 该类是八字排盘插件的核心类，负责：
 * - 接收用户输入的出生信息
 * - 进行公历/农历转换
 * - 计算真太阳时（如果需要）
 * - 计算四柱（年柱、月柱、日柱、时柱）的干支
 * - 提供各种八字信息的获取方法
 * 
 * 使用示例：
 * ```php
 * $chart = new BaziChart([
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
 * ```
 */
class BaziChart
{
    /**
     * 用户基本信息
     * @var string 姓名
     */
    private $name;

    /**
     * @var string 性别（male/female）
     */
    private $gender;

    /**
     * 用户输入的出生日期时间
     * @var int 出生年份
     */
    private $birthYear;

    /**
     * @var int 出生月份
     */
    private $birthMonth;

    /**
     * @var int 出生日期
     */
    private $birthDay;

    /**
     * @var int 出生小时
     */
    private $birthHour;

    /**
     * @var int 出生分钟
     */
    private $birthMinute;

    /**
     * @var int 出生秒
     */
    private $birthSecond;

    /**
     * @var bool 是否农历
     */
    private $isLunar;

    /**
     * @var bool 是否使用真太阳时
     */
    private $isSolarTime;

    /**
     * @var string 出生地点
     */
    private $birthPlace;

    /**
     * @var float 经度
     */
    private $longitude;

    /**
     * @var float 纬度
     */
    private $latitude;

    /**
     * 公历日期时间（经过转换后的实际计算用日期）
     * @var int 公历年份
     */
    private $solarYear;

    /**
     * @var int 公历月份
     */
    private $solarMonth;

    /**
     * @var int 公历日期
     */
    private $solarDay;

    /**
     * @var int 公历小时
     */
    private $solarHour;

    /**
     * @var int 公历分钟
     */
    private $solarMinute;

    /**
     * @var int 公历秒
     */
    private $solarSecond;

    /**
     * 农历日期时间
     * @var int 农历年份
     */
    private $lunarYear;

    /**
     * @var int 农历月份
     */
    private $lunarMonth;

    /**
     * @var int 农历日期
     */
    private $lunarDay;

    /**
     * @var bool 是否闰月
     */
    private $isLeapMonth;

    /**
     * 四柱干支信息
     * @var array 年柱干支
     */
    private $yearGanZhi;

    /**
     * @var array 月柱干支
     */
    private $monthGanZhi;

    /**
     * @var array 日柱干支
     */
    private $dayGanZhi;

    /**
     * @var array 时柱干支
     */
    private $hourGanZhi;

    /**
     * 日主信息（日柱的天干，是八字分析的核心）
     * @var string 日主天干
     */
    private $dayGan;

    /**
     * @var string 日主地支
     */
    private $dayZhi;

    /**
     * 构造函数
     * 
     * 初始化八字排盘，接收用户输入的参数并进行计算
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
     */
    public function __construct(array $params)
    {
        // 保存用户输入的基本信息
        $this->name = isset($params['name']) ? $params['name'] : '';
        $this->gender = isset($params['gender']) ? $params['gender'] : 'male';
        $this->birthYear = (int)$params['year'];
        $this->birthMonth = (int)$params['month'];
        $this->birthDay = (int)$params['day'];
        $this->birthHour = isset($params['hour']) ? (int)$params['hour'] : 0;
        $this->birthMinute = isset($params['minute']) ? (int)$params['minute'] : 0;
        $this->birthSecond = isset($params['second']) ? (int)$params['second'] : 0;
        $this->isLunar = isset($params['isLunar']) ? (bool)$params['isLunar'] : false;
        $this->isSolarTime = isset($params['isSolarTime']) ? (bool)$params['isSolarTime'] : false;
        $this->birthPlace = isset($params['birthPlace']) ? $params['birthPlace'] : '';
        $this->isLeapMonth = isset($params['isLeapMonth']) ? (bool)$params['isLeapMonth'] : false;

        // 执行初始化和计算
        $this->initLocation();
        $this->calcDateTime();
        $this->calcGanZhi();
    }

    /**
     * 初始化地理位置
     * 
     * 根据出生地点获取经纬度信息
     * 如果没有指定地点或地点无法识别，使用默认值（东经120度，北纬30度）
     */
    private function initLocation()
    {
        if ($this->birthPlace) {
            // 尝试从城市数据中获取经纬度
            $location = SolarTime::getCityLocation($this->birthPlace);
            if ($location) {
                $this->longitude = $location['lng'];
                $this->latitude = $location['lat'];
            }
        }

        // 如果无法获取位置信息，使用默认值
        if (!isset($this->longitude)) {
            $this->longitude = isset($this->longitude) ? $this->longitude : 120;
            $this->latitude = isset($this->latitude) ? $this->latitude : 30;
        }
    }

    /**
     * 计算日期时间
     * 
     * 处理以下转换：
     * 1. 农历转公历（如果输入是农历）
     * 2. 公历转农历（如果输入是公历）
     * 3. 真太阳时计算（如果启用）
     */
    private function calcDateTime()
    {
        if ($this->isLunar) {
            // 输入是农历，转换为公历
            $this->lunarYear = $this->birthYear;
            $this->lunarMonth = $this->birthMonth;
            $this->lunarDay = $this->birthDay;

            // 农历转公历
            $solar = Lunar::toSolar($this->lunarYear, $this->lunarMonth, $this->lunarDay, $this->isLeapMonth);
            $this->solarYear = $solar['year'];
            $this->solarMonth = $solar['month'];
            $this->solarDay = $solar['day'];
        } else {
            // 输入是公历，转换为农历
            $this->solarYear = $this->birthYear;
            $this->solarMonth = $this->birthMonth;
            $this->solarDay = $this->birthDay;

            // 公历转农历
            $lunar = Lunar::toLunar($this->solarYear, $this->solarMonth, $this->solarDay);
            $this->lunarYear = $lunar['year'];
            $this->lunarMonth = $lunar['month'];
            $this->lunarDay = $lunar['day'];
            $this->isLeapMonth = $lunar['isLeap'];
        }

        // 真太阳时计算
        if ($this->isSolarTime && $this->birthPlace) {
            // 根据经度计算真太阳时
            $solarTime = SolarTime::calcSolarTime(
                $this->solarYear,
                $this->solarMonth,
                $this->solarDay,
                $this->birthHour,
                $this->birthMinute,
                $this->birthSecond,
                $this->longitude
            );
            $this->solarYear = $solarTime['year'];
            $this->solarMonth = $solarTime['month'];
            $this->solarDay = $solarTime['day'];
            $this->solarHour = $solarTime['hour'];
            $this->solarMinute = $solarTime['minute'];
            $this->solarSecond = $solarTime['second'];
        } else {
            // 不使用真太阳时，直接使用输入时间
            $this->solarHour = $this->birthHour;
            $this->solarMinute = $this->birthMinute;
            $this->solarSecond = $this->birthSecond;
        }
    }

    /**
     * 计算四柱干支
     * 
     * 根据公历日期时间计算年柱、月柱、日柱、时柱的干支
     */
    private function calcGanZhi()
    {
        // 计算年柱
        $this->yearGanZhi = GanZhi::getYearGanZhi($this->solarYear);

        // 计算月柱
        $this->monthGanZhi = GanZhi::getMonthGanZhi($this->solarYear, $this->solarMonth);

        // 计算日柱
        $this->dayGanZhi = GanZhi::getDayGanZhi($this->solarYear, $this->solarMonth, $this->solarDay);
        $this->dayGan = $this->dayGanZhi['gan'];
        $this->dayZhi = $this->dayGanZhi['zhi'];

        // 计算时柱（需要日干作为参数）
        $this->hourGanZhi = GanZhi::getHourGanZhi($this->dayGan, $this->solarHour);
    }

    /**
     * 获取基本信息
     * 
     * @return array 包含姓名、性别、出生地、经纬度的数组
     */
    public function getBasicInfo()
    {
        return [
            'name' => $this->name,
            'gender' => $this->gender,
            'birthPlace' => $this->birthPlace,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];
    }

    /**
     * 获取公历日期时间
     * 
     * @return array 包含年月日时分秒及格式化字符串的数组
     */
    public function getSolarDateTime()
    {
        return [
            'year' => $this->solarYear,
            'month' => $this->solarMonth,
            'day' => $this->solarDay,
            'hour' => $this->solarHour,
            'minute' => $this->solarMinute,
            'second' => $this->solarSecond,
            'formatted' => sprintf('%04d年%02d月%02d日 %02d时%02d分', $this->solarYear, $this->solarMonth, $this->solarDay, $this->solarHour, $this->solarMinute),
        ];
    }

    /**
     * 获取农历日期时间
     * 
     * @return array 包含农历年月日、是否闰月及格式化字符串的数组
     */
    public function getLunarDateTime()
    {
        // 获取农历月份和日期的中文名称
        $monthName = Lunar::getLunarMonthName($this->lunarMonth);
        $dayName = Lunar::getLunarDayName($this->lunarDay);
        $leapStr = $this->isLeapMonth ? '闰' : '';

        return [
            'year' => $this->lunarYear,
            'month' => $this->lunarMonth,
            'day' => $this->lunarDay,
            'isLeap' => $this->isLeapMonth,
            'formatted' => sprintf('%04d年%s%s月%s', $this->lunarYear, $leapStr, $monthName, $dayName),
        ];
    }

    /**
     * 获取四柱详细信息
     * 
     * 返回年柱、月柱、日柱、时柱的完整信息，包括：
     * - 天干、地支
     * - 五行属性
     * - 纳音
     * - 十神关系
     * - 长生十二宫
     * - 阴阳属性
     * 
     * @return array 四柱信息数组
     */
    public function getFourColumns()
    {
        return [
            'year' => $this->getColumnInfo($this->yearGanZhi),
            'month' => $this->getColumnInfo($this->monthGanZhi),
            'day' => $this->getColumnInfo($this->dayGanZhi),
            'hour' => $this->getColumnInfo($this->hourGanZhi),
        ];
    }

    /**
     * 获取单柱的详细信息
     * 
     * @param array $ganZhi 干支数组，包含gan和zhi
     * @return array 该柱的详细信息
     */
    private function getColumnInfo($ganZhi)
    {
        $gan = $ganZhi['gan'];
        $zhi = $ganZhi['zhi'];
        $ganZhiStr = $gan . $zhi;

        return [
            'gan' => $gan,                              // 天干
            'zhi' => $zhi,                              // 地支
            'ganZhi' => $ganZhiStr,                     // 干支组合
            'ganWuXing' => GanZhi::getWuXing($gan),     // 天干五行
            'zhiWuXing' => GanZhi::getDiZhiWuXing($zhi), // 地支五行
            'naYin' => GanZhi::getNaYin($ganZhiStr),    // 纳音
            'ganRelation' => GanZhi::getGanRelation($this->dayGan, $gan), // 与日主的十神关系
            'changSheng' => GanZhi::getChangSheng($this->dayGan, $zhi),   // 长生十二宫
            'isYangGan' => GanZhi::isYangGan($gan),     // 天干阴阳
            'isYangZhi' => GanZhi::isYangZhi($zhi),     // 地支阴阳
        ];
    }

    /**
     * 获取八字（四柱干支）
     * 
     * @return array 包含年柱、月柱、日柱、时柱干支的数组
     */
    public function getBazi()
    {
        return [
            'year' => $this->yearGanZhi['gan'] . $this->yearGanZhi['zhi'],
            'month' => $this->monthGanZhi['gan'] . $this->monthGanZhi['zhi'],
            'day' => $this->dayGanZhi['gan'] . $this->dayGanZhi['zhi'],
            'hour' => $this->hourGanZhi['gan'] . $this->hourGanZhi['zhi'],
        ];
    }

    /**
     * 获取日主信息
     * 
     * 日主是八字分析的核心，即日柱的天干
     * 
     * @return array 日主信息，包含天干、地支、五行、阴阳属性
     */
    public function getDayMaster()
    {
        return [
            'gan' => $this->dayGan,
            'zhi' => $this->dayZhi,
            'wuXing' => GanZhi::getWuXing($this->dayGan),
            'isYang' => GanZhi::isYangGan($this->dayGan),
        ];
    }

    /**
     * 获取生肖
     * 
     * @return string 生肖名称，如"鼠"、"牛"等
     */
    public function getAnimalSign()
    {
        return Lunar::getAnimals($this->solarYear);
    }

    /**
     * 获取五行统计
     * 
     * 统计八字中金木水火土的出现次数
     * 
     * @return array 五行计数数组，如['金' => 2, '木' => 1, ...]
     */
    public function getWuXingCount()
    {
        $wuXingCount = ['金' => 0, '木' => 0, '水' => 0, '火' => 0, '土' => 0];

        // 遍历四柱，统计五行
        $columns = [$this->yearGanZhi, $this->monthGanZhi, $this->dayGanZhi, $this->hourGanZhi];

        foreach ($columns as $column) {
            // 统计天干五行
            $ganWX = GanZhi::getWuXing($column['gan']);
            // 统计地支五行
            $zhiWX = GanZhi::getDiZhiWuXing($column['zhi']);

            if ($ganWX) {
                $wuXingCount[$ganWX]++;
            }
            if ($zhiWX) {
                $wuXingCount[$zhiWX]++;
            }
        }

        return $wuXingCount;
    }

    /**
     * 获取四柱天干与日主的十神关系
     * 
     * @return array 四柱十神关系数组
     */
    public function getGanRelations()
    {
        $relations = [];
        $columns = [
            'year' => $this->yearGanZhi,
            'month' => $this->monthGanZhi,
            'day' => $this->dayGanZhi,
            'hour' => $this->hourGanZhi,
        ];

        foreach ($columns as $key => $column) {
            $relations[$key] = GanZhi::getGanRelation($this->dayGan, $column['gan']);
        }

        return $relations;
    }

    /**
     * 获取四柱地支的长生十二宫
     * 
     * @return array 四柱长生十二宫数组
     */
    public function getChangShengList()
    {
        $list = [];
        $columns = [
            'year' => $this->yearGanZhi,
            'month' => $this->monthGanZhi,
            'day' => $this->dayGanZhi,
            'hour' => $this->hourGanZhi,
        ];

        foreach ($columns as $key => $column) {
            $list[$key] = GanZhi::getChangSheng($this->dayGan, $column['zhi']);
        }

        return $list;
    }

    /**
     * 获取完整的八字排盘结果
     * 
     * 返回所有八字信息的综合数组
     * 
     * @return array 完整的八字排盘结果
     */
    public function toArray()
    {
        return [
            'basic' => $this->getBasicInfo(),           // 基本信息
            'solarDateTime' => $this->getSolarDateTime(), // 公历日期时间
            'lunarDateTime' => $this->getLunarDateTime(), // 农历日期时间
            'fourColumns' => $this->getFourColumns(),   // 四柱详细信息
            'bazi' => $this->getBazi(),                 // 八字（四柱干支）
            'dayMaster' => $this->getDayMaster(),       // 日主信息
            'animalSign' => $this->getAnimalSign(),     // 生肖
            'wuXingCount' => $this->getWuXingCount(),   // 五行统计
            'ganRelations' => $this->getGanRelations(), // 十神关系
            'changShengList' => $this->getChangShengList(), // 长生十二宫
        ];
    }
}
