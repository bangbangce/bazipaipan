<?php

namespace Bazi;

/**
 * 天干地支计算类
 * 
 * 该类提供了天干地支（干支）的各种计算功能，包括：
 * - 年柱、月柱、日柱、时柱的干支计算
 * - 五行属性查询
 * - 纳音五行查询
 * - 十神关系计算
 * - 长生十二宫查询
 * - 阴阳属性判断
 */
class GanZhi
{
    /**
     * 十天干
     * 甲、乙、丙、丁、戊、己、庚、辛、壬、癸
     * @var array
     */
    private static $tianGan = ['甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'];

    /**
     * 十二地支
     * 子、丑、寅、卯、辰、巳、午、未、申、酉、戌、亥
     * @var array
     */
    private static $diZhi = ['子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];

    /**
     * 天干五行属性
     * 甲乙属木、丙丁属火、戊己属土、庚辛属金、壬癸属水
     * @var array
     */
    private static $wuXing = [
        '甲' => '木', '乙' => '木', '丙' => '火', '丁' => '火', '戊' => '土',
        '己' => '土', '庚' => '金', '辛' => '金', '壬' => '水', '癸' => '水',
    ];

    /**
     * 地支五行属性
     * 寅卯属木、巳午属火、申酉属金、亥子属水、辰戌丑未属土
     * @var array
     */
    private static $diZhiWuXing = [
        '子' => '水', '丑' => '土', '寅' => '木', '卯' => '木', '辰' => '土', '巳' => '火',
        '午' => '火', '未' => '土', '申' => '金', '酉' => '金', '戌' => '土', '亥' => '水',
    ];

    /**
     * 六十纳音表
     * 
     * 纳音是将六十甲子与五行相配的一种方法，每两个干支对应一个纳音
     * 例如：甲子、乙丑对应"海中金"
     * 
     * @var array
     */
    private static $naYin = [
        '甲子' => '海中金', '乙丑' => '海中金', '丙寅' => '炉中火', '丁卯' => '炉中火',
        '戊辰' => '大林木', '己巳' => '大林木', '庚午' => '路旁土', '辛未' => '路旁土',
        '壬申' => '剑锋金', '癸酉' => '剑锋金', '甲戌' => '山头火', '乙亥' => '山头火',
        '丙子' => '涧下水', '丁丑' => '涧下水', '戊寅' => '城头土', '己卯' => '城头土',
        '庚辰' => '白蜡金', '辛巳' => '白蜡金', '壬午' => '杨柳木', '癸未' => '杨柳木',
        '甲申' => '泉中水', '乙酉' => '泉中水', '丙戌' => '屋上土', '丁亥' => '屋上土',
        '戊子' => '霹雳火', '己丑' => '霹雳火', '庚寅' => '松柏木', '辛卯' => '松柏木',
        '壬辰' => '长流水', '癸巳' => '长流水', '甲午' => '沙中金', '乙未' => '沙中金',
        '丙申' => '山下火', '丁酉' => '山下火', '戊戌' => '平地木', '己亥' => '平地木',
        '庚子' => '壁上土', '辛丑' => '壁上土', '壬寅' => '金箔金', '癸卯' => '金箔金',
        '甲辰' => '覆灯火', '乙巳' => '覆灯火', '丙午' => '天河水', '丁未' => '天河水',
        '戊申' => '大驿土', '己酉' => '大驿土', '庚戌' => '钗钏金', '辛亥' => '钗钏金',
        '壬子' => '桑柘木', '癸丑' => '桑柘木', '甲寅' => '大溪水', '乙卯' => '大溪水',
        '丙辰' => '沙中土', '丁巳' => '沙中土', '戊午' => '天上火', '己未' => '天上火',
        '庚申' => '石榴木', '辛酉' => '石榴木', '壬戌' => '大海水', '癸亥' => '大海水',
    ];

    /**
     * 十二长生表
     * 
     * 根据日主天干，列出其在十二地支中的长生状态
     * 十二长生依次为：长生、沐浴、冠带、临官、帝旺、衰、病、死、墓、绝、胎、养
     * 
     * @var array
     */
    private static $changSheng = [
        '甲' => ['亥', '子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌'],
        '乙' => ['午', '巳', '辰', '卯', '寅', '丑', '子', '亥', '戌', '酉', '申', '未'],
        '丙' => ['寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥', '子', '丑'],
        '丁' => ['酉', '申', '未', '午', '巳', '辰', '卯', '寅', '丑', '子', '亥', '戌'],
        '戊' => ['寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥', '子', '丑'],
        '己' => ['酉', '申', '未', '午', '巳', '辰', '卯', '寅', '丑', '子', '亥', '戌'],
        '庚' => ['巳', '午', '未', '申', '酉', '戌', '亥', '子', '丑', '寅', '卯', '辰'],
        '辛' => ['子', '亥', '戌', '酉', '申', '未', '午', '巳', '辰', '卯', '寅', '丑'],
        '壬' => ['申', '酉', '戌', '亥', '子', '丑', '寅', '卯', '辰', '巳', '午', '未'],
        '癸' => ['卯', '寅', '丑', '子', '亥', '戌', '酉', '申', '未', '午', '巳', '辰'],
    ];

    /**
     * 十二长生名称
     * @var array
     */
    private static $changShengName = ['长生', '沐浴', '冠带', '临官', '帝旺', '衰', '病', '死', '墓', '绝', '胎', '养'];

    /**
     * 天干十神关系表
     * 
     * 十神是八字命理中描述天干之间关系的术语：
     * - 比肩：与日主同阴阳同五行
     * - 劫财：与日主异阴阳同五行
     * - 食神：日主所生，同阴阳
     * - 伤官：日主所生，异阴阳
     * - 偏财：日主所克，同阴阳
     * - 正财：日主所克，异阴阳
     * - 偏官（七杀）：克日主，同阴阳
     * - 正官：克日主，异阴阳
     * - 偏印（枭神）：生日主，同阴阳
     * - 正印：生日主，异阴阳
     * 
     * @var array
     */
    private static $ganRelation = [
        '甲' => ['比肩' => '甲', '劫财' => '乙', '食神' => '丙', '伤官' => '丁', '偏财' => '戊', '正财' => '己', '偏官' => '庚', '正官' => '辛', '偏印' => '壬', '正印' => '癸'],
        '乙' => ['比肩' => '乙', '劫财' => '甲', '食神' => '丁', '伤官' => '丙', '偏财' => '己', '正财' => '戊', '偏官' => '辛', '正官' => '庚', '偏印' => '癸', '正印' => '壬'],
        '丙' => ['比肩' => '丙', '劫财' => '丁', '食神' => '戊', '伤官' => '己', '偏财' => '庚', '正财' => '辛', '偏官' => '壬', '正官' => '癸', '偏印' => '甲', '正印' => '乙'],
        '丁' => ['比肩' => '丁', '劫财' => '丙', '食神' => '己', '伤官' => '戊', '偏财' => '辛', '正财' => '庚', '偏官' => '癸', '正官' => '壬', '偏印' => '乙', '正印' => '甲'],
        '戊' => ['比肩' => '戊', '劫财' => '己', '食神' => '庚', '伤官' => '辛', '偏财' => '壬', '正财' => '癸', '偏官' => '甲', '正官' => '乙', '偏印' => '丙', '正印' => '丁'],
        '己' => ['比肩' => '己', '劫财' => '戊', '食神' => '辛', '伤官' => '庚', '偏财' => '癸', '正财' => '壬', '偏官' => '乙', '正官' => '甲', '偏印' => '丁', '正印' => '丙'],
        '庚' => ['比肩' => '庚', '劫财' => '辛', '食神' => '壬', '伤官' => '癸', '偏财' => '甲', '正财' => '乙', '偏官' => '丙', '正官' => '丁', '偏印' => '戊', '正印' => '己'],
        '辛' => ['比肩' => '辛', '劫财' => '庚', '食神' => '癸', '伤官' => '壬', '偏财' => '乙', '正财' => '甲', '偏官' => '丁', '正官' => '丙', '偏印' => '己', '正印' => '戊'],
        '壬' => ['比肩' => '壬', '劫财' => '癸', '食神' => '甲', '伤官' => '乙', '偏财' => '丙', '正财' => '丁', '偏官' => '戊', '正官' => '己', '偏印' => '庚', '正印' => '辛'],
        '癸' => ['比肩' => '癸', '劫财' => '壬', '食神' => '乙', '伤官' => '甲', '偏财' => '丁', '正财' => '丙', '偏官' => '己', '正官' => '戊', '偏印' => '辛', '正印' => '庚'],
    ];

    /**
     * 计算年柱干支
     * 
     * 年柱以立春为界，这里简化使用公历年份计算
     * 算法：以公元4年为甲子年，每60年一个循环
     * 
     * @param int $year 公历年份
     * @return array 包含gan(天干)、zhi(地支)、ganIndex、zhiIndex的数组
     */
    public static function getYearGanZhi($year)
    {
        // 公元4年为甲子年，所以减去4
        $ganIndex = ($year - 4) % 10;
        $zhiIndex = ($year - 4) % 12;

        return [
            'gan' => self::$tianGan[$ganIndex],
            'zhi' => self::$diZhi[$zhiIndex],
            'ganIndex' => $ganIndex,
            'zhiIndex' => $zhiIndex,
        ];
    }

    /**
     * 计算月柱干支
     * 
     * 月柱地支固定：寅(1月)、卯(2月)、辰(3月)...丑(12月)
     * 月柱天干根据年干推算（五虎遁年起月法）：
     * - 甲己年起丙寅
     * - 乙庚年起戊寅
     * - 丙辛年起庚寅
     * - 丁壬年起壬寅
     * - 戊癸年起甲寅
     * 
     * @param int $year 公历年份
     * @param int $month 公历月份（1-12）
     * @return array 包含gan(天干)、zhi(地支)、ganIndex、zhiIndex的数组
     */
    public static function getMonthGanZhi($year, $month)
    {
        // 获取年干
        $yearGan = self::getYearGanZhi($year)['gan'];
        $yearGanIndex = array_search($yearGan, self::$tianGan);

        // 根据年干确定月干的起始位置（五虎遁）
        // 年干为甲(0)或己(5)时，月起丙寅，丙的索引为2
        // 年干为乙(1)或庚(6)时，月起戊寅，戊的索引为4
        // 以此类推
        $baseGanIndex = ($yearGanIndex % 5) * 2;
        $monthGanIndex = ($baseGanIndex + $month - 1) % 10;

        // 月支固定：寅(1月)、卯(2月)...丑(12月)
        // 寅的索引为2，所以(month + 1) % 12
        $monthZhiIndex = ($month + 1) % 12;

        return [
            'gan' => self::$tianGan[$monthGanIndex],
            'zhi' => self::$diZhi[$monthZhiIndex],
            'ganIndex' => $monthGanIndex,
            'zhiIndex' => $monthZhiIndex,
        ];
    }

    /**
     * 计算日柱干支
     * 
     * 使用基准日法计算：
     * - 以1900年1月1日为基准日（癸酉日）
     * - 计算目标日期与基准日的天数差
     * - 根据天数差推算干支
     * 
     * @param int $year 公历年份
     * @param int $month 公历月份（1-12）
     * @param int $day 公历日期（1-31）
     * @return array 包含gan(天干)、zhi(地支)、ganIndex、zhiIndex的数组
     */
    public static function getDayGanZhi($year, $month, $day)
    {
        // 基准日期：1900年1月1日
        $baseDate = new \DateTime('1900-01-01');
        $targetDate = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day));
        
        // 计算天数差
        $diff = $baseDate->diff($targetDate);
        $days = $diff->days;

        // 1900年1月1日是癸酉日
        // 癸的索引为9，酉的索引为9
        // 所以天干索引 = (days + 10) % 10，地支索引 = (days + 12) % 12
        $ganIndex = ($days + 10) % 10;
        $zhiIndex = ($days + 12) % 12;

        return [
            'gan' => self::$tianGan[$ganIndex],
            'zhi' => self::$diZhi[$zhiIndex],
            'ganIndex' => $ganIndex,
            'zhiIndex' => $zhiIndex,
        ];
    }

    /**
     * 计算时柱干支
     * 
     * 时柱地支根据出生时辰确定：
     * - 子时：23:00-01:00
     * - 丑时：01:00-03:00
     * - 寅时：03:00-05:00
     * - ...以此类推
     * 
     * 时柱天干根据日干推算（五鼠遁日起时法）：
     * - 甲己日起甲子
     * - 乙庚日起丙子
     * - 丙辛日起戊子
     * - 丁壬日起庚子
     * - 戊癸日起壬子
     * 
     * @param string $dayGan 日干
     * @param int $hour 出生小时（0-23）
     * @return array 包含gan(天干)、zhi(地支)、ganIndex、zhiIndex的数组
     */
    public static function getHourGanZhi($dayGan, $hour)
    {
        $dayGanIndex = array_search($dayGan, self::$tianGan);
        
        // 根据日干确定时干的起始位置（五鼠遁）
        $baseGanIndex = ($dayGanIndex % 5) * 2;

        // 获取时辰地支索引
        $zhiIndex = self::getHourZhiIndex($hour);
        
        // 计算时干索引
        $hourGanIndex = ($baseGanIndex + $zhiIndex) % 10;

        return [
            'gan' => self::$tianGan[$hourGanIndex],
            'zhi' => self::$diZhi[$zhiIndex],
            'ganIndex' => $hourGanIndex,
            'zhiIndex' => $zhiIndex,
        ];
    }

    /**
     * 根据小时获取时辰地支索引
     * 
     * @param int $hour 小时（0-23）
     * @return int 地支索引（0-11）
     */
    public static function getHourZhiIndex($hour)
    {
        // 子时：23:00-01:00（索引0）
        if ($hour >= 23 || $hour < 1) {
            return 0;
        } elseif ($hour >= 1 && $hour < 3) {
            return 1;   // 丑时
        } elseif ($hour >= 3 && $hour < 5) {
            return 2;   // 寅时
        } elseif ($hour >= 5 && $hour < 7) {
            return 3;   // 卯时
        } elseif ($hour >= 7 && $hour < 9) {
            return 4;   // 辰时
        } elseif ($hour >= 9 && $hour < 11) {
            return 5;   // 巳时
        } elseif ($hour >= 11 && $hour < 13) {
            return 6;   // 午时
        } elseif ($hour >= 13 && $hour < 15) {
            return 7;   // 未时
        } elseif ($hour >= 15 && $hour < 17) {
            return 8;   // 申时
        } elseif ($hour >= 17 && $hour < 19) {
            return 9;   // 酉时
        } elseif ($hour >= 19 && $hour < 21) {
            return 10;  // 戌时
        } else {
            return 11;  // 亥时
        }
    }

    /**
     * 获取天干的五行属性
     * 
     * @param string $gan 天干
     * @return string|null 五行属性（金木水火土），如果天干无效则返回null
     */
    public static function getWuXing($gan)
    {
        return isset(self::$wuXing[$gan]) ? self::$wuXing[$gan] : null;
    }

    /**
     * 获取地支的五行属性
     * 
     * @param string $zhi 地支
     * @return string|null 五行属性（金木水火土），如果地支无效则返回null
     */
    public static function getDiZhiWuXing($zhi)
    {
        return isset(self::$diZhiWuXing[$zhi]) ? self::$diZhiWuXing[$zhi] : null;
    }

    /**
     * 获取干支的纳音五行
     * 
     * @param string $ganZhi 干支组合，如"甲子"
     * @return string|null 纳音五行，如"海中金"
     */
    public static function getNaYin($ganZhi)
    {
        return isset(self::$naYin[$ganZhi]) ? self::$naYin[$ganZhi] : null;
    }

    /**
     * 获取天干与日主的十神关系
     * 
     * @param string $dayGan 日主天干
     * @param string $targetGan 目标天干
     * @return string|null 十神名称（比肩、劫财、食神等）
     */
    public static function getGanRelation($dayGan, $targetGan)
    {
        if (!isset(self::$ganRelation[$dayGan])) {
            return null;
        }

        foreach (self::$ganRelation[$dayGan] as $relation => $gan) {
            if ($gan === $targetGan) {
                return $relation;
            }
        }
        return null;
    }

    /**
     * 获取日主在某地支的长生十二宫状态
     * 
     * @param string $dayGan 日主天干
     * @param string $zhi 地支
     * @return string|null 长生状态（长生、沐浴、冠带等）
     */
    public static function getChangSheng($dayGan, $zhi)
    {
        if (!isset(self::$changSheng[$dayGan])) {
            return null;
        }

        $zhiIndex = array_search($zhi, self::$diZhi);
        if ($zhiIndex === false) {
            return null;
        }

        // 查找地支在长生表中的位置
        $changShengIndex = array_search($zhi, self::$changSheng[$dayGan]);
        if ($changShengIndex === false) {
            return null;
        }

        return self::$changShengName[$changShengIndex];
    }

    /**
     * 判断天干是否为阳干
     * 
     * 甲、丙、戊、庚、壬为阳干
     * 乙、丁、己、辛、癸为阴干
     * 
     * @param string $gan 天干
     * @return bool 是否为阳干
     */
    public static function isYangGan($gan)
    {
        $index = array_search($gan, self::$tianGan);
        // 索引为偶数（0,2,4,6,8）的是阳干
        return $index !== false && $index % 2 === 0;
    }

    /**
     * 判断地支是否为阳支
     * 
     * 子、寅、辰、午、申、戌为阳支
     * 丑、卯、巳、未、酉、亥为阴支
     * 
     * @param string $zhi 地支
     * @return bool 是否为阳支
     */
    public static function isYangZhi($zhi)
    {
        $index = array_search($zhi, self::$diZhi);
        // 索引为偶数（0,2,4,6,8,10）的是阳支
        return $index !== false && $index % 2 === 0;
    }

    /**
     * 获取天干列表
     * 
     * @return array 十天干数组
     */
    public static function getTianGanList()
    {
        return self::$tianGan;
    }

    /**
     * 获取地支列表
     * 
     * @return array 十二地支数组
     */
    public static function getDiZhiList()
    {
        return self::$diZhi;
    }

    /**
     * 将干支转换为数字（用于比较和排序）
     * 
     * @param string $ganZhi 干支组合，如"甲子"
     * @return int|null 0-59的数字，如果干支无效则返回null
     */
    public static function ganZhiToInt($ganZhi)
    {
        $gan = mb_substr($ganZhi, 0, 1, 'UTF-8');
        $zhi = mb_substr($ganZhi, 1, 1, 'UTF-8');

        $ganIndex = array_search($gan, self::$tianGan);
        $zhiIndex = array_search($zhi, self::$diZhi);

        if ($ganIndex === false || $zhiIndex === false) {
            return null;
        }

        return $ganIndex * 12 + $zhiIndex;
    }

    /**
     * 将数字转换为干支
     * 
     * @param int $num 0-59的数字
     * @return string 干支组合
     */
    public static function intToGanZhi($num)
    {
        $ganIndex = $num % 10;
        $zhiIndex = $num % 12;
        return self::$tianGan[$ganIndex] . self::$diZhi[$zhiIndex];
    }
}
