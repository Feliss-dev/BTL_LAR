<div id="chatbot-container" class="chatbot-container">
    <div id="chatbot-toggle" class="chatbot-toggle" aria-label="Mở chatbot">
        <i class="fa fa-comments"></i>
    </div>

    <div id="chatbot-window" class="chatbot-window" style="display: none;">
        <div class="chatbot-header">
            <div class="header-left">
                <div class="avatar"> <i class="fa fa-robot"></i> </div>
                <div class="titles">
                    <h5>Tư vấn mua sắm</h5>
                    <small class="subtitle">Trợ lý ảo - tìm đồng hồ phù hợp nhanh chóng</small>
                </div>
            </div>

            <div class="chatbot-controls">
                <button id="new-chat-btn" class="btn btn-icon" title="Chat mới">
                    <i class="fa fa-plus"></i>
                </button>
                <button id="clear-history-btn" class="btn btn-icon" title="Xóa lịch sử">
                    <i class="fa fa-trash"></i>
                </button>
                <button id="chatbot-close" class="btn btn-icon" aria-label="Đóng">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <div id="chat-messages" class="chat-messages" role="log" aria-live="polite">
            <div class="bot-message initial">
                <div class="message-content">
                    Xin chào! Tôi là trợ lý ảo của cửa hàng đồng hồ. Tôi có thể giúp bạn tìm kiếm và tư vấn sản phẩm phù
                    hợp. Bạn cần tìm loại đồng hồ nào?
                </div>
            </div>
        </div>

        <div id="chat-quick-prompts" class="chat-quick-prompts" aria-label="Gợi ý nhanh">
            <div class="quick-prompts-label"><i class="fas fa-lightbulb"></i> Gợi ý</div>
            <div class="quick-prompts-buttons">
                <!-- quick prompts trigger server-side search (force_search=1) to ensure suggestions returned -->
                <button class="quick-prompt" data-prompt="Đồng hồ nam" data-force-search="1"><i
                        class="fa fa-mars"></i><span> Nam</span></button>
                <button class="quick-prompt" data-prompt="Đồng hồ nữ" data-force-search="1"><i
                        class="fa fa-venus"></i><span> Nữ</span></button>
                <button class="quick-prompt" data-prompt="Đồng hồ giá dưới 2 triệu" data-force-search="1"><i
                        class="fa fa-tag"></i><span>&lt; 2 triệu</span></button>
                <button class="quick-prompt" data-prompt="Đồng hồ thương hiệu nổi tiếng" data-force-search="1"><i
                        class="fa fa-star"></i><span> Thương hiệu</span></button>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn...">
                <div class="input-group-append">
                    <button id="send-btn" class="btn btn-primary" title="Gửi">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <div id="typing-indicator" class="typing-indicator" style="display: none;">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --chat-width: 380px;
        --chat-height: 540px;
        --primary: #0d6efd;
        --bg: #ffffff;
        --muted: #6c757d;
        --radius: 14px;
        --shadow: 0 12px 30px rgba(13, 110, 253, 0.12), 0 4px 12px rgba(2, 6, 23, 0.06);
    }

    .chatbot-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1200;
        font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    .chatbot-toggle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), #0062d6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 22px rgba(9, 30, 66, 0.18);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .chatbot-toggle i {
        font-size: 20px;
    }

    .chatbot-toggle:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 38px rgba(9, 30, 66, 0.22);
    }

    .chatbot-window {
        width: var(--chat-width);
        height: var(--chat-height);
        background: var(--bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        position: absolute;
        bottom: 80px;
        right: 0;
        overflow: hidden;
        transform: translateX(-18px);
        /* shift left slightly when pop up */
        transition: transform .18s ease, opacity .18s ease;
        border: 1px solid rgba(15, 23, 42, 0.04);
    }

    .chatbot-header {
        background: linear-gradient(90deg, rgba(13, 110, 253, 1), rgba(0, 123, 255, 0.95));
        color: white;
        padding: 12px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .chatbot-header .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chatbot-header .avatar {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .chatbot-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
    }

    .chatbot-header .subtitle {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.9);
        display: block;
        margin-top: 2px;
    }

    .chatbot-controls {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .chatbot-controls .btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.95);
        padding: 6px;
        border-radius: 8px;
        cursor: pointer;
        transition: background .12s ease;
    }

    .chatbot-controls .btn:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .chat-messages {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
    }

    .message {
        margin-bottom: 14px;
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .user-message {
        justify-content: flex-end;
    }

    .bot-message {
        justify-content: flex-start;
    }

    .message-content {
        max-width: 78%;
        padding: 10px 14px;
        border-radius: 14px;
        word-wrap: break-word;
        line-height: 1.45;
        font-size: 14px;
        box-shadow: 0 1px 0 rgba(15, 23, 42, 0.02);
    }

    .user-message .message-content {
        background: linear-gradient(90deg, var(--primary), #0062d6);
        color: #fff;
        border-bottom-right-radius: 6px;
    }

    .bot-message .message-content {
        background: #f7f9fc;
        color: #0f172a;
        border-bottom-left-radius: 6px;
    }

    .bot-message.initial .message-content {
        background: transparent;
        color: var(--muted);
        padding: 0;
        box-shadow: none;
    }

    .suggested-products {
        margin-top: 10px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    /* two-column product suggestions on wider screens */
    @media (min-width: 420px) {
        .suggested-products {
            grid-template-columns: 1fr 1fr;
        }

        .chatbot-window {
            width: 420px;
            transform: translateX(-22px);
        }
    }

    .product-suggestion {
        display: flex;
        gap: 10px;
        align-items: center;
        border-radius: 10px;
        padding: 8px;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.04);
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }

    .product-suggestion:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(2, 6, 23, 0.06);
        border-color: rgba(13, 110, 253, 0.12);
    }

    .product-suggestion img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .product-info h6 {
        margin: 0;
        font-size: 13px;
        color: #0f172a;
        font-weight: 600;
    }

    .product-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 13px;
        margin-top: 4px;
    }

    .product-info small {
        display: block;
        color: var(--muted);
        font-size: 11px;
        margin-top: 6px;
    }

    .chat-input-container {
        padding: 12px;
        border-top: 1px solid rgba(15, 23, 42, 0.04);
        background: #fff;
    }

    .input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .input-group .form-control {
        flex: 1;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        outline: none;
        font-size: 14px;
    }

    .input-group .btn-primary {
        background: linear-gradient(90deg, var(--primary), #0062d6);
        border: none;
        padding: 8px 12px;
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .typing-indicator {
        text-align: center;
        padding: 6px 0;
    }

    .typing-indicator span {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        margin: 0 3px;
        animation: typing 1.2s infinite ease-in-out;
        opacity: .9;
    }

    .typing-indicator span:nth-child(1) {
        animation-delay: -0.24s
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: -0.12s
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(.8);
            opacity: .5
        }

        40% {
            transform: scale(1.2);
            opacity: 1
        }
    }

    .chat-quick-prompts {
        border-top: 1px solid rgba(15, 23, 42, 0.03);
        padding: 10px 12px;
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .quick-prompts-label {
        font-size: 12px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-prompts-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .quick-prompt {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        border: 1px solid rgba(15, 23, 42, 0.03);
        cursor: pointer;
        font-size: 13px;
    }

    .quick-prompt i {
        color: var(--primary);
        font-size: 12px;
    }

    .quick-prompt:hover {
        background: var(--primary);
        color: #fff;
    }

    /* small devices adjustments */
    @media (max-width: 420px) {
        .chatbot-window {
            right: 6px;
            transform: translateX(-10px);
            width: calc(100vw - 40px);
            height: 60vh;
            bottom: 72px;
        }

        .product-suggestion img {
            width: 54px;
            height: 54px;
        }
    }
</style>

<script>
    class Chatbot {
        constructor() {
            this.sessionId = localStorage.getItem('chatbot_session_id') || null;
            this.isOpen = false;
            this.forceSearchFlag = false;
            this.init();
        }

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : (window.csrfToken || '');
        }

        init() {
            this.bindEvents();
            this.loadHistory();
            this.bindQuickPrompts();
        }

        bindEvents() {
            document.getElementById('chatbot-toggle').addEventListener('click', () => {
                this.toggle();
            });
            document.getElementById('chatbot-close').addEventListener('click', () => {
                this.close();
            });
            document.getElementById('send-btn').addEventListener('click', () => {
                this.sendMessage();
            });
            document.getElementById('chat-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.sendMessage();
            });
            document.getElementById('new-chat-btn').addEventListener('click', () => {
                this.newChat();
            });
            document.getElementById('clear-history-btn').addEventListener('click', () => {
                this.clearHistory();
            });
            document.addEventListener('click', (e) => {
                if (!document.getElementById('chatbot-container').contains(e.target) && this.isOpen) {
                    this.close();
                }
            });
        }

        bindQuickPrompts() {
            document.querySelectorAll('.quick-prompt').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const text = btn.getAttribute('data-prompt') || btn.textContent.trim();
                    this.forceSearchFlag = btn.getAttribute('data-force-search') === '1';
                    const input = document.getElementById('chat-input');
                    input.value = text;
                    input.focus();
                    // slight debounce to avoid double-sends
                    setTimeout(() => {
                        this.sendMessage();
                        // reset
                        this.forceSearchFlag = false;
                    }, 120);
                });
            });
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            document.getElementById('chatbot-window').style.display = 'flex';
            document.getElementById('chatbot-toggle').style.display = 'none';
            this.isOpen = true;
            this.focusInput();
        }

        close() {
            document.getElementById('chatbot-window').style.display = 'none';
            document.getElementById('chatbot-toggle').style.display = 'flex';
            this.isOpen = false;
        }

        focusInput() {
            setTimeout(() => {
                document.getElementById('chat-input').focus();
            }, 100);
        }

        async sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();

            if (!message) return;

            // Add user message to chat
            this.addMessage(message, 'user');
            input.value = '';

            // Show typing indicator
            this.showTyping();

            const csrf = this.getCsrfToken();
            if (!csrf) {
                this.hideTyping();
                console.error('CSRF token missing');
                this.addMessage('Lỗi: CSRF token không tồn tại. Vui lòng tải lại trang.', 'bot');
                return;
            }

            try {
                const response = await fetch('/chatbot/stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'text/event-stream'
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: this.sessionId,
                        force_search: !!this.forceSearchFlag
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }

                // Prepare a bot message container for incremental streaming text
                const messagesContainer = document.getElementById('chat-messages');
                const botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'message bot-message';
                const botContent = document.createElement('div');
                botContent.className = 'message-content';
                botContent.innerHTML = ''; // will append chunks
                botMessageDiv.appendChild(botContent);
                messagesContainer.appendChild(botMessageDiv);
                this.scrollToBottom();

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let buffer = '';
                let productsAppended = false;

                while (true) {
                    const {
                        done,
                        value
                    } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, {
                        stream: true
                    });

                    // SSE events are separated by double newline
                    let parts = buffer.split('\n\n');
                    // keep last partial chunk in buffer
                    buffer = parts.pop();

                    for (const part of parts) {
                        const lines = part.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
                        for (const line of lines) {
                            if (!line.startsWith('data:')) continue;
                            let payloadStr = line.slice(5).trim();
                            // Some SSE implementations send "[DONE]" or non-json tokens
                            if (payloadStr === '[DONE]') continue;
                            try {
                                const payload = JSON.parse(payloadStr);
                                // update session id if provided
                                if (payload.session_id) {
                                    this.sessionId = payload.session_id;
                                    localStorage.setItem('chatbot_session_id', this.sessionId);
                                }

                                if (payload.type === 'content' && payload.text) {
                                    // remove any embedded suggested_product_ids JSON block before showing text
                                    let filtered = payload.text.replace(/\{[\s\S]*?"suggested_product_ids"\s*:\s*\[[\s\S]*?\][\s\S]*?\}/g, '');
                                    filtered = filtered.trim();
                                    if (filtered !== '') {
                                        // ensure HTML escaping of plain text; replace \n with <br>
                                        const safeText = filtered.replace(/&/g, '&amp;').replace(/</g, '&lt;')
                                            .replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                                        botContent.innerHTML += safeText;
                                        this.scrollToBottom();
                                    }
                                } else if (payload.type === 'products' && Array.isArray(payload.products) && payload
                                    .products.length > 0 && !productsAppended) {
                                    // append suggested products block under the current bot message
                                    const productsDiv = document.createElement('div');
                                    productsDiv.className = 'suggested-products';
                                    payload.products.forEach(product => {
                                        const productDiv = document.createElement('div');
                                        productDiv.className = 'product-suggestion';
                                        productDiv.onclick = () => window.open(product.url, '_blank');

                                        const discountPrice = product.discount > 0 ?
                                            (product.price - (product.price * product.discount / 100)) :
                                            product.price;

                                        productDiv.innerHTML = `
                                            <img src="${product.photo}" alt="${product.title}" onerror="this.src='/storage/photos/default.jpg'">
                                            <div class="product-info">
                                                <h6>${product.title}</h6>
                                                <div class="product-price">
                                                    ${product.discount > 0 ? `<del>${this.formatPrice(product.price)}đ</del> ` : ''}
                                                    ${this.formatPrice(discountPrice)}đ
                                                </div>
                                                <small>${product.category} · ${product.brand || ''}</small>
                                            </div>
                                        `;
                                        productsDiv.appendChild(productDiv);
                                    });
                                    botMessageDiv.appendChild(productsDiv);
                                    productsAppended = true;
                                    this.scrollToBottom();
                                } else if (payload.type === 'done') {
                                    // finished
                                    this.hideTyping();
                                }
                            } catch (e) {
                                // Not JSON - ignore non-json SSE lines
                                console.warn('SSE parse warning:', e);
                            }
                        }
                    }
                }

                // finalize any remaining buffer (non-delimited)
                if (buffer.trim()) {
                    // try parse final buffer if it contains a data: prefix
                    const matches = buffer.match(/data:\s*(.*)/);
                    if (matches && matches[1]) {
                        try {
                            const payload = JSON.parse(matches[1]);
                            if (payload.session_id) {
                                this.sessionId = payload.session_id;
                                localStorage.setItem('chatbot_session_id', this.sessionId);
                            }
                            if (payload.type === 'content' && payload.text) {
                                let filtered = payload.text.replace(/\{[\s\S]*?"suggested_product_ids"\s*:\s*\[[\s\S]*?\][\s\S]*?\}/g, '');
                                filtered = filtered.trim();
                                if (filtered !== '') {
                                    const safeText = filtered.replace(/&/g, '&amp;').replace(/</g, '&lt;')
                                        .replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                                    botContent.innerHTML += safeText;
                                    this.scrollToBottom();
                                }
                            }

                            if (payload.type === 'products' && Array.isArray(payload.products) && payload.products
                                .length > 0 && !productsAppended) {
                                // append products similar to above
                                const productsDiv = document.createElement('div');
                                productsDiv.className = 'suggested-products';
                                payload.products.forEach(product => {
                                    const productDiv = document.createElement('div');
                                    productDiv.className = 'product-suggestion';
                                    productDiv.onclick = () => window.open(product.url, '_blank');

                                    const discountPrice = product.discount > 0 ?
                                        (product.price - (product.price * product.discount / 100)) :
                                        product.price;

                                    productDiv.innerHTML = `
                                        <img src="${product.photo}" alt="${product.title}" onerror="this.src='/storage/photos/default.jpg'">
                                        <div class="product-info">
                                            <h6>${product.title}</h6>
                                            <div class="product-price">
                                                ${product.discount > 0 ? `<del>${this.formatPrice(product.price)}đ</del> ` : ''}
                                                ${this.formatPrice(discountPrice)}đ
                                            </div>
                                            <small>${product.category} · ${product.brand || ''}</small>
                                        </div>
                                    `;
                                    productsDiv.appendChild(productDiv);
                                });
                                botMessageDiv.appendChild(productsDiv);
                                productsAppended = true;
                                this.scrollToBottom();
                            }
                        } catch (e) {
                            // ignore
                        }
                    }
                }

                // hide typing if not already hidden
                this.hideTyping();

            } catch (error) {
                this.hideTyping();
                console.error('Detailed error:', error);
                this.addMessage(`Lỗi: ${error.message}`, 'bot');
            }
        }

        addMessage(content, type, suggestedProducts = []) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;

            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';

            // when showing bot saved responses, strip any embedded suggested_product_ids JSON
            let display = content;
            if (type === 'bot' && typeof display === 'string') {
                display = display.replace(/\{[\s\S]*?"suggested_product_ids"\s*:\s*\[[\s\S]*?\][\s\S]*?\}/g, '').trim();
            }
            messageContent.innerHTML = this.formatMessage(display);

            messageDiv.appendChild(messageContent);

            // Add suggested products if available
            if (suggestedProducts && suggestedProducts.length > 0) {
                const productsDiv = document.createElement('div');
                productsDiv.className = 'suggested-products';

                suggestedProducts.forEach(product => {
                    const productDiv = document.createElement('div');
                    productDiv.className = 'product-suggestion';
                    productDiv.onclick = () => window.open(product.url, '_blank');

                    const discountPrice = product.discount > 0 ?
                        (product.price - (product.price * product.discount / 100)) :
                        product.price;

                    productDiv.innerHTML = `
                    <img src="${product.photo}" alt="${product.title}" onerror="this.src='/storage/photos/default.jpg'">
                    <div class="product-info">
                        <h6>${product.title}</h6>
                        <div class="product-price">
                            ${product.discount > 0 ? `<del>${this.formatPrice(product.price)}đ</del> ` : ''}
                            ${this.formatPrice(discountPrice)}đ
                        </div>
                        <small>${product.category}</small>
                    </div>
                `;

                    productsDiv.appendChild(productDiv);
                });

                messageDiv.appendChild(productsDiv);
            }

            messagesContainer.appendChild(messageDiv);
            this.scrollToBottom();
        }

        formatMessage(content) {
            return content.replace(/\n/g, '<br>');
        }

        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        showTyping() {
            document.getElementById('typing-indicator').style.display = 'block';
            this.scrollToBottom();
        }

        hideTyping() {
            document.getElementById('typing-indicator').style.display = 'none';
        }

        scrollToBottom() {
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        async newChat() {
            try {
                const response = await fetch('/chatbot/new-session', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.sessionId = data.session_id;
                    localStorage.setItem('chatbot_session_id', this.sessionId);

                    // Clear messages
                    document.getElementById('chat-messages').innerHTML = `
                    <div class="bot-message">
                        <div class="message-content">
                            Xin chào! Tôi là trợ lý ảo của cửa hàng đồng hồ. Tôi có thể giúp bạn tìm kiếm và tư vấn sản phẩm phù hợp. Bạn cần tìm loại đồng hồ nào?
                        </div>
                    </div>
                `;
                }
            } catch (error) {
                console.error('New chat error:', error);
            }
        }

        async clearHistory() {
            if (!this.sessionId) return;

            if (!confirm('Bạn có chắc muốn xóa lịch sử chat không?')) return;

            try {
                const response = await fetch('/chatbot/clear-history', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Clear messages
                    document.getElementById('chat-messages').innerHTML = `
                    <div class="bot-message">
                        <div class="message-content">
                            Lịch sử chat đã được xóa. Bạn có thể bắt đầu cuộc trò chuyện mới.
                        </div>
                    </div>
                `;
                }
            } catch (error) {
                console.error('Clear history error:', error);
            }
        }

        async loadHistory() {
            if (!this.sessionId) return;

            try {
                const response = await fetch(`/chatbot/history?session_id=${this.sessionId}`);
                const data = await response.json();

                if (data.success && data.history.length > 0) {
                    document.getElementById('chat-messages').innerHTML = '';

                    data.history.forEach(chat => {
                        this.addMessage(chat.user_message, 'user');
                        this.addMessage(chat.bot_response, 'bot', chat.suggested_products);
                    });
                }
            } catch (error) {
                console.error('Load history error:', error);
            }
        }
    }

    // Initialize chatbot when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        new Chatbot();
    });
</script>
