<?php if ($_SERVER['PHP_SELF'] !== '/login.php' && $_SERVER['PHP_SELF'] !== '/signup.php') : ?>
    <footer id="footer" class="footer w-full">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="d-flex align-items-center gap-2">
                        <img src="<?= SITE_URL ?>/assets/images/logo.png" class="border bg-white rounded-circle p-1"
                            style="width: 80px; height: 80px;"
                            alt="">
                        <span style="font-size: 1.5rem; font-weight: 600;text-wrap: wrap;">
                            <?= SITE_NAME; ?>
                        </span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>
                            <?= SITE_ADDRESS; ?>
                        </p>
                        <p>
                            <strong>Phone:</strong>
                            <span>
                                <?= SITE_PHONE; ?>
                            </span>
                        </p>
                        <p>
                            <strong>Email:</strong>
                            <span>
                                <?= SITE_EMAIL; ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="<?= SITE_URL; ?>">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#services">Services</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#team">Team</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12">
                    <h4>Follow Us</h4>
                    <p>
                        Let us be social and follow us on social media to keep in touch with us.
                    </p>
                    <div class="social-links d-flex">
                        <a href="https://x.com/Kurdcar4"><i class="bi bi-twitter-x"></i></a>
                        <a href=""><i class="bi bi-facebook"></i></a>
                        <a href=""><i class="bi bi-instagram"></i></a>
                        <a href=""><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container copyright text-center mt-4">
            <p>
                Copyright ©
                <?= date('Y'); ?>
                <a href="." class="link-secondary">
                    <?= SITE_NAME; ?>
                </a>.
                All rights reserved.
            </p>
        </div>
    </footer>
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
<?php endif; ?>

<script>
    const apiKey = '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'; // Replace with your actual API key
    const apiUrl = 'https://api.openai.com/v1/chat/completions';

    const chatIcon = document.getElementById("chat-icon");
    const chatWindow = document.getElementById("chat-window");
    const closeChat = document.getElementById("close-chat");
    const inputBox = document.getElementById("input-box");
    const sendButton = document.getElementById("send-btn");
    const messagesDiv = document.getElementById("messages");

    // System message containing the custom data
    const systemMessage = `You are an AI assistant created by Perplexity for Kurd Car Insurance.use emojis

## 📌 General Information
- Welcome to Kurdistan's trusted car insurance provider
- Offering reliable, affordable, and comprehensive protection
- Our team: Varin Kamil Fakhradin, Nuralhuda Nabil, Mohammed Ahmed, and Lana

## 📍 Contact Information
- Location: Empire World, Erbil
- Phone/WhatsApp: +964 751 818 9870
- Email: kurdcarinsurance@gmail.com
- Hours: 24/7 Support Available

## 💎 Insurance Plans

STANDARD PLAN ($50/month)
🚗🔧🛡️
━━━━━━━━━━━━━━━━━━━━━
- 50% repair coverage up to $5,000
- Basic theft protection
- Third-party liability: $10,000 property, $5,000 injury
- Support: 9 AM - 5 PM
- Response time: 24 hours

GOLD PLAN ($120/month)
🏅🚗💬
━━━━━━━━━━━━━━━━━━━━━
- 70% repair coverage up to $10,000
- Enhanced fire & theft protection
- Third-party liability: $25,000 property, $10,000 injury
- 24/7 chat support
- Response time: 12 hours
- Covers up to 3 vehicles

PREMIUM PLAN ($250/month)
🌟🚗🔥
━━━━━━━━━━━━━━━━━━━━━
- 90% repair coverage up to $25,000
- Full disaster protection
- Third-party liability: $50,000 property, $25,000 injury
- 24/7 phone & chat support
- Response time: 6 hours
- Covers up to 5 vehicles

PLATINUM PLAN ($500/month)
💎🚙🕒
━━━━━━━━━━━━━━━━━━━━━
- 100% unlimited repair coverage
- Complete disaster protection
- Unlimited liability coverage
- VIP 24/7 dedicated support
- Response time: 1 hour
- Unlimited vehicles coverage

## 🚗 Vehicle Registration Steps
1. Go to Profile > Vehicle
2. Click "Add Vehicle"
3. Enter required details:
   - License Plate
   - Make & Model
   - Year
   - VIN
   - Color
   - Mileage
   - Vehicle Photo
4. View available packages
5. Select and subscribe

## ❓ Common Questions
- Third-Party vs Comprehensive Coverage
- Multiple vehicle insurance options
- Accident reporting procedure
- Subscription cancellation process

## 🤝 Why Choose Us
- Flexible Insurance Plans
- Reliable Protection
- 24/7 Customer Support
- Professional Team
- Quick Claims Processing`;

    // Load conversation from localStorage or initialize with system message
    let conversation = JSON.parse(localStorage.getItem('chatConversation')) || [{
        role: "system",
        content: systemMessage
    }];

    // Function to save conversation to localStorage
    function saveConversation() {
        localStorage.setItem('chatConversation', JSON.stringify(conversation));
    }

    // Function to clear conversation from localStorage
    function clearConversation() {
        localStorage.removeItem('chatConversation');
        conversation = [{
            role: "system",
            content: systemMessage
        }];
        messagesDiv.innerHTML = ''; // Clear the chat window
    }

    function toggleChat() {
        const isHidden = chatWindow.style.display === "none";
        chatWindow.style.display = isHidden ? "flex" : "none";
        if (isHidden && messagesDiv.children.length === 0) {
            // Load previous messages if any
            conversation.slice(1).forEach(msg => {
                if (msg.role === "user") {
                    displayUserMessage(msg.content);
                } else if (msg.role === "assistant") {
                    displayBotMessage(msg.content);
                }
            });
            if (conversation.length === 1) {
                displayBotMessage("🚗 Welcome to Kurd Car Insurance! How may I assist you today?");
            }
        }
    }

    function showTypingIndicator() {
        const indicator = document.createElement("div");
        indicator.className = "typing-indicator";
        indicator.innerHTML = `
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            `;
        messagesDiv.appendChild(indicator);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        return indicator;
    }

    function displayMessage(message, className) {
        const messageDiv = document.createElement("div");
        messageDiv.className = `message ${className}`;
        messageDiv.textContent = message;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function displayUserMessage(message) {
        displayMessage(message, "user-message");
    }

    function displayBotMessage(message) {
        displayMessage(message, "bot-message");
    }

    function displayError(message) {
        const errorDiv = document.createElement("div");
        errorDiv.className = "error-message";
        errorDiv.textContent = message;
        messagesDiv.appendChild(errorDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    async function sendMessage() {
        const userMessage = inputBox.value.trim();
        if (!userMessage) return;

        inputBox.value = "";
        inputBox.disabled = true;
        sendButton.disabled = true;

        displayUserMessage(userMessage);
        const typingIndicator = showTypingIndicator();

        conversation.push({
            role: "user",
            content: userMessage
        });

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiKey}`
                },
                body: JSON.stringify({
                    model: 'gpt-3.5-turbo',
                    messages: conversation,
                    temperature: 0.7,
                    max_tokens: 300
                })
            });

            if (!response.ok) {
                throw new Error('Failed to get response from assistant');
            }

            const data = await response.json();
            const botResponse = data.choices[0].message.content;

            typingIndicator.remove();
            displayBotMessage(botResponse);

            conversation.push({
                role: "assistant",
                content: botResponse
            });

            // Save the updated conversation to localStorage
            saveConversation();

        } catch (error) {
            typingIndicator.remove();
            displayError("Sorry, I couldn't process your message. Please try again.");
            console.error('Error:', error);
        }

        inputBox.disabled = false;
        sendButton.disabled = false;
        inputBox.focus();
    }

    // Event Listeners
    chatIcon.addEventListener("click", toggleChat);
    closeChat.addEventListener("click", () => chatWindow.style.display = "none");
    sendButton.addEventListener("click", sendMessage);
    inputBox.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
    });

    // Optional: Add a button to clear chat history
    const clearChatButton = document.createElement("button");
    clearChatButton.textContent = "Clear Chat";
    clearChatButton.addEventListener("click", clearConversation);
    document.body.appendChild(clearChatButton);
</script>
</body>

</html>
<?php
ob_end_flush();
?>