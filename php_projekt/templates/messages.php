<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Conversation.php';
require_once 'classes/Message.php';
require_once 'components/Header.php';
require_once 'components/Footer.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->getConnection();
$conversationModel = new Conversation($db);
$messageModel = new Message($db);
$my_id = $_SESSION['user_id'];
$active_conv_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$chat_messages = [];
$active_chat_info = null;
$error_message = '';

try {
    if (isset($_GET['start_chat'])) {
        $item_id = intval($_GET['start_chat']);

        if ($item_id > 0) {
            $conversationId = $conversationModel->ensureForItemAndBuyer($item_id, $my_id);

            if ($conversationId) {
                header("Location: messages.php?id=" . $conversationId);
                exit();
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['conv_id'], $_POST['message_text'])) {
        $conv_id = intval($_POST['conv_id']);
        $msg_text = trim($_POST['message_text']);

        if ($conv_id > 0 && $msg_text !== '' && $conversationModel->userCanAccess($conv_id, $my_id)) {
            $messageModel->addMessage($conv_id, $my_id, $msg_text);
            $conversationModel->updateTimestamp($conv_id);
        }

        header("Location: messages.php?id=" . $conv_id);
        exit();
    }

    $conversations = $conversationModel->getUserConversations($my_id);

    if ($active_conv_id) {
        $active_chat_info = $conversationModel->getConversationForUser($active_conv_id, $my_id);
        if ($active_chat_info) {
            $chat_messages = $messageModel->getMessagesByConversationId($active_conv_id);
        } else {
            $error_message = 'Conversation not found or access denied.';
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox | Nocturne</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chat-history::-webkit-scrollbar { width: 6px; }
        .chat-history::-webkit-scrollbar-thumb { background: var(--dark-charcoal); }
    </style>
</head>
<body style="background-color: var(--bg-ash);">

    <?php (new Header())->render(); ?>

    <main class="messages-main" style="margin: 40px auto; max-width: 1200px;">
        <div class="section-title">
            <h2>Inbox</h2>
            <div class="title-line"></div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-box" style="margin: 20px auto; max-width: 1200px; background: #ffe6e6; color: #900; padding: 15px; border-radius: 8px; border: 1px solid #f2c2c2;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="messages-container">
            
            <aside class="conversations-list">
                <div class="conversations-header">
                    <h3>Your Chats</h3>
                </div>
                
                <?php if (count($conversations) > 0): ?>
                    <?php foreach ($conversations as $conv): ?>
                        <?php 
                            $is_buyer = ($conv['buyer_id'] == $my_id);
                            $other_person = $is_buyer ? $conv['seller_name'] : $conv['buyer_name'];
                            $active_class = ($conv['id'] == $active_conv_id) ? 'active' : '';
                            $img = !empty($conv['image_url']) ? '../' . $conv['image_url'] : '../images/no-image.jpg';
                        ?>
                        
                        <a href="messages.php?id=<?php echo $conv['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="conversation-item <?php echo $active_class; ?>">
                                <img src="<?php echo htmlspecialchars($img); ?>" class="avatar" style="border-radius:0;">
                                <div class="conv-info">
                                    <div class="conv-top">
                                        <span class="conv-name">@<?php echo htmlspecialchars($other_person); ?></span>
                                    </div>
                                    <span class="conv-item-name"><?php echo htmlspecialchars($conv['title']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="padding: 20px; color: var(--grey); font-size: 14px;">No active conversations.</p>
                <?php endif; ?>
            </aside>

            <section class="chat-area">
                
                <?php if ($active_chat_info): ?>
                    <?php 
                        $is_me_buyer = ($active_chat_info['buyer_id'] == $my_id);
                        $chatting_with = $is_me_buyer ? $active_chat_info['seller_name'] : $active_chat_info['buyer_name'];
                    ?>
                    
                    <div class="chat-header">
                        <div class="chat-header-info">
                            <h3>@<?php echo htmlspecialchars($chatting_with); ?></h3>
                            <span>Regarding: <a href="item.php?id=<?php echo $active_chat_info['item_id']; ?>" style="color:var(--dark-charcoal);"><?php echo htmlspecialchars($active_chat_info['title']); ?></a></span>
                        </div>
                    </div>

                    <div class="chat-history" id="chatHistoryBox">
                        <?php if (count($chat_messages) > 0): ?>
                            <?php foreach ($chat_messages as $msg): ?>
                                <?php 
                                    $msg_class = ($msg['sender_id'] == $my_id) ? 'sent' : 'received'; 
                                ?>
                                <div class="message-wrapper <?php echo $msg_class; ?>">
                                    <div class="message bubble">
                                        <?php echo nl2br(htmlspecialchars($msg['message_text'])); ?>
                                    </div>
                                    <span class="msg-time"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--grey); margin-top: 50px;">No messages yet. Send a message to start!</p>
                        <?php endif; ?>
                    </div>

                    <form action="messages.php" method="POST" class="chat-input-area" style="margin:0;">
                        <input type="hidden" name="conv_id" value="<?php echo $active_conv_id; ?>">
                        <input type="text" name="message_text" placeholder="Write a message..." required style="flex:1; border:none; padding:15px; outline:none; background:transparent;">
                        <button type="submit" class="btn-primary" style="border:none; padding: 0 30px; font-weight:bold; cursor:pointer;">SEND</button>
                    </form>

                <?php else: ?>
                    <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--grey);">
                        Select a conversation from the left to start messaging.
                    </div>
                <?php endif; ?>
                
            </section>
        </div>
    </main>

    <?php (new Footer())->render(); ?>
</html>