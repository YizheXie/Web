/**
 * 登录表单验证功能
 */

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', function() {
    initLoginForm();
});

/**
 * 初始化登录表单验证功能。
 */
function initLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;
    
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    
    // 添加实时验证
    if (usernameInput) {
        usernameInput.addEventListener('blur', () => validateUsername());
        usernameInput.addEventListener('input', () => clearError('usernameError'));
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('blur', () => validatePassword());
        passwordInput.addEventListener('input', () => clearError('passwordError'));
    }
    
    // 表单提交验证
    form.addEventListener('submit', handleFormSubmit);
}

/**
 * 验证用户名字段。
 */
function validateUsername() {
    const usernameInput = document.getElementById('username');
    const errorElement = document.getElementById('usernameError');
    const username = usernameInput.value.trim();
    
    if (!username) {
        showError(errorElement, '用户名不能为空');
        return false;
    }
    
    if (username.length < 2) {
        showError(errorElement, '用户名至少需要2个字符');
        return false;
    }
    
    if (username.length > 50) {
        showError(errorElement, '用户名不能超过50个字符');
        return false;
    }
    
    // 检查用户名格式（字母、数字、下划线）
    if (!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(username)) {
        showError(errorElement, '用户名只能包含字母、数字、下划线和中文');
        return false;
    }
    
    clearError('usernameError');
    return true;
}

/**
 * 验证密码字段。
 */
function validatePassword() {
    const passwordInput = document.getElementById('password');
    const errorElement = document.getElementById('passwordError');
    const password = passwordInput.value;
    
    if (!password) {
        showError(errorElement, '密码不能为空');
        return false;
    }
    
    if (password.length < 6) {
        showError(errorElement, '密码至少需要6个字符');
        return false;
    }
    
    if (password.length > 100) {
        showError(errorElement, '密码不能超过100个字符');
        return false;
    }
    
    clearError('passwordError');
    return true;
}

/**
 * 显示错误信息。
 */
function showError(errorElement, message) {
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        // 添加错误样式到输入框
        const inputId = errorElement.id.replace('Error', '');
        const inputElement = document.getElementById(inputId);
        if (inputElement) {
            inputElement.classList.add('error');
        }
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
        
        // 移除错误样式
        const inputId = errorId.replace('Error', '');
        const inputElement = document.getElementById(inputId);
        if (inputElement) {
            inputElement.classList.remove('error');
        }
    }
}

/**
 * 处理表单提交。
 */
function handleFormSubmit(e) {
    // 清除所有现有错误信息
    clearError('usernameError');
    clearError('passwordError');
    
    // 验证所有字段
    const isUsernameValid = validateUsername();
    const isPasswordValid = validatePassword();
    
    if (!isUsernameValid || !isPasswordValid) {
        e.preventDefault();
        
        // 添加轻微的摇摆动画提示
        const form = document.getElementById('loginForm');
        if (form) {
            form.classList.add('shake');
            setTimeout(() => {
                form.classList.remove('shake');
            }, 500);
        }
        
        return false;
    }
    
    // 如果验证通过，显示加载状态
    const submitBtn = document.querySelector('.login-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = '登录中...';
        submitBtn.classList.add('loading');
    }
    
    // 表单会正常提交到PHP处理
    return true;
}

/**
 * 添加输入框聚焦效果
 */
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // 如果输入框有值，保持聚焦状态
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
});

/**
 * 密码可见性切换功能
 */
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = '🙈';
        toggleBtn.setAttribute('aria-label', '隐藏密码');
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = '👁️';
        toggleBtn.setAttribute('aria-label', '显示密码');
    }
} 