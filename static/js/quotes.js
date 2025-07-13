/**
 * 每日名言功能
 * 从数据库获取名言并显示在页面上
 */

/**
 * 初始化每日名言功能
 */
function initDailyQuotes() {
    const quoteBox = document.querySelector('.quote-box');
    if (!quoteBox) return;
    
    // 获取名言数据
    fetchDailyQuotes();
    
    // 点击刷新按钮更新名言
    const refreshButton = quoteBox.querySelector('.quote-refresh button');
    if (refreshButton) {
        refreshButton.addEventListener('click', fetchDailyQuotes);
    }
}

/**
 * 从服务器获取每日名言
 */
function fetchDailyQuotes() {
    const quoteBox = document.querySelector('.quote-box');
    if (!quoteBox) return;
    
    const quoteContent = quoteBox.querySelector('.quote-content');
    if (!quoteContent) return;
    
    // 显示加载状态
    quoteContent.textContent = '加载中...';
    quoteContent.style.opacity = 0.7;
    
    // 发送AJAX请求获取名言
    fetch('get_daily_quotes.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.quotes && data.quotes.length > 0) {
                // 随机选择一条名言
                const randomIndex = Math.floor(Math.random() * data.quotes.length);
                const quote = data.quotes[randomIndex];
                
                // 显示名言
                displayQuote(quoteContent, quote.content);
            } else {
                // 如果获取失败，显示默认名言
                displayQuote(quoteContent, '生活就像骑自行车，要保持平衡就得不断前进。');
            }
        })
        .catch(error => {
            console.error('获取名言失败:', error);
            // 显示默认名言
            displayQuote(quoteContent, '生活就像骑自行车，要保持平衡就得不断前进。');
        });
}

/**
 * 显示名言
 * @param {HTMLElement} quoteContent - 名言容器元素
 * @param {string} content - 名言内容
 */
function displayQuote(quoteContent, content) {
    if (!quoteContent) return;
    
    // 淡出效果
    quoteContent.style.opacity = 0;
    
    setTimeout(() => {
        // 更新内容
        quoteContent.textContent = content;
        
        // 淡入效果
        quoteContent.style.transition = 'opacity 0.5s ease';
        quoteContent.style.opacity = 1;
    }, 200);
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', function() {
    initDailyQuotes();
}); 