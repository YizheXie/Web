<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'database.php';
require_once 'site_config_helper.php';

try {
    // 获取每日名言
    $quotes = SiteConfigHelper::getDailyQuotes();
    
    if (!empty($quotes)) {
        // 将名言字符串转换为数组
        $quoteArray = array_map('trim', explode(';', $quotes));
        $quoteArray = array_filter($quoteArray); // 移除空值
        
        // 转换为对象数组格式
        $quotesData = [];
        foreach ($quoteArray as $quote) {
            $quotesData[] = ['content' => $quote];
        }
        
        echo json_encode([
            'success' => true,
            'quotes' => $quotesData
        ]);
    } else {
        // 如果没有名言，返回默认名言
        echo json_encode([
            'success' => true,
            'quotes' => [
                ['content' => '生活就像骑自行车，要保持平衡就得不断前进。'],
                ['content' => '我不是一个特别有天赋的人，只是对问题特别好奇而已。'],
                ['content' => '学习是一种态度，不是能力。'],
                ['content' => '未来完全取决于你现在的努力。'],
                ['content' => '人生如同写代码，看似结束的地方，其实是新的起点。']
            ]
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => '获取名言失败',
        'message' => $e->getMessage()
    ]);
}
?> 