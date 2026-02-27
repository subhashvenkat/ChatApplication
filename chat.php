<?php
session_start();
$conn = new mysqli("localhost", "root", "", "chat_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT id, username, email FROM users WHERE id != $user_id";
$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    if ($data['action'] == "fetch" && isset($data['receiver_id'])) {
        $receiver_id = $data['receiver_id'];

        $sql = "SELECT sender_id, message, timestamp FROM messages 
                WHERE (sender_id = $user_id AND receiver_id = $receiver_id) 
                   OR (sender_id = $receiver_id AND receiver_id = $user_id) 
                ORDER BY timestamp ASC";

        $result = $conn->query($sql);
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        echo json_encode($messages);
    }

    if ($data['action'] == "send" && isset($data['receiver_id']) && isset($data['message'])) {
        $receiver_id = $data['receiver_id'];
        $message = $conn->real_escape_string($data['message']);
        $timestamp = date("Y-m-d H:i:s");

        $sql = "INSERT INTO messages (sender_id, receiver_id, message, timestamp) 
                VALUES ($user_id, $receiver_id, '$message', '$timestamp')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    }

    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@3"></script>
    <style>
        .list-group-item{
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
<div id="app" class="container py-4">
    <div class="d-flex justify-content-between">
        <h2><i class="fa-solid fa-user me-2"></i>
        Welcome, {{ userUsername }}</h2>
        <a href="login.php" class="btn btn-success">Logout</a>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <h4>Friends</h4>
            <ul class="list-group">
                <li v-for="user in users" :key="user.id" 
                    class="list-group-item list-group-item-action d-flex align-items-center"@click="selectUser(user)">
                    <i class="fa-solid fa-user me-2"></i>
                    {{ user.username }}
                </li>
            </ul>
        </div>

        <div class="col-md-8">
            <div v-if="selected_user" class="chat-box card shadow-sm p-3">
                <h4><i class="fa-solid fa-user me-2"></i>{{ selected_user.username }}</h4>
                <hr> 
                <div class="messages-box" ref="messagesBox" style="height: 300px; overflow-y: auto;">
                <div v-for="message in messages" :key="message.id" 
                     :class="message.sender_id == userId ? 'text-end text-success' : 'text-start text-primary'">
                    <strong>{{ message.sender_id == userId ? 'You' : selected_user.username }}:</strong> 
                    {{ message.message }}
                    <small class="text-muted d-block">{{ message.timestamp }}</small>
                </div>
                </div>

                <div class="mt-3">
                    <input v-model="new_messages" @keyup.enter="send_messages" class="form-control" placeholder="Message">
                    <button @click="send_messages" class="btn btn-success mt-2">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const app = Vue.createApp({
    data() {
        return {
            userId: <?= json_encode($user_id) ?>,
            userUsername: <?= json_encode($_SESSION['username'] ?? $_SESSION['email']) ?>, 
            users: <?= json_encode($users) ?>,
            selected_user: null,
            messages: [],
            new_messages: "",
            pollingInterval: null
        };
    },
    methods: {
        selectUser(user) {
            this.selected_user = user;
            this.get_messages();
        },
        get_messages() {
            if (!this.selected_user) return;

            fetch("chat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: "fetch",
                    receiver_id: this.selected_user.id
                })
            })
            .then(response => response.json())
            .then(data => {
                this.messages = data;
                this.scrollToBottom();
            })
            .catch(error => console.error("Error fetching messages:", error));
        },
        send_messages() {
            if (!this.new_messages.trim()) return;

            fetch("chat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: "send",
                    receiver_id: this.selected_user.id,
                    message: this.new_messages
                })
            })
            .then(response => response.json())
            .then(() => {
                this.new_messages = '';  
                this.get_messages(); 
            })
            .catch(error => console.error("Error sending message:", error));
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const messagesBox = this.$refs.messagesBox;
                if (messagesBox) {
                    messagesBox.scrollTop = messagesBox.scrollHeight;
                }
            });
        },
        startPolling() {
            this.pollingInterval = setInterval(this.get_messages, 3000);
        },
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        }
    },
    mounted() {
        this.startPolling();
    },
    beforeUnmount() {
        this.stopPolling();
    }

});
app.mount("#app");
</script>

</body>
</html>

