/* Blog JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // CAPTCHA functionality
    const captchaQuestion = document.getElementById('captchaQuestion');
    const captchaAnswer = document.getElementById('captcha_answer');
    const refreshCaptcha = document.getElementById('refreshCaptcha');

    // Function to generate new captcha
    function generateCaptcha() {
        fetch('/generate-captcha')
            .then(response => response.json())
            .then(data => {
                captchaQuestion.textContent = data.question;
                captchaAnswer.value = '';
            })
            .catch(error => {
                console.error('Erro ao gerar captcha:', error);
                captchaQuestion.textContent = 'Erro ao carregar';
            });
    }

    // Generate initial captcha
    if (captchaQuestion) {
        generateCaptcha();
    }

    // Refresh captcha button
    if (refreshCaptcha) {
        refreshCaptcha.addEventListener('click', function() {
            this.style.transform = 'rotate(180deg)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 300);
            generateCaptcha();
        });
    }

    // Comment form submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showMessage('Comentário enviado com sucesso! Será revisado antes da publicação.', 'success');
                    
                    // Reset form
                    this.reset();
                    
                    // Generate new captcha
                    generateCaptcha();
                    
                    // Cancel reply if active
                    cancelReply();
                    
                    // Reload comments if needed
                    if (data.reload_comments) {
                        location.reload();
                    }
                } else {
                    // Show error message
                    showMessage(data.message || 'Erro ao enviar comentário.', 'error');
                    
                    // Generate new captcha on error
                    generateCaptcha();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Erro ao enviar comentário. Tente novamente.', 'error');
                
                // Generate new captcha on error
                generateCaptcha();
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Reply functionality
    window.replyToComment = function(commentId, authorName) {
        const parentIdInput = document.getElementById('parent_id');
        const replyInfo = document.getElementById('reply-info');
        const replyToName = document.getElementById('reply-to-name');
        const commentTextarea = document.getElementById('content');
        
        if (parentIdInput && replyInfo && replyToName && commentTextarea) {
            parentIdInput.value = commentId;
            replyToName.textContent = authorName;
            replyInfo.style.display = 'flex';
            
            // Scroll to comment form
            commentTextarea.focus();
            commentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    // Cancel reply functionality
    window.cancelReply = function() {
        const parentIdInput = document.getElementById('parent_id');
        const replyInfo = document.getElementById('reply-info');
        
        if (parentIdInput && replyInfo) {
            parentIdInput.value = '';
            replyInfo.style.display = 'none';
        }
    };

    // Show message function
    function showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.blog-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = `blog-message blog-message-${type}`;
        messageDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="blog-message-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Insert message at the top of the comment form
        const commentFormContainer = document.querySelector('.comment-form-container');
        if (commentFormContainer) {
            commentFormContainer.insertBefore(messageDiv, commentFormContainer.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 5000);
        }
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});