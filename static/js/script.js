/**
 * 全局脚本，包含主题切换、文章交互、分页、过滤、搜索、弹窗等功能。
 */

// --- 基本设置与事件监听 ---

/**
 * 禁止浏览器默认的右键菜单。
 */
document.addEventListener('contextmenu', function (event) {
    event.preventDefault();
});

// --- 项目卡片按压效果 ---

/**
 * 处理卡片按下状态。
 * @param {Event} event - 触发事件。
 */
function handlePress(event) {
    this.classList.add('pressed');
}

/**
 * 处理卡片释放状态。
 * @param {Event} event - 触发事件。
 */
function handleRelease(event) {
    this.classList.remove('pressed');
}

/**
 * 处理卡片取消状态（例如鼠标移出）。
 * @param {Event} event - 触发事件。
 */
function handleCancel(event) {
    this.classList.remove('pressed');
}

// 为所有 .projectItem 添加按压效果监听器
const projectItems = document.querySelectorAll('.projectItem');
projectItems.forEach(function (button) {
    button.addEventListener('mousedown', handlePress);
    button.addEventListener('mouseup', handleRelease);
    button.addEventListener('mouseleave', handleCancel);
    button.addEventListener('touchstart', handlePress);
    button.addEventListener('touchend', handleRelease);
    button.addEventListener('touchcancel', handleCancel);
});

// --- 工具函数 ---

/**
 * 切换指定选择器元素的类名。
 * @param {string} selector - CSS 选择器。
 * @param {string} className - 需要切换的类名。
 */
function toggleClass(selector, className) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(function (element) {
        element.classList.toggle(className);
    });
}

/**
 * 弹出或关闭图片查看器。
 * @param {string} [imageURL] - 可选，要显示的图片 URL。如果省略则关闭弹窗。
 */
function pop(imageURL) {
    const tcMainElement = document.querySelector(".tc-img");
    if (imageURL) {
        tcMainElement.src = imageURL;
    }
    toggleClass(".tc-main", "active");
    toggleClass(".tc", "active");
}

// 图片查看器点击事件监听
const tcElement = document.querySelector('.tc');
const tcMainElement = document.querySelector('.tc-main');
if (tcElement && tcMainElement) {
    tcElement.addEventListener('click', function (event) {
        pop();
    });
    tcMainElement.addEventListener('click', function (event) {
        event.stopPropagation();
    });
}

// --- Cookie 处理函数 ---

/**
 * 设置 Cookie。
 * @param {string} name - Cookie 名称。
 * @param {string} value - Cookie 值。
 * @param {number} days - Cookie 有效天数。
 */
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * 获取 Cookie。
 * @param {string} name - Cookie 名称。
 * @returns {string|null} Cookie 的值，如果不存在则返回 null。
 */
function getCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(nameEQ) === 0) {
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}

// --- DOM 加载完成后的初始化 ---

/**
 * DOM 内容加载完成后执行的主初始化函数。
 */
document.addEventListener('DOMContentLoaded', function () {
    // 顶部导航栏切换功能
    initTopNavigation();
    
    // 主题切换功能
    initThemeToggle();
    
    // 文章展开/收起功能
    initBlogExpand();
    
    // 博客搜索、分类和分页功能
    initBlogFeatures();
    
    // 代码高亮初始化
    initCodeHighlighting();
    
    // 移动端分类菜单功能
    initMobileCategoryMenu();
    
    // 随机名言功能
    initRandomQuotes();
    
    // 联系表单验证功能
    initContactForm();
});

/**
 * 初始化联系表单验证功能。
 */
function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;
    
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    
    // 添加实时验证
    if (nameInput) {
        nameInput.addEventListener('blur', () => validateName());
        nameInput.addEventListener('input', () => clearError('nameError'));
    }
    
    if (emailInput) {
        emailInput.addEventListener('blur', () => validateEmail());
        emailInput.addEventListener('input', () => clearError('emailError'));
    }
    
    if (messageInput) {
        messageInput.addEventListener('blur', () => validateMessage());
        messageInput.addEventListener('input', () => clearError('messageError'));
    }
    
    // 表单提交验证
    form.addEventListener('submit', handleFormSubmit);
}

/**
 * 验证姓名字段。
 */
function validateName() {
    const nameInput = document.getElementById('name');
    const errorElement = document.getElementById('nameError');
    const name = nameInput.value.trim();
    
    if (!name) {
        showError(errorElement, '姓名不能为空');
        return false;
    }
    
    if (name.length < 2) {
        showError(errorElement, '姓名至少需要2个字符');
        return false;
    }
    
    if (name.length > 50) {
        showError(errorElement, '姓名不能超过50个字符');
        return false;
    }
    
    if (!/^[\u4e00-\u9fa5a-zA-Z\s]+$/.test(name)) {
        showError(errorElement, '姓名只能包含中英文字符和空格');
        return false;
    }
    
    clearError('nameError');
    return true;
}

/**
 * 验证邮箱字段。
 */
function validateEmail() {
    const emailInput = document.getElementById('email');
    const errorElement = document.getElementById('emailError');
    const email = emailInput.value.trim();
    
    if (!email) {
        showError(errorElement, '邮箱不能为空');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError(errorElement, '请输入有效的邮箱地址');
        return false;
    }
    
    if (email.length > 100) {
        showError(errorElement, '邮箱地址不能超过100个字符');
        return false;
    }
    
    clearError('emailError');
    return true;
}

/**
 * 验证留言字段。
 */
function validateMessage() {
    const messageInput = document.getElementById('message');
    const errorElement = document.getElementById('messageError');
    const message = messageInput.value.trim();
    
    if (!message) {
        showError(errorElement, '留言不能为空');
        return false;
    }
    
    if (message.length < 10) {
        showError(errorElement, '留言至少需要10个字符');
        return false;
    }
    
    if (message.length > 1000) {
        showError(errorElement, '留言不能超过1000个字符');
        return false;
    }
    
    clearError('messageError');
    return true;
}

/**
 * 显示错误信息。
 */
function showError(errorElement, message) {
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

/**
 * 清除错误信息。
 */
function clearError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

/**
 * 处理表单提交。
 */
function handleFormSubmit(e) {
    e.preventDefault();
    
    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isMessageValid = validateMessage();
    
    if (isNameValid && isEmailValid && isMessageValid) {
        // 禁用提交按钮，防止重复提交
        const submitBtn = document.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = '发送中...';
        }
        
        // 发送数据到PHP后端
        const formData = new FormData(document.getElementById('contactForm'));
        
        fetch('contact_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // 重置表单
                document.getElementById('contactForm').reset();
                // 清除所有错误信息
                clearError('nameError');
                clearError('emailError');
                clearError('messageError');
            } else {
                alert('❌ ' + data.message);
                // 显示服务器端验证错误
                if (data.errors) {
                    data.errors.forEach(error => {
                        console.log('Server validation error:', error);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ 发送失败，请检查网络连接或稍后重试。');
        })
        .finally(() => {
            // 恢复提交按钮
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = '发送消息';
            }
        });
    }
}

/**
 * 初始化顶部导航栏交互功能。
 */
function initTopNavigation() {
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function() {
            navToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
        
        // 点击导航链接后关闭菜单
        const links = navLinks.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                // 仅在移动视图下执行
                if (window.innerWidth <= 768) {
                    navToggle.classList.remove('active');
                    navLinks.classList.remove('active');
                }
            });
        });
        
        // 监听窗口大小变化，在大屏幕上重置导航栏状态
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                navToggle.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
        
        // 添加滚动淡出效果
        const topNav = document.querySelector('.top-nav');
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // 检查滚动方向
            if (scrollTop > lastScrollTop && scrollTop > 30) {
                // 向下滚动超过30px时，淡出导航栏
                topNav.style.opacity = "0";
                topNav.style.transform = "translateY(-100%)";
            } else {
                // 向上滚动或在顶部附近时，显示导航栏
                topNav.style.opacity = "1";
                topNav.style.transform = "translateY(0)";
            }
            
            lastScrollTop = scrollTop;
        });
    }
}

/**
 * 初始化主题切换功能（浅色/暗色）。
 */
function initThemeToggle() {
    const html = document.querySelector('html');
    let themeState = getCookie("themeState") || "Light";

    function changeTheme(theme) {
        html.dataset.theme = theme;
        setCookie("themeState", theme, 365);
        themeState = theme;
    }

    const checkbox = document.getElementById('myonoffswitch');
    if (checkbox) {
        checkbox.addEventListener('change', function () {
            if (themeState === "Dark") {
                changeTheme("Light");
            } else {
                changeTheme("Dark");
            }
        });

        if (themeState === "Dark") {
            checkbox.checked = false;
        }
    }

    changeTheme(themeState);
}

/**
 * 初始化博客文章展开/收起功能。
 */
function initBlogExpand() {
    /**
     * 全局可访问的切换函数。
     * @param {HTMLElement} element - 被点击的博客文章元素 (.blog-post)。
     */
    window.toggleBlogExpand = function(element) {
        const content = element.querySelector('.blog-content');
        
        if (element.classList.contains('blog-collapsed')) {
            expandBlogPost(element, content);
        } else {
            collapseBlogPost(element, content);
        }
    };
    
    /**
     * 展开博客文章。
     * @param {HTMLElement} element - 文章元素。
     * @param {HTMLElement} content - 内容元素。
     */
    function expandBlogPost(element, content) {
        // 展开前，先设置过渡结束后的最终高度
        const actualHeight = content.scrollHeight;
        
        // 记录当前滚动位置
        const scrollPos = window.scrollY;
        
        // 展开
        element.classList.remove('blog-collapsed');
        element.classList.add('blog-expanding'); // 添加中间状态
        
        // 设置实际高度
        content.style.maxHeight = actualHeight + 'px';
        
        // 完成过渡后清理
        setTimeout(() => {
            element.classList.remove('blog-expanding');
            element.classList.add('blog-expanded');
            content.style.maxHeight = ''; // 移除内联样式，使用CSS类的值
            
            // 平滑切换按钮文本
            const moreButton = element.querySelector('.blog-more');
            moreButton.style.opacity = '0';
            setTimeout(() => {
                moreButton.textContent = '收起文章';
                moreButton.style.opacity = '1';
            }, 200);
        }, 400); // 与CSS过渡时间相同
        
        // 保持滚动位置不变
        window.scrollTo(0, scrollPos);
    }
    
    /**
     * 收起博客文章。
     * @param {HTMLElement} element - 文章元素。
     * @param {HTMLElement} content - 内容元素。
     */
    function collapseBlogPost(element, content) {
        // 获取当前高度，用于平滑收起
        const currentHeight = content.scrollHeight;
        content.style.maxHeight = currentHeight + 'px';
        
        // 触发重排，确保设置了当前高度
        void content.offsetHeight;
        
        // 添加过渡中状态
        element.classList.remove('blog-expanded');
        element.classList.add('blog-collapsing');
        
        // 设置为收起高度
        content.style.maxHeight = '80px';
        
        // 平滑切换按钮文本
        const moreButton = element.querySelector('.blog-more');
        moreButton.style.opacity = '0';
        setTimeout(() => {
            moreButton.textContent = '阅读更多';
            moreButton.style.opacity = '1';
        }, 200);
        
        // 完成过渡后清理
        setTimeout(() => {
            element.classList.remove('blog-collapsing');
            element.classList.add('blog-collapsed');
            content.style.maxHeight = '';
            
            // 如果需要，平滑滚动到文章顶部
            if (window.scrollY > element.offsetTop) {
                window.scrollTo({
                    top: element.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        }, 400); // 与CSS过渡时间相同
    }
}

/**
 * 初始化博客页面的核心功能：搜索、分类和分页。
 */
function initBlogFeatures() {
    initSearch();
    initCategories();
    initPagination();
    initBlogDisplay();
}

/**
 * 初始化博客搜索功能。
 */
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchText = e.target.value.toLowerCase();
        const posts = document.querySelectorAll('.blog-post');
        let foundAny = false;
        
        posts.forEach(function(post) {
            const title = post.querySelector('.blog-title').textContent.toLowerCase();
            const content = post.querySelector('.blog-content').textContent.toLowerCase();
            
            if (title.includes(searchText) || content.includes(searchText)) {
                post.style.display = 'block';
                foundAny = true;
            } else {
                post.style.display = 'none';
            }
        });
        
        // 处理无搜索结果提示
        handleNoSearchResults(searchText, foundAny);
        
        // 如果在搜索模式下，隐藏分页
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            pagination.style.display = searchText.length > 0 ? 'none' : 'flex';
        }
        
        // 如果搜索了内容，显示所有页面的文章
        const blogPages = document.querySelectorAll('.blog-page');
        if (searchText.length > 0) {
            blogPages.forEach(function(page) {
                page.style.display = 'block';
            });
        } else {
            // 恢复原始分页状态
            blogPages.forEach(function(page, index) {
                page.style.display = (index === 0) ? 'block' : 'none';
            });
            
            // 重置分页链接
            const paginationLinks = document.querySelectorAll('.pagination a');
            paginationLinks.forEach(function(link, index) {
                link.classList.toggle('active', index === 0);
            });
        }
    });
}

/**
 * 处理无搜索结果提示显示逻辑。
 * @param {string} searchText - 搜索文本
 * @param {boolean} foundAny - 是否找到任何结果
 */
function handleNoSearchResults(searchText, foundAny) {
    let noResultsElement = document.querySelector('.no-search-results-js');
    
    if (searchText.length > 0 && !foundAny) {
        // 如果搜索了内容但没有找到结果，显示提示
        if (!noResultsElement) {
            noResultsElement = createNoResultsElement();
            const blogPage = document.querySelector('.blog-page');
            if (blogPage) {
                blogPage.appendChild(noResultsElement);
            }
        }
        
        // 更新搜索词显示
        const searchTermElement = noResultsElement.querySelector('.search-term');
        if (searchTermElement) {
            searchTermElement.textContent = searchText;
        }
        
        noResultsElement.style.display = 'block';
    } else {
        // 如果有结果或没有搜索，隐藏提示
        if (noResultsElement) {
            noResultsElement.style.display = 'none';
        }
    }
}

/**
 * 创建无搜索结果提示元素。
 * @returns {HTMLElement} 无搜索结果提示元素
 */
function createNoResultsElement() {
    const noResultsDiv = document.createElement('div');
    noResultsDiv.className = 'no-search-results no-search-results-js';
    noResultsDiv.innerHTML = `
        <div class="no-results-icon">
            <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <path d="M448 384c-8.832 0-16-7.168-16-16s7.168-16 16-16 16 7.168 16 16-7.168 16-16 16m0-64c-26.496 0-48 21.504-48 48s21.504 48 48 48 48-21.504 48-48-21.504-48-48-48m128 64c-8.832 0-16-7.168-16-16s7.168-16 16-16 16 7.168 16 16-7.168 16-16 16m0-64c-26.496 0-48 21.504-48 48s21.504 48 48 48 48-21.504 48-48-21.504-48-48-48M304 824c4.736 0 9.408-2.016 12.576-5.856l124.64-148.544c2.048-2.432 3.136-5.504 3.136-8.704 0-6.592-4.864-12.224-11.392-13.632l-28.64-6.176c-10.144-2.24-20.64-3.392-31.2-3.392-70.688 0-128 57.312-128 128 0 5.12 0.32 10.176 0.832 15.2l2.112 30.688c0.64 9.248 8.352 16.416 17.6 16.416h38.336zM720 824c4.736 0 9.408-2.016 12.576-5.856l124.64-148.544c2.048-2.432 3.136-5.504 3.136-8.704 0-6.592-4.864-12.224-11.392-13.632l-28.64-6.176c-10.144-2.24-20.64-3.392-31.2-3.392-70.688 0-128 57.312-128 128 0 5.12 0.32 10.176 0.832 15.2l2.112 30.688c0.64 9.248 8.352 16.416 17.6 16.416h38.336z" p-id="7834"></path>
            </svg>
        </div>
        <h3>暂无搜索结果</h3>
        <p>没有找到与 "<span class="search-term"></span>" 相关的文章</p>
        <div class="no-results-suggestions">
            <p>建议您：</p>
            <ul>
                <li>检查输入的关键词是否正确</li>
                <li>尝试更换关键词重新搜索</li>
                <li>使用更简单的关键词</li>
            </ul>
        </div>
        <div class="no-results-actions">
            <a href="#" class="btn btn-primary" onclick="document.getElementById('searchInput').value=''; document.getElementById('searchInput').dispatchEvent(new Event('input')); return false;">清除搜索</a>
        </div>
    `;
    return noResultsDiv;
}

/**
 * 初始化博客分类过滤功能。
 */
function initCategories() {
    // 获取所有分类链接和文章
    const categoryLinks = document.querySelectorAll('.category-link');
    const allPosts = document.querySelectorAll('.blog-post');
    const pagination = document.querySelector('.pagination');
    
    if (!categoryLinks.length) return;
    
    // 计算各分类文章数量
    updateCategoryCounts(categoryLinks, allPosts);
    
    // 处理分类点击事件
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // 检查是否是blog.php页面（有href属性且不是javascript:void(0)）
            const href = this.getAttribute('href');
            if (href && href !== 'javascript:void(0)' && href.includes('blog.php')) {
                // 允许默认跳转行为，不阻止
                return;
            }
            
            e.preventDefault();
            
            // 更新活动状态
            updateActiveState(categoryLinks, this);
            
            // 获取选中的分类
            const selectedCategory = this.getAttribute('data-category');
            
            if (selectedCategory === 'all') {
                handleAllCategory();
            } else {
                handleSpecificCategory(selectedCategory, allPosts, pagination);
            }
            
            // 重置搜索框
            resetSearch();
            
            // 平滑滚动到顶部
            scrollToTop();
        });
    });
}

/**
 * 更新分类链接上的文章计数显示。
 * @param {NodeListOf<Element>} categoryLinks - 分类链接元素列表。
 * @param {NodeListOf<Element>} allPosts - 所有博客文章元素列表。
 */
function updateCategoryCounts(categoryLinks, allPosts) {
    // 记录每个分类的文章数量（用于显示正确的计数）
    const categoryCounts = {
        all: 0,
        tech: 0,
        life: 0,
        reading: 0
    };
    
    // 统计每个分类的文章数量
    allPosts.forEach(function(post) {
        // 跳过功能演示文章（data-id为5的是功能演示文章）
        if (post.getAttribute('data-id') === '114514') {
            return;
        }
        
        categoryCounts.all++;
        const category = post.getAttribute('data-category');
        if (category && categoryCounts[category] !== undefined) {
            categoryCounts[category]++;
        }
    });
    
    // 更新分类计数显示
    categoryLinks.forEach(function(link) {
        const category = link.getAttribute('data-category');
        const countSpan = link.querySelector('.category-count');
        if (countSpan && categoryCounts[category] !== undefined) {
            countSpan.textContent = '(' + categoryCounts[category] + ')';
        }
    });
}

/**
 * 更新一组元素中的活动状态（移除其他元素的 active 类，为目标元素添加 active 类）。
 * @param {NodeListOf<Element>} elements - 元素列表。
 * @param {HTMLElement} activeElement - 要设为活动状态的元素。
 */
function updateActiveState(elements, activeElement) {
    elements.forEach(function(element) {
        element.classList.remove('active');
    });
    activeElement.classList.add('active');
}

/**
 * 处理选择"全部文章"分类的逻辑。
 */
function handleAllCategory() {
    // 设置body属性，控制功能演示区域的显示
    document.body.setAttribute('data-category', 'all');

    // 仅显示第一页
    const blogPages = document.querySelectorAll('.blog-page');
    blogPages.forEach(function(page, index) {
        page.style.display = (index === 0) ? 'block' : 'none';
    });
    
    // 重置分页链接状态
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(function(pLink, index) {
        pLink.classList.toggle('active', index === 0);
    });
    
    // 显示分页控件
    const pagination = document.querySelector('.pagination');
    if (pagination) {
        pagination.style.display = 'flex';
    }

    // 处理功能演示区域的显示
    const demoSections = document.querySelectorAll('.demo-section');
    demoSections.forEach(function(section) {
        // 确保功能演示在全部文章分类中是可见的
        section.style.display = 'block';
        section.style.opacity = '1';
        
        // 触发布局重绘以解决可能的显示问题
        void section.offsetHeight;
    });
    
    // 确保功能演示文章可见
    const demoPost = document.querySelector('.blog-post[data-id="114514"]');
    if (demoPost && window.getComputedStyle(demoPost).display === 'none') {
        demoPost.style.display = 'block';
    }
    
    // 显示第一页的所有文章，使用新的动画效果
    const firstPagePosts = document.querySelector('.blog-page[data-page="1"]').querySelectorAll('.blog-post');
    animateArticles(firstPagePosts);
}

/**
 * 处理选择特定分类的逻辑。
 * @param {string} category - 选中的分类标识。
 * @param {NodeListOf<Element>} allPosts - 所有博客文章元素列表。
 * @param {HTMLElement|null} pagination - 分页元素。
 */
function handleSpecificCategory(category, allPosts, pagination) {
    // 设置body属性，控制功能演示区域的显示
    document.body.setAttribute('data-category', category);

    // 隐藏分页控件
    if (pagination) {
        pagination.style.display = 'none';
    }
    
    // 显示所有页面容器
    const blogPages = document.querySelectorAll('.blog-page');
    blogPages.forEach(function(page) {
        page.style.display = 'block';
    });
    
    // 先隐藏所有文章
    allPosts.forEach(function(post) {
        post.style.display = 'none';
        
        // 移除所有动画类
        removeAnimationClasses(post);
    });
    
    // 查找匹配的文章
    const matchingPosts = findMatchingPosts(category, allPosts);
    
    // 使用动画效果显示匹配的文章
    animateArticles(matchingPosts);
}

/**
 * 根据分类标识查找匹配的文章。
 * @param {string} category - 分类标识。
 * @param {NodeListOf<Element>} allPosts - 所有文章元素。
 * @returns {Array<HTMLElement>} 匹配的文章元素数组。
 */
function findMatchingPosts(category, allPosts) {
    let matchingPosts = [];
    
    // 先尝试精确匹配
    allPosts.forEach(function(post) {
        // 排除功能演示文章
        if (post.getAttribute('data-id') === '114514') {
            return;
        }
        
        const postCategory = post.getAttribute('data-category');
        if (postCategory === category) {
            matchingPosts.push(post);
        }
    });
    
    // 如果没有找到匹配的文章，尝试更灵活的匹配
    if (matchingPosts.length === 0) {
        allPosts.forEach(function(post) {
            // 排除功能演示文章
            if (post.getAttribute('data-id') === '114514') {
                return;
            }
            
            const postCategory = post.getAttribute('data-category');
            
            // 使用更灵活的匹配（包含关系、大小写不敏感等）
            if (postCategory && (
                postCategory.toLowerCase() === category.toLowerCase() ||
                postCategory.toLowerCase().includes(category.toLowerCase()) ||
                category.toLowerCase().includes(postCategory.toLowerCase())
            )) {
                matchingPosts.push(post);
            }
        });
    }
    
    return matchingPosts;
}

/**
 * 初始化分页功能。
 */
function initPagination() {
    const paginationLinks = document.querySelectorAll('.pagination a');
    if (!paginationLinks.length) return;
    
    // 分页点击事件
    paginationLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // 检查是否是blog.php页面（有href属性且包含blog.php）
            const href = this.getAttribute('href');
            if (href && href.includes('blog.php')) {
                // 允许默认跳转行为，不阻止
                return;
            }
            
            e.preventDefault();
            
            // 检查是否在"全部文章"分类下
            const activeCategory = document.querySelector('.category-link.active');
            if (activeCategory && activeCategory.getAttribute('data-category') !== 'all') {
                return;
            }
            
            // 更新活动状态
            updateActiveState(paginationLinks, this);
            
            // 获取页码
            const pageNum = this.getAttribute('data-page');
            
            // 切换页面，使用新的动画效果
            const blogPages = document.querySelectorAll('.blog-page');
            blogPages.forEach(function(page) {
                const isTargetPage = page.getAttribute('data-page') === pageNum;
                page.style.display = isTargetPage ? 'block' : 'none';
                
                // 如果是目标页面，应用动画到其文章
                if (isTargetPage) {
                    animateArticles(page.querySelectorAll('.blog-post'));
                }
            });
            
            // 平滑滚动到顶部
            scrollToTop();
        });
    });
}

/**
 * 初始化博客文章的初始显示状态（默认显示第一页的"全部文章"）。
 */
function initBlogDisplay() {
    // 初始化 - 确保显示"全部文章"分类
    const allCategoryLink = document.querySelector('.category-link[data-category="all"]');
    if (allCategoryLink) {
        // 需要避免初始化时的滚动行为
        const originalScrollBehavior = window.scrollTo;
        
        // 临时替换scrollTo方法以防止初始化时的滚动
        window.scrollTo = function() {};
        
        // 模拟点击"全部文章"分类
        allCategoryLink.click();
        
        // 恢复原始的scrollTo方法
        window.scrollTo = originalScrollBehavior;
        
        // 确保页面在顶部
        setTimeout(function() {
            window.scrollTo({
                top: 0,
                behavior: 'auto'
            });
        }, 10);
    } else {
        // 找不到"全部文章"分类链接，手动处理初始显示
        const firstPage = document.querySelector('.blog-page[data-page="1"]');
        if (firstPage) {
            firstPage.style.display = 'block';
            animateArticles(firstPage.querySelectorAll('.blog-post'));
        }
    }
}

/**
 * 重置搜索输入框的值。
 */
function resetSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
}

/**
 * 平滑滚动到页面顶部或指定元素。
 */
function scrollToTop() {
    const titleElement = document.querySelector('.title');
    if (titleElement) {
        window.scrollTo({
            top: titleElement.offsetTop - 20,
            behavior: 'smooth'
        });
    } else {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}

/**
 * 移除元素上的动画相关 CSS 类。
 * @param {HTMLElement} element - 目标元素。
 */
function removeAnimationClasses(element) {
    element.classList.remove('fadeInUp', 'scaleIn');
    for (let i = 1; i <= 8; i++) {
        element.classList.remove('anim-delay-' + i);
    }
}

/**
 * 为一组文章元素应用入场动画。
 * @param {NodeListOf<Element>} articles - 文章元素列表。
 */
function animateArticles(articles) {
    // 转换为数组以便使用数组方法
    const articlesArray = Array.from(articles);
    
    articlesArray.forEach(function(post, index) {
        // 重置所有动画类
        removeAnimationClasses(post);
        
        // 确保文章可见
        post.style.display = 'block';
        ensureContentVisible(post);
        
        // 根据位置给予不同的动画效果，交替使用两种动画
        const animClass = index % 2 === 0 ? 'fadeInUp' : 'scaleIn';
        post.classList.add(animClass);
        
        // 为前8篇文章设置不同的延迟
        const delay = Math.min(index + 1, 8);
        post.classList.add('anim-delay-' + delay);
    });
}

/**
 * 确保文章内容及其子元素可见（用于动画开始前）。
 * @param {HTMLElement} post - 文章元素。
 */
function ensureContentVisible(post) {
    const elements = ['blog-content', 'blog-title', 'blog-meta', 'blog-more'];
    
    elements.forEach(function(className) {
        const element = post.querySelector('.' + className);
        if (element) {
            element.style.display = element.tagName === 'DIV' ? 'block' : 'inline-block';
            element.style.visibility = 'visible';
            element.style.opacity = '1';
        }
    });
}

/**
 * 初始化代码高亮 (如果 Prism.js 已加载)。
 */
function initCodeHighlighting() {
    // 在页面加载完成后执行Prism高亮 (如果已经加载)
    if (typeof Prism !== 'undefined') {
        Prism.highlightAll();
    }
}

/**
 * 监听页面加载完成事件，隐藏加载动画。
 */
const pageLoading = document.querySelector("#loading");
window.addEventListener('load', function () {
    setTimeout(function () {
        pageLoading.style.opacity = '0';
    }, 600);
});

/**
 * 初始化移动端分类菜单的展开/收起功能。
 */
function initMobileCategoryMenu() {
    const mobileCategoryBtn = document.querySelector('.mobile-category-btn');
    const mobileCategoryMenu = document.querySelector('.mobile-category-menu');
    
    if (mobileCategoryBtn && mobileCategoryMenu) {
        mobileCategoryBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileCategoryMenu.classList.toggle('active');
        });
        
        // 点击其他区域关闭菜单
        document.addEventListener('click', function() {
            if (mobileCategoryMenu.classList.contains('active')) {
                mobileCategoryMenu.classList.remove('active');
            }
        });
        
        // 防止下拉菜单的点击事件冒泡
        const dropdown = document.querySelector('.mobile-category-dropdown');
        if (dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
    
    // 确保分类链接点击时也适用于移动菜单
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (mobileCategoryMenu.classList.contains('active')) {
                mobileCategoryMenu.classList.remove('active');
            }
        });
    });
}

/**
 * 初始化随机名言功能。
 */
function initRandomQuotes() {
    const quoteBox = document.querySelector('.quote-box');
    if (!quoteBox) return;
    
    const quotes = [
        { content: "生活就像骑自行车，要保持平衡就得不断前进。" },
        { content: "我不是一个特别有天赋的人，只是对问题特别好奇而已。" },
        { content: "学习是一种态度，不是能力。" },
        { content: "未来完全取决于你现在的努力。" },
        { content: "人生如同写代码，看似结束的地方，其实是新的起点。" },
        { content: "科技的目的是为人类提供更好的生活方式。" },
        { content: "AI不是为了取代人类，而是为了增强人类的能力。" },
        { content: "编程不仅是编写代码，更是在设计思想。" },
        { content: "不要害怕犯错，害怕的是犯了同样的错。" },
        { content: "每一个成功者都有一个开始。勇于开始，才能找到成功的路。" },
        { content: "世界上只有一种真正的英雄主义，那就是在认清生活真相后依然热爱生活。" },
        { content: "没有伞的孩子，必须努力奔跑。" },
        { content: "当你感到悲伤时，最好是去学些什么东西。学习会使你永远立于不败之地。" },
        { content: "如果你能梦想到，你就能实现它。" },
        { content: "我们的征途是星辰大海。" }
    ];
    
    function displayRandomQuote() {
        const randomIndex = Math.floor(Math.random() * quotes.length);
        const quote = quotes[randomIndex];
        
        const quoteContent = quoteBox.querySelector('.quote-content');
        
        if (quoteContent) {
            quoteContent.textContent = quote.content;
            
            // 添加淡入动画
            quoteContent.style.opacity = 0;
            
            setTimeout(() => {
                quoteContent.style.transition = 'opacity 0.5s ease';
                quoteContent.style.opacity = 1;
            }, 100);
        }
    }
    
    // 初始显示一条名言
    displayRandomQuote();
    
    // 点击刷新按钮更新名言
    const refreshButton = quoteBox.querySelector('.quote-refresh button');
    if (refreshButton) {
        refreshButton.addEventListener('click', displayRandomQuote);
    }
}