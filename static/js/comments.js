/**
 * 评论系统相关的JavaScript功能
 */

document.addEventListener('DOMContentLoaded', function() {
    const emojiItems = document.querySelectorAll('.emoji-item');
    const textarea = document.getElementById('comment_content');
    const commentForm = document.getElementById('commentForm');
    const parentIdInput = document.getElementById('parentId');
    const replyIndicator = document.getElementById('replyIndicator');
    const replyToSpan = document.getElementById('replyTo');
    const cancelReplyBtn = document.getElementById('cancelReply');
    const submitButtonText = document.getElementById('submitButtonText');
    
    // 表情符号点击事件
    emojiItems.forEach(item => {
        item.addEventListener('click', function() {
            const emoji = this.getAttribute('data-emoji');
            if (textarea && emoji) {
                const cursorPos = textarea.selectionStart;
                const textBefore = textarea.value.substring(0, cursorPos);
                const textAfter = textarea.value.substring(cursorPos);
                
                textarea.value = textBefore + emoji + textAfter;
                textarea.selectionStart = cursorPos + emoji.length;
                textarea.selectionEnd = cursorPos + emoji.length;
                textarea.focus();
            }
        });
    });
    
    // 回复按钮点击事件
    const replyButtons = document.querySelectorAll('.comment-reply-btn');
    replyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const commentId = this.getAttribute('data-comment-id');
            const authorName = this.getAttribute('data-author');
            
            parentIdInput.value = commentId;
            replyToSpan.textContent = authorName;
            replyIndicator.classList.add('active');
            submitButtonText.textContent = '发表回复';
            
            commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            textarea.focus();
        });
    });
    
    // 取消回复按钮点击事件
    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            parentIdInput.value = '0';
            replyIndicator.classList.remove('active');
            submitButtonText.textContent = '发表评论';
        });
    }
    
    // AJAX提交评论，避免页面刷新
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault(); // 阻止默认提交
            
            const submitButton = commentForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span>提交中...</span>';
            
            // 使用fetch API提交表单
            const formData = new FormData(commentForm);
            
            fetch('blog.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // 简单的成功处理
                showSuccessMessage('评论提交成功，等待管理员审核后显示！');
                
                // 清空表单
                commentForm.reset();
                
                // 重置按钮
                submitButton.disabled = false;
                submitButton.innerHTML = '<span>发表评论</span>';
                
                // 如果是回复，取消回复状态
                if (parentIdInput.value !== '0') {
                    parentIdInput.value = '0';
                    replyIndicator.classList.remove('active');
                    submitButtonText.textContent = '发表评论';
                }
            })
            .catch(error => {
                showErrorMessage('提交失败，请重试');
                
                // 重置按钮
                submitButton.disabled = false;
                submitButton.innerHTML = '<span>发表评论</span>';
            });
        });
    }
    
    // 显示成功消息
    function showSuccessMessage(message) {
        const existingMessage = document.querySelector('.comment-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'comment-message success';
        messageDiv.textContent = message;
        
        const formSection = document.querySelector('.comment-form-section');
        const form = document.querySelector('.comment-form');
        formSection.insertBefore(messageDiv, form);
        
        // 5秒后自动消失
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    // 显示错误消息
    function showErrorMessage(message) {
        const existingMessage = document.querySelector('.comment-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'comment-message error';
        messageDiv.textContent = message;
        
        const formSection = document.querySelector('.comment-form-section');
        const form = document.querySelector('.comment-form');
        formSection.insertBefore(messageDiv, form);
        
        // 5秒后自动消失
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}); 