<?php
require_once 'database.php';

class SiteConfigHelper {
    private static $configCache = null;
    private static $db = null;
    
    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    private static function loadConfig() {
        if (self::$configCache === null) {
            $db = self::getDb();
            $allConfig = $db->getAllSiteConfig();
            self::$configCache = [];
            foreach ($allConfig as $config) {
                self::$configCache[$config['config_key']] = $config['config_value'];
            }
        }
    }
    
    /**
     * 获取网站配置值
     * @param string $key 配置键名
     * @param string $default 默认值
     * @return string 配置值
     */
    public static function getConfig($key, $default = '') {
        self::loadConfig();
        return isset(self::$configCache[$key]) ? self::$configCache[$key] : $default;
    }
    
    /**
     * 获取邮箱地址
     */
    public static function getEmail() {
        return self::getConfig('email', 'your.email@example.com');
    }
    
    /**
     * 获取GitHub链接
     */
    public static function getGithubUrl() {
        return self::getConfig('github_url', 'https://github.com/your-username');
    }
    
    /**
     * 获取微信二维码路径
     */
    public static function getWechatQr() {
        return self::getConfig('wechat_qr', './static/img/wechat.jpg');
    }
    
    /**
     * 获取Google Scholar链接
     */
    public static function getGoogleScholar() {
        return self::getConfig('google_scholar', 'https://scholar.google.com/');
    }
    
    /**
     * 获取ORCID链接
     */
    public static function getOrcid() {
        return self::getConfig('orcid', 'https://orcid.org/0000-0000-0000-0000');
    }
    
    /**
     * 获取地理位置
     */
    public static function getLocation() {
        return self::getConfig('location', 'China-Hainan');
    }
    
    /**
     * 获取教育信息
     */
    public static function getEducationInfo() {
        return self::getConfig('education_info', 'MUC & CityU MAC');
    }
    
    /**
     * 获取个人标签数组
     */
    public static function getPersonalTags() {
        $tags = self::getConfig('personal_tags', '大二牲,AI,LLM,麦门,爱睡觉');
        return array_map('trim', explode(',', $tags));
    }
    
    /**
     * 获取时间线事件数组
     */
    public static function getTimelineEvents() {
        $events = self::getConfig('timeline_events', '转世成为异世界美男|2004.10;发现异界没有五险一金|2010.01;被精灵公主倒追导致王国通货膨胀|2018.11;教兽人用PPT汇报工作|2025.11;异界黑马学院开业啦！|2030.06;35岁被学院优化|2039.04;和史莱姆一起投简历|- now');
        $eventItems = explode(';', $events);
        $result = [];
        foreach ($eventItems as $item) {
            $parts = explode('|', $item);
            if (count($parts) >= 2) {
                $result[] = [
                    'event' => trim($parts[0]),
                    'date' => trim($parts[1])
                ];
            }
        }
        return $result;
    }
    
    /**
     * 获取欢迎语姓名
     */
    public static function getWelcomeName() {
        return self::getConfig('welcome_name', 'Your Name');
    }
    
    /**
     * 获取欢迎描述1
     */
    public static function getWelcomeDescription1() {
        return self::getConfig('welcome_description1', '😊 Jack of all trades, Master of None');
    }
    
    /**
     * 获取欢迎描述2
     */
    public static function getWelcomeDescription2() {
        return self::getConfig('welcome_description2', '🤗 Life was like a box of Chocolate, you Never know what you\'re gonna get.');
    }
    
    /**
     * 获取每日名言字符串
     */
    public static function getDailyQuotes() {
        return self::getConfig('daily_quotes', '生活就像骑自行车，要保持平衡就得不断前进。;我不是一个特别有天赋的人，只是对问题特别好奇而已。;学习是一种态度，不是能力。;未来完全取决于你现在的努力。;人生如同写代码，看似结束的地方，其实是新的起点。');
    }
    
    /**
     * 获取网站标题
     */
    public static function getSiteTitle() {
        return self::getConfig('site_title', 'Your Site Title');
    }
    
    /**
     * 获取页脚文本
     */
    public static function getFooterText() {
        return self::getConfig('footer_text', 'Your Name &copy; 2025 | ID: 12345678');
    }
}
?> 