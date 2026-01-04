<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4 class="footer-title">KẾT NỐI VỚI PHUONG STORE</h4>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-zalo"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                </div>
                <h4 class="footer-title" style="margin-top: 20px;">TỔNG ĐÀI MIỄN PHÍ</h4>
                <p class="hotline">Tư vấn mua hàng: <strong>1800.6601</strong></p>
                <p class="hotline">Hỗ trợ kỹ thuật: <strong>1800.6601</strong></p>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">VỀ CHÚNG TÔI</h4>
                <ul class="footer-links">
                    <li><a href="#">Giới thiệu về công ty</a></li>
                    <li><a href="#">Quy chế hoạt động</a></li>
                    <li><a href="#">Hệ thống cửa hàng</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">CHÍNH SÁCH</h4>
                <ul class="footer-links">
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Chính sách trả góp</a></li>
                    <li><a href="#">Giao hàng & Lắp đặt</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">HỖ TRỢ THANH TOÁN</h4>
                <div class="payment-grid">
                    <img src="https://fptshop.com.vn/Content/v5/images/icon-visa.png" alt="Visa">
                    <img src="https://fptshop.com.vn/Content/v5/images/icon-mastercard.png" alt="MasterCard">
                    <img src="https://fptshop.com.vn/Content/v5/images/icon-momo.png" alt="MoMo">
                    <img src="https://fptshop.com.vn/Content/v5/images/icon-vnpay.png" alt="VNPay">
                </div>
                <h4 class="footer-title" style="margin-top: 20px;">CHỨNG NHẬN</h4>
                <div class="cert-icons">
                    <img src="https://fptshop.com.vn/Content/v5/images/icon-congthuong.png" alt="Bộ Công Thương" style="width: 120px;">
                </div>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            <p class="keywords">
                <strong>Mọi người cũng tìm kiếm:</strong> iPhone 17 Pro Max | Laptop Gaming | Samsung S24 | MacBook M3
            </p>
            <p class="copyright">© 2025 CÔNG TY CỔ PHẦN PHUONG STORE</p>
        </div>
    </div>
</footer>

<div class="floating-container">
    <a href="javascript:void(0);" class="btn-float-chat" title="Chat với AI" onclick="toggleChat()">
        <i class="fa-solid fa-robot"></i>
    </a>
    
    <div class="btn-back-to-top" id="backToTopBtn" title="Lên đầu trang">
        <i class="fa-solid fa-arrow-up"></i>
    </div>
</div>

<div id="chatbot-container" style="display: none;">
    <div class="chat-header">
        <h4><i class="fa-solid fa-robot"></i> Trợ lý ảo AI</h4>
        <span onclick="toggleChat()" style="cursor:pointer; font-size: 20px;">&times;</span>
    </div>
    <div class="chat-body" id="chat-body">
        <div class="bot-message">
            Chào bạn! Tôi là trợ lý ảo AI.<br>
            Bạn cần tìm sản phẩm gì? (VD: Samsung, iPhone...)
        </div>
    </div>
    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Nhập tên sản phẩm..." onkeypress="handleEnter(event)">
        <button onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>

<style>
    /* Nút nổi (Nếu bạn chưa có CSS cho floating-container thì dùng cái này) */
    .floating-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-float-chat, .btn-back-to-top {
        width: 50px;
        height: 50px;
        background: #d70018;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
    }
    
    .btn-float-chat:hover, .btn-back-to-top:hover {
        transform: translateY(-5px);
        background: #b30014;
        color: white;
    }

    /* Khung Chat */
    #chatbot-container {
        position: fixed; 
        bottom: 90px; 
        right: 20px;
        width: 320px; 
        background: white;
        border-radius: 12px; 
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 99999; 
        overflow: hidden; 
        font-family: Arial, sans-serif;
        border: 1px solid #e0e0e0;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-header { 
        background: #d70018; 
        color: white; 
        padding: 12px 15px; 
        display: flex; 
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
    }
    
    .chat-body { 
        height: 300px; 
        overflow-y: auto; 
        padding: 15px; 
        background: #f9f9f9; 
        display: flex; 
        flex-direction: column; 
        gap: 12px;
    }

    .chat-footer { 
        display: flex; 
        border-top: 1px solid #eee; 
        padding: 5px;
        background: white;
    }
    
    .chat-footer input { 
        flex: 1; 
        padding: 10px; 
        border: none; 
        outline: none; 
        font-size: 14px;
    }
    
    .chat-footer button { 
        padding: 0 15px; 
        background: white; 
        border: none; 
        cursor: pointer; 
        color: #d70018;
        font-size: 18px;
    }

    .user-message { 
        align-self: flex-end; 
        background: #d70018; 
        color: white; 
        padding: 8px 12px; 
        border-radius: 15px 15px 0 15px; 
        max-width: 85%; 
        font-size: 14px; 
        box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    }
    
    .bot-message { 
        align-self: flex-start; 
        background: white; 
        color: #333; 
        padding: 8px 12px; 
        border-radius: 15px 15px 15px 0; 
        max-width: 85%; 
        font-size: 14px; 
        border: 1px solid #ddd;
        box-shadow: 1px 1px 3px rgba(0,0,0,0.05);
    }
    
    .bot-image { 
        width: 100%; 
        border-radius: 8px; 
        margin-top: 8px; 
        border: 1px solid #eee;
    }
</style>

<script>
    function toggleChat() {
        var chat = document.getElementById("chatbot-container");
        if (chat.style.display === "none" || chat.style.display === "") {
            chat.style.display = "block";
            setTimeout(() => document.getElementById("chat-input").focus(), 100);
        } else {
            chat.style.display = "none";
        }
    }

    function handleEnter(e) {
        if (e.key === 'Enter') sendMessage();
    }

    function sendMessage() {
        var input = document.getElementById("chat-input");
        var msg = input.value.trim();
        if (msg === "") return;

        // 1. Hiện tin nhắn người dùng
        addMessage(msg, 'user-message');
        input.value = "";

        // 2. Gửi AJAX lên Server
        fetch('index.php?ctrl=chat&act=handle_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: msg })
        })
        .then(response => response.json())
        .then(data => {
            // 3. Hiện phản hồi của Bot
            addMessage(data.reply, 'bot-message', data.image);
        })
        .catch(error => {
            console.error('Error:', error);
            addMessage("Lỗi kết nối server!", 'bot-message');
        });
    }

    function addMessage(text, className, imageUrl = null) {
        var body = document.getElementById("chat-body");
        var div = document.createElement("div");
        div.className = className;
        div.innerHTML = text;

        if (imageUrl) {
            var img = document.createElement("img");
            img.src = imageUrl;
            img.className = "bot-image";
            div.appendChild(img);
        }

        body.appendChild(div);
        body.scrollTop = body.scrollHeight; 
    }
    
    // JS cho nút Back To Top (Nếu bạn chưa có)
    var backToTopBtn = document.getElementById("backToTopBtn");
    if(backToTopBtn) {
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopBtn.style.display = "flex";
            } else {
                backToTopBtn.style.display = "none";
            }
        };
        backToTopBtn.onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
    }
</script>