<?php

namespace Bazipaipan;

/**
 * 农历与公历转换类
 * 
 * 该类提供了农历和公历之间的相互转换功能，使用寿星天文历算法
 * 支持年份范围：1900年-2100年
 */
class Lunar
{
    /**
     * 农历数据表
     * 
     * 每个数据包含以下信息（使用位运算解析）：
     * - 1-4位：表示闰月大小，0表示无闰月
     * - 5-16位：表示12个月的大小月，1为大月30天，0为小月29天
     * - 17-20位：表示闰月是哪个月，0表示无闰月
     * 
     * @var array
     */
    private static $lunarInfo = [
        0x04bd8, 0x04ae0, 0x0a570, 0x054d5, 0x0d260, 0x0d950, 0x16554, 0x056a0, 0x09ad0, 0x055d2,
        0x04ae0, 0x0a5b6, 0x0a4d0, 0x0d250, 0x1d255, 0x0b540, 0x0d6a0, 0x0ada2, 0x095b0, 0x14977,
        0x04970, 0x0a4b0, 0x0b4b5, 0x06a50, 0x06d40, 0x1ab54, 0x02b60, 0x09570, 0x052f2, 0x04970,
        0x06566, 0x0d4a0, 0x0ea50, 0x06e95, 0x05ad0, 0x02b60, 0x186e3, 0x092e0, 0x1c8d7, 0x0c950,
        0x0d4a0, 0x1d8a6, 0x0b550, 0x056a0, 0x1a5b4, 0x025d0, 0x092d0, 0x0d2b2, 0x0a950, 0x0b557,
        0x06ca0, 0x0b550, 0x15355, 0x04da0, 0x0a5b0, 0x14573, 0x052b0, 0x0a9a8, 0x0e950, 0x06aa0,
        0x0aea6, 0x0ab50, 0x04b60, 0x0aae4, 0x0a570, 0x05260, 0x0f263, 0x0d950, 0x05b57, 0x056a0,
        0x096d0, 0x04dd5, 0x04ad0, 0x0a4d0, 0x0d4d4, 0x0d250, 0x0d558, 0x0b540, 0x0b6a0, 0x195a6,
        0x095b0, 0x049b0, 0x0a974, 0x0a4b0, 0x0b27a, 0x06a50, 0x06d40, 0x0af46, 0x0ab60, 0x09570,
        0x04af5, 0x04970, 0x064b0, 0x074a3, 0x0ea50, 0x06b58, 0x055c0, 0x0ab60, 0x096d5, 0x092e0,
        0x0c960, 0x0d954, 0x0d4a0, 0x0da50, 0x07552, 0x056a0, 0x0abb7, 0x025d0, 0x092d0, 0x0cab5,
        0x0a950, 0x0b4a0, 0x0baa4, 0x0ad50, 0x055d9, 0x04ba0, 0x0a5b0, 0x15176, 0x052b0, 0x0a930,
        0x07954, 0x06aa0, 0x0ad50, 0x05b52, 0x04b60, 0x0a6e6, 0x0a4e0, 0x0d260, 0x0ea65, 0x0d530,
        0x05aa0, 0x076a3, 0x096d0, 0x04afb, 0x04ad0, 0x0a4d0, 0x1d0b6, 0x0d250, 0x0d520, 0x0dd45,
        0x0b5a0, 0x056d0, 0x055b2, 0x049b0, 0x0a577, 0x0a4b0, 0x0aa50, 0x1b255, 0x06d20, 0x0ada0,
        0x14b63, 0x09370, 0x049f8, 0x04970, 0x064b0, 0x168a6, 0x0ea50, 0x06b20, 0x1a6c4, 0x0aae0,
        0x0a2e0, 0x0d2e3, 0x0c960, 0x0d557, 0x0d4a0, 0x0da50, 0x05d55, 0x056a0, 0x0a6d0, 0x055d4,
        0x052d0, 0x0a9b8, 0x0a950, 0x0b4a0, 0x0b6a6, 0x0ad50, 0x055a0, 0x0aba4, 0x0a5b0, 0x052b0,
        0x0b273, 0x06930, 0x07337, 0x06aa0, 0x0ad50, 0x14b55, 0x04b60, 0x0a570, 0x054e4, 0x0d160,
        0x0e968, 0x0d520, 0x0daa0, 0x16aa6, 0x056d0, 0x04ae0, 0x0a9d4, 0x0a2d0, 0x0d150, 0x0f252,
        0x0d520,
    ];

    /**
     * 公历每个月的天数（平年）
     * @var array
     */
    private static $solarMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    /**
     * 天干
     * 甲、乙、丙、丁、戊、己、庚、辛、壬、癸
     * @var array
     */
    private static $tianGan = ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'];

    /**
     * 地支
     * 子、丑、寅、卯、辰、巳、午、未、申、酉、戌、亥
     * @var array
     */
    private static $diZhi = ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];

    /**
     * 十二生肖
     * @var array
     */
    private static $animals = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'];

    /**
     * 农历月份名称
     * @var array
     */
    private static $lunarMonthName = ['正', '二', '三', '四', '五', '六', '七', '八', '九', '十', '冬', '腊'];

    /**
     * 农历日期名称
     * @var array
     */
    private static $lunarDayName = [
        '初一', '初二', '初三', '初四', '初五', '初六', '初七', '初八', '初九', '初十',
        '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '二十',
        '廿一', '廿二', '廿三', '廿四', '廿五', '廿六', '廿七', '廿八', '廿九', '三十',
    ];

    /**
     * 根据索引获取天干
     * 
     * @param int $index 天干索引（0-9）
     * @return string 天干字符
     */
    public static function getTianGan($index)
    {
        return self::$tianGan[$index % 10];
    }

    /**
     * 根据索引获取地支
     * 
     * @param int $index 地支索引（0-11）
     * @return string 地支字符
     */
    public static function getDiZhi($index)
    {
        return self::$diZhi[$index % 12];
    }

    /**
     * 根据年份获取生肖
     * 
     * @param int $year 公历年份
     * @return string 生肖名称
     */
    public static function getAnimals($year)
    {
        return self::$animals[($year - 4) % 12];
    }

    /**
     * 获取农历月份的中文名称
     * 
     * @param int $month 农历月份（1-12）
     * @return string 月份名称
     */
    public static function getLunarMonthName($month)
    {
        return self::$lunarMonthName[$month - 1];
    }

    /**
     * 获取农历日期的中文名称
     * 
     * @param int $day 农历日期（1-30）
     * @return string 日期名称
     */
    public static function getLunarDayName($day)
    {
        return self::$lunarDayName[$day - 1];
    }

    /**
     * 获取农历某年的总天数
     * 
     * 通过解析农历数据表，计算指定年份的总天数
     * 
     * @param int $y 农历年份
     * @return int 该年的总天数
     */
    private static function lYearDays($y)
    {
        // 基础天数为348天（12个月×29天）
        $sum = 348;
        // 遍历数据位，计算每月是否为大月
        for ($i = 0x8000; $i > 0x8; $i >>= 1) {
            $sum += (self::$lunarInfo[$y - 1900] & $i) ? 1 : 0;
        }
        // 加上闰月天数
        return $sum + self::leapDays($y);
    }

    /**
     * 获取农历某年闰月的月份
     * 
     * @param int $y 农历年份
     * @return int 闰月月份（1-12），0表示该年无闰月
     */
    private static function leapMonth($y)
    {
        return self::$lunarInfo[$y - 1900] & 0xf;
    }

    /**
     * 获取农历某年闰月的天数
     * 
     * @param int $y 农历年份
     * @return int 闰月天数（29或30），0表示该年无闰月
     */
    private static function leapDays($y)
    {
        if (self::leapMonth($y)) {
            // 检查闰月大小月标志位
            return (self::$lunarInfo[$y - 1900] & 0x10000) ? 30 : 29;
        }
        return 0;
    }

    /**
     * 获取农历某年某月的天数
     * 
     * @param int $y 农历年份
     * @param int $m 农历月份（1-12）
     * @return int 该月天数（29或30）
     */
    private static function monthDays($y, $m)
    {
        return (self::$lunarInfo[$y - 1900] & (0x10000 >> $m)) ? 30 : 29;
    }

    /**
     * 将农历日期转换为公历日期
     * 
     * 算法原理：
     * 1. 以1900年1月31日（农历1900年正月初一）为基准点
     * 2. 计算从基准点到目标农历日期的总天数
     * 3. 将总天数加到基准日期上，得到公历日期
     * 
     * @param int $lunarYear 农历年份
     * @param int $lunarMonth 农历月份（1-12）
     * @param int $lunarDay 农历日期（1-30）
     * @param bool $isLeapMonth 是否为闰月，默认false
     * @return array 转换后的公历日期 ['year'=>年, 'month'=>月, 'day'=>日]
     */
    public static function toSolar($lunarYear, $lunarMonth, $lunarDay, $isLeapMonth = false)
    {
        if ($lunarYear < 1900 || $lunarYear > 2100) {
            throw new \InvalidArgumentException('农历年份必须在1900-2100年之间');
        }
        if ($lunarMonth < 1 || $lunarMonth > 12) {
            throw new \InvalidArgumentException('农历月份必须在1-12之间');
        }
        if ($lunarDay < 1 || $lunarDay > 30) {
            throw new \InvalidArgumentException('农历日期必须在1-30之间');
        }

        $offset = 0;
        $leap = self::leapMonth($lunarYear);
        $isLeap = false;

        // 累加从1900年到目标年份之前所有年份的天数
        for ($i = 1900; $i < $lunarYear; $i++) {
            $offset += self::lYearDays($i);
        }

        // 累加目标年份中从正月到目标月份之前所有月份的天数
        for ($i = 1; $i < $lunarMonth; $i++) {
            // 处理闰月的情况
            if ($leap == $i && !$isLeap) {
                $offset += self::leapDays($lunarYear);
            }
            $offset += self::monthDays($lunarYear, $i);
        }

        // 如果是闰月，需要额外加上正常月份的天数
        if ($isLeapMonth && $leap == $lunarMonth) {
            $offset += self::monthDays($lunarYear, $lunarMonth);
        }

        // 加上当月的天数（减1是因为日期从1开始）
        $offset += $lunarDay - 1;

        // 以1900年1月31日为基准，计算公历日期
        $baseDate = new \DateTime('1900-01-31');
        $baseDate->modify('+' . $offset . ' days');

        return [
            'year' => (int)$baseDate->format('Y'),
            'month' => (int)$baseDate->format('m'),
            'day' => (int)$baseDate->format('d'),
        ];
    }

    /**
     * 将公历日期转换为农历日期
     * 
     * 算法原理：
     * 1. 计算公历日期与基准日期（1900年1月31日）之间的天数差
     * 2. 从1900年开始，逐年减去每年的天数，直到天数差小于某年的总天数
     * 3. 再从正月开始，逐月减去每月的天数，直到确定月份和日期
     * 
     * @param int $solarYear 公历年份
     * @param int $solarMonth 公历月份（1-12）
     * @param int $solarDay 公历日期（1-31）
     * @return array 转换后的农历日期 ['year'=>年, 'month'=>月, 'day'=>日, 'isLeap'=>是否闰月]
     */
    public static function toLunar($solarYear, $solarMonth, $solarDay)
    {
        if ($solarYear < 1900 || $solarYear > 2100) {
            throw new \InvalidArgumentException('公历年份必须在1900-2100年之间');
        }
        if ($solarMonth < 1 || $solarMonth > 12) {
            throw new \InvalidArgumentException('公历月份必须在1-12之间');
        }
        if ($solarDay < 1 || $solarDay > 31) {
            throw new \InvalidArgumentException('公历日期必须在1-31之间');
        }

        // 基准日期：1900年1月31日（农历1900年正月初一）
        $baseDate = new \DateTime('1900-01-31');
        $objDate = new \DateTime(sprintf('%04d-%02d-%02d', $solarYear, $solarMonth, $solarDay));

        // 计算天数差
        $offset = $baseDate->diff($objDate)->days;

        $temp = 0;
        $isLeap = false;

        // 逐年减去天数，确定农历年份
        for ($i = 1900; $i < 2101 && $offset > 0; $i++) {
            $temp = self::lYearDays($i);
            $offset -= $temp;
        }

        // 如果减多了，需要回退
        if ($offset < 0) {
            $offset += $temp;
            $i--;
        }

        $lunarYear = $i;
        $leap = self::leapMonth($i);
        $isLeap = false;

        // 逐月减去天数，确定农历月份和日期
        for ($i = 1; $i < 13 && $offset > 0; $i++) {
            // 处理闰月
            if ($leap > 0 && $i == ($leap + 1) && !$isLeap) {
                --$i;
                $isLeap = true;
                $temp = self::leapDays($lunarYear);
            } else {
                $temp = self::monthDays($lunarYear, $i);
            }

            // 闰月处理完毕后恢复标志
            if ($isLeap && $i == ($leap + 1)) {
                $isLeap = false;
            }

            $offset -= $temp;
        }

        // 特殊情况：刚好是闰月的第一天
        if ($offset == 0 && $leap > 0 && $i == $leap + 1) {
            if ($isLeap) {
                $isLeap = false;
            } else {
                $isLeap = true;
                --$i;
            }
        }

        // 如果减多了，需要回退
        if ($offset < 0) {
            $offset += $temp;
            --$i;
        }

        $lunarMonth = $i;
        $lunarDay = $offset + 1;  // 日期从1开始

        return [
            'year' => $lunarYear,
            'month' => $lunarMonth,
            'day' => $lunarDay,
            'isLeap' => $isLeap,
        ];
    }
}
